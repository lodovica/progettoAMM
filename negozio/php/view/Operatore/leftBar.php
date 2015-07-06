<h2>Informazioni</h2>
<p> 
     Benvenuto <?= $user->getNome()." ". $user->getCognome()?>.
</p>

<?
if(!isset($_SESSION['pagina'])) $_SESSION['pagina'] = 'home.php';
switch ($_SESSION['pagina']) {
    case 'home.php':?>
        <p>
            Seleziona una voce dal menù.
        </p>
       <?break;
    case 'gestione_ordini.php':?>
        <p>
            Elenco degli ordini richiesti oggi e non ancora pagati. Nel momento in cui verranno contrassegnati come pagati non appariranno
            piu in questa schermata.
        </p>
        <p>
            E' possibile visionare informazioni aggiuntive su ogni ordine non ancora pagato.
        </p>        
       <?break;   
    case 'ricerca_ordini.php':?>
        <p>
            Ricerca tutti gli ordini gestiti tramite il sito scegliendo una particolare data e la relativa fascia oraria.
        </p>
       <?break;
    case 'dettaglio_ordine.php':?>
        <p>
            Dettaglio dei prezzi ed elenco prodotti relativi all'ordine selezionato.
        </p>
       <?break;   
    case 'anagrafica.php':?>
        <p>
            I dati anagrafici presenti in questa pagina sono stati forniti al datore di lavoro e non sono modificabili direttamente dal dipendente.
        </p>        
       <?break;    
   default:
       break;   
}
?>
