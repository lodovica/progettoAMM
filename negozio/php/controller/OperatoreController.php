<?php

include_once 'BaseController.php';
include_once basename(__DIR__) . '/../model/RigaOrdineFactory.php';
include_once basename(__DIR__) . '/../model/ProdottoFactory.php';
include_once basename(__DIR__) . '/../model/OrdineFactory.php';

/**
 * Controller che gestisce la modifica dei dati dell'applicazione relativa agli 
 * addetti agli ordini
 */
class OperatoreController extends BaseController {


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

        if (!$this->loggedIn()) {
            // utente non autenticato, rimando alla home
            $this->showLoginPage($vd);
        } else {
            // utente autenticato
            $user = UserFactory::instance()->cercaUtentePerId(
                    $_SESSION[BaseController::user], $_SESSION[BaseController::role]);

            // verifico quale sia la sottopagina della categoria
            // Docente da servire ed imposto il descrittore 
            // della vista per caricare i "pezzi" delle pagine corretti
            // tutte le variabili che vengono create senza essere utilizzate 
            // direttamente in questo switch, sono quelle che vengono poi lette
            // dalla vista, ed utilizzano le classi del modello
            if (isset($request["subpage"])) {
                switch ($request["subpage"]) {

                    
                    // visualizza i dati anagrafici
                    case 'anagrafica':
                        $_SESSION['pagina'] = 'anagrafica.php';   
                        $vd->setSottoPagina('anagrafica');
                        break;
                    
                    // gestione degli ordini eseguiti oggi
                    case 'gestione_ordini':
                        $_SESSION['pagina'] = 'gestione_ordini.php';
                        $ordini = OrdineFactory::instance()->getOrdiniNonPagati();
                        $vd->setSottoPagina('gestione_ordini');
                        break;
                    
                    // ricerca di tutti gli ordini che sono stati eseguiti tramite il sito 
                    case 'ricerca_ordini':
                        $_SESSION['pagina'] = 'ricerca_ordini.php';
                        $orari = OrarioFactory::instance()->getOrari();
                        $date = OrdineFactory::instance()->getDate();
                        $vd->setSottoPagina('ricerca_ordini');
                   
                        $vd->addScript("../js/jquery-2.1.1.min.js");
                        $vd->addScript("../js/ricercaOrdini.js");
                        break;                    
                    
                    // utilizzo la funzione js e il json per ricercare e stampare i risultati della ricerca_ordini
                    case 'filtra_ordini':
                        $vd->toggleJson();
                        $vd->setSottoPagina('ricerca_ordini_json');
                        
                        $errori = array();

                        if (isset($request['myData']) && ($request['myData'] != '')) {
                            $data = $request['myData'];
                        } else {
                            $data = null;
                        }
                        
                        $ordini = OrdineFactory::instance()->getOrdiniPerData($data);
                        //i dati si vedono nel js ma non nel controller ne nel json                     
                        break;

                    default:
                        $_SESSION['pagina'] = 'home.php';
                        $vd->setSottoPagina('home');
                        break;
                }
            }


            // gestione dei comandi inviati dall'utente
            if (isset($request["cmd"])) {

                switch ($request["cmd"]) {

                    // logout
                    case 'logout':
                        $this->logout($vd);
                        break;
                                      
                    case 'dettaglio':
                        //mi permette di vedere i dettagli relativi a un ordine : elenco prodotti, quantita'�, prezzi singoli e totali
                        //e richieste di spedizione a domicilio                          
                        $_SESSION['pagina'] = 'dettaglio_ordine.php';                            
                        $ordineId = filter_var($request['ordine'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        $ordine = OrdineFactory::instance()->getOrdine($ordineId);
                        $RigaOrdine = RigaOrdineFactory::instance()->getRigaOrdinePerIdOrdine($ordine);
                        $cliente = UserFactory::instance()->getClientePerId($ordine->getCliente());
                        $vd->setSottoPagina('dettaglio_ordine');
                        $this->showHomeUtente($vd);
                        break; 
                    
                    case 'paga':
                        //permette al dipendente di segnalare un ordine come pagato e quindi non farlo piu apparire
                        //nell'elenco degli ordini da gestire
                        $msg = array();
                        $ordineId = filter_var($request['ordine'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                        if (OrdineFactory::instance()->setPagato($ordineId, $user)) {
                            $this->creaFeedbackUtente($msg, $vd, "Ordine ".$ordineId." pagato.");
                        }else $this->creaFeedbackUtente($msg, $vd, "Errore cancellazione"); 
                        
                        $vd->setSottoPagina('gestione_ordini');
                        $ordini = OrdineFactory::instance()->getOrdiniNonPagati();
                        $this->showHomeUtente($vd);                        
                        break;

                    // default
                    default:
                        $this->showHomeUtente($vd);
                        break;
                }
            } else {
                // nessun comando, dobbiamo semplicemente visualizzare la vista
                $user = UserFactory::instance()->cercaUtentePerId(
                        $_SESSION[BaseController::user], $_SESSION[BaseController::role]);
                $this->showHomeUtente($vd);
            }
        }

        // richiamo la vista
        require basename(__DIR__) . '/../view/master.php';
    }


}

?>
