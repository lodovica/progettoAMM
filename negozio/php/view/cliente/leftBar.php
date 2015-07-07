<h2>Informazioni</h2>
<p> 
    Benvenuto <?= $user->getNome()." ". $user->getCognome()?>.
</p>
<p>
<?
if(!isset($_SESSION['pagina'])) $_SESSION['pagina'] = 'home.php';
switch ($_SESSION['pagina']) {
    case 'home.php':?>
        <p>
            Seleziona una voce dal menu'.
        </p>
       <?break;
    case 'anagrafica.php':?>
        <p>
            Indirizzo: Nel caso in cui venisse richiesta una spedizione a domicilio questa avverra'  all'indirizzo riportato
            in questa pagina. Nel caso in cui si voglia ricevere la consegna ad un indirizzo differente assicurarsi di modificarlo
            prima di confermare l'ordine.   
        </p>
        <p>
            Password: La password puo' essere modificata in qualsiasi momento.
        </p>
       <?break;    
    case 'ordina.php':?>
        <p>
            Inserire la quantita' di prodotti che si desidera ordinare all'interno degli appositi spazi.
            Sono presenti spazi differenti a seconda delle dimensioni.
            Ricordarsi di confermare l'ordine a seguito del riepilogo, altrimenti non verra' inviato.
        </p>
        <p>
            *Verifica sezione Anagrafica
        </p>        
       <?break;  
    case 'elenco_ordini.php':?>
        <p>
            Cronologia degli ordini effettuati.
        </p>
       <?break;  
    case 'contatti.php':?>
        <p>
            Ci trovi sempre aperti dal lunedi' al sabato dalle <strong>9.30</strong> alle <strong>20.00</strong>. Giorno di chiusura: Domenica
        </p>
        <p>
            Per qualsiasi informazione non eistare a contattarci.
        </p>
       <?break;   
    case 'dettaglio_ordine.php':?>
        <p>
            Dettaglio dei prezzi ed elenco prodotti relativi all'ordine selezionato.
        </p>
       <?break;      
   default:
       break;
}
?>
</p>
