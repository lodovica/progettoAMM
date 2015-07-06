<?php

include_once 'BaseController.php';
include_once basename(__DIR__) . '/../model/RigaOrdineFactory.php';
include_once basename(__DIR__) . '/../model/OrarioFactory.php';
include_once basename(__DIR__) . '/../model/ProdottoFactory.php';
include_once basename(__DIR__) . '/../model/OrdineFactory.php';

/**
 * Controller che gestisce la modifica dei dati dell'applicazione relativa ai 
 * Clienti da parte di utenti con ruolo Cliente 
 */
class ClienteController extends BaseController {

    
    /**
     * Costruttore
     */
    public function __construct() {
        parent::__construct();
    }
    

    /**
     * Metodo per gestire l'input dell'utente. 
     * @param type $request la richiesta da gestire
     */
    public function handleInput(&$request) {

        // creo il descrittore della vista
        $vd = new ViewDescriptor();


        // imposto la pagina
        $vd->setPagina($request['page']);

        // gestion dei comandi
        // tutte le variabili che vengono create senza essere utilizzate 
        // direttamente in questo switch, sono quelle che vengono poi lette
        // dalla vista, ed utilizzano le classi del modello

        if (!$this->loggedIn()) {
            // utente non autenticato, rimando alla home

            $this->showLoginPage($vd);
        } 
        else {
            // utente autenticato
            $user = UserFactory::instance()->cercaUtentePerId(
                            $_SESSION[BaseController::user], $_SESSION[BaseController::role]);


            // verifico quale sia la sottopagina della categoria ed imposto il 
            // descrittore della vista per caricare i "pezzi" delle pagine corretti
            // tutte le variabili che vengono create senza essere utilizzate 
            // direttamente in questo switch, sono quelle che vengono poi lette
            // dalla vista, ed utilizzano le classi del modello
            if (isset($request["subpage"])) {
                switch ($request["subpage"]) {
                    //ad ogni pagina viene associato un valore nella variabile $_SESSION['pagina'] per fare in modo che nel menu
                    //a sinistra appaiano informazioni guida differenti a seconda della pagina che si sta visualizzando
                    
                    // modifica dei dati anagrafici per le consegne a domicilio
                    case 'anagrafica':
                        $_SESSION['pagina'] = 'anagrafica.php';   
                        $vd->setSottoPagina('anagrafica');
                        break;

                    // Ordinazione delle prodotti con scelta dei quantita'  e dimensioni
                    case 'ordina':                        
                        $_SESSION['pagina'] = 'ordina.php';
                        $prodotti = ProdottoFactory::instance()->getProdotti();
                        $orari = OrarioFactory::instance()->getOrari();
                        $vd->setSottoPagina('ordina');
                        break;

                    // visualizzazione degli ordini effettuati precedentemente
                    case 'elenco_ordini':
                        $_SESSION['pagina'] = 'elenco_ordini.php'; 
                        $ordini = OrdineFactory::instance()->getOrdiniPerIdCliente($user);
                        $vd->setSottoPagina('elenco_ordini');
                        break;                    

                    // visualizzaza come raggiungere i vari contatti del negozio
                    case 'contatti':
                        $_SESSION['pagina'] = 'contatti.php';  
                        $vd->setSottoPagina('contatti');
                        break;
                    
                    default:
                        $_SESSION['pagina'] = 'home.php';    
                        $vd->setSottoPagina('home');
                        break;
                }
            }

            // gestione dei comandi inviati dall'utente
            if (isset($request["cmd"])) {
                // abbiamo ricevuto un comando
                switch ($request["cmd"]) {

                    // logout
                    case 'logout':
                        $this->logout($vd);
                        break;
                        
                    case 'procedi_ordine':
                        //si verifica che i dati inseriti dall'utente relativamente a quantita'  e dimensione prodotti siano nel formato
                        //corretto e in numero accettabile. Successivamente ad ogni tipologia di prodotto viene associato un 
                        //nuovo record nella tabella RigheOrdini in cui viene indicato, oltre al numero ordine relativo, anche
                        //la quantita'  di prodotti di quel determinato tipo che si vogliono ordinare.
                        $vd->setSottoPagina('conferma_ordine');
                        $msg = array();
                        //carico un array con tutti gli id dei prodotti ordinabili
                        $idProdotti = ProdottoFactory::instance()-> getIdProdotti();
                        
                        //verifico se i valori inseriti dall'utente sono corretti e conto quanti prodotti sono stati ordinati in totale
                        $nrProdotti = $this->validaForm($idProdotti, $request);
                        
                        //creo un nuovo ordine attualmente formato solo dall'id (che e' anche l'ulitmo disponibile)
                        $ordine = new Ordine();
                        $ordine->setId(OrdineFactory::instance()->getLastId());
                        $ordineId = $ordine->getId();
                        
                        //se il numero di prodotti ordinate e' accettabile (>0) per ogni tipologia di prodotto creo un nuovo record su RigheOrdini
                        //indicando dimensione e quantita' . aggiorno l'ordine creato in precedenza con tutto il riepilogo dei dati
                        if($$nrProdotti){ 
                            OrdineFactory::instance()->nuovoOrdine($ordine);                           

                            foreach($idProdotti as $idProdotto){
                                $quantita = filter_var($request[$idProdotto.'medio'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                                if (isset($quantita)){
                                   RigaOrdineFactory::instance()->creaRigaOrdine($idProdotto, $ordineId, $quantita, "medio");}
                                $quantita = filter_var($request[$idProdotto.'grande'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);    
                                if (isset($quantita)){
                                   RigaOrdineFactory::instance()->creaRigaOrdine($idProdotto, $ordineId, $quantita, "grande");}
                            }
                            OrdineFactory::instance()->aggiornaOrdine($user, $ordine, $request['spedizione']);                     
                        } 
                        //altrimenti indico che non e' possibile ordinare quel quantitativo di prodotti 
                        //e cancello tutti i dati creati fino a questo momento
                        else {
                            RigaOrdineFactory::instance()->cancellaRigaOrdine($ordineId);
                            OrdineFactory::instance()->cancellaOrdine($ordineId);                           
                            $msg[]= '<li>Non e\' possibile ordinare questo quantitativo di prodotti';
                            $vd->setSottoPagina('ordina');                            
                        }
                        $this->creaFeedbackUtente($msg, $vd, "");
                        $this->showHomeUtente($vd);
                        break;
                   
                        
                    case 'dettaglio':
                        //mi permette di vedere i dettagli relativi a un ordine : elenco prodotti, quantita' , prezzi singoli e totali
                        //e richieste di spedizioni a domicilio
                        $_SESSION['pagina'] = 'dettaglio_ordine.php'; 
                        $ordineId = filter_var($request['ordine'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        $ordine = OrdineFactory::instance()->getOrdine($ordineId);
                        $RigaOrdines = RigaOrdineFactory::instance()->getRigaOrdinePerIdOrdine($ordine);
                        $vd->setSottoPagina('dettaglio_ordine');
                        $this->showHomeUtente($vd);
                        break; 
                    
                    case 'conferma_ordine':
                        //dopo il riepilogo dell'ordine il ciente puo' decidere se confermarlo 
                        $msg = array();
                        $ordineId = $request['ordineId'];                        
                        $this->creaFeedbackUtente($msg, $vd, "Ordine ".$ordineId." creato con successo.");
                        $vd->setSottoPagina('home');
                        $this->showHomeUtente($vd);                        
                        break;
                    
                    case 'cancella_ordine':
                        //oppure cancellarlo, in questo caso si cancella RigaOrdine e ordine
                        $msg = array();
                        $ordineId = $request['ordineId'];
                        $p = RigaOrdineFactory::instance()->cancellaRigaOrdine($ordineId);
                        $o = OrdineFactory::instance()->cancellaOrdine($ordineId);
                        if ($p && $o) {
                            $this->creaFeedbackUtente($msg, $vd, "Ordine ".$ordineId." cancellato.");
                        }else $this->creaFeedbackUtente('<li>Errore cancellazione</li>', $vd, "");
                        $vd->setSottoPagina('home');
                        $this->showHomeUtente($vd);
                        break;
                        
                    
                    case 'indirizzo':
                        //aggiornamento indirizzo - l'indirizzo viene utilizzato per le consegne a domicilio
                        $msg = array();
                        $this->aggiornaIndirizzo($user, $request, $msg);
                        $this->creaFeedbackUtente($msg, $vd, "Indirizzo aggiornato");
                        $this->showHomeCliente($vd);
                        break;
                    
                    case 'password':
                        // cambio password
                        $msg = array();
                        $this->aggiornaPassword($user, $request, $msg);
                        $this->creaFeedbackUtente($msg, $vd, "Password aggiornata");
                        $this->showHomeCliente($vd);
                        break;

                    default : $this->showHomeUtente($vd);
                }
            }
            else {
                // nessun comando
                $user = UserFactory::instance()->cercaUtentePerId($_SESSION[BaseController::user], $_SESSION[BaseController::role]);
                $this->showHomeUtente($vd);
            }          
        }
        // includo la vista
        require basename(__DIR__) . '/../view/master.php';        
    }
 
    //funzione che permette di verificare se i valori inseriti nei campi delle quantita' dei prodotti da ordinare sono validi
    //e conta quanti prodotti sono stati richiesti in totale
    private function validaForm($idProdotti , $request) {
         $valide = 0;
         foreach($idProdotti as $idProdotto){
            $quantitaM = filter_var($request[$idProdotto.'medio'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            if (isset($quantitaM) && ($quantitaM != 0)) $valide+=$quantitaM;
            $quantitaG = filter_var($request[$idProdotto.'grande'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            if (isset($quantitaG) && ($quantitaG != 0)) $valide+=$quantitaG;   
         }        
         return $valide;
    }

}

?>
