<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <h1>Progetto per il corso di AMM di Lodovica Marchesi</h1>
        
        <h2> Descrizione dell'applicazione</h2>
        <p>
            Il sito in questione è un semplice e-commerce per la vendita di mieli e marmellate sarde.
            Ogni ordine comprende un insieme di prodotti (almeno 1) ed è caratterizzato da alcuni elementi quali:
        </p>
        <ul>
            <li>Numero identificativo univoco dell'ordine</li>
            <li>Data in cui l'ordine è stato prenotato</li>
            <li>Prezzo totale</li>
            <li>Stato dell'ordine (utilizzato per identificare gli ordini pagati o meno)</li>
            <li>Richiesta di spedizione a domicilio</li>       
        </ul> 
        <p> 
            Ogni ordine si distinguerà dagli altri, oltre che per l'identificativo univoco, anche per: 
       </p>
       <ul>
            <li>Il cliente che l'ha richiesto</li>
            <li>L'addetto agli ordini al quale è stata assegnata la gestione</li>
        </p>      
        <p>
            Nel sistema sono presenti due tipi di utenti, che interagiscono in modo differente con l'interfaccia:
        </p>    
        <p><strong>Il cliente</strong></p>
        <p>
            Il cliente ha la possibilità di eseguire 4 operazioni principali:
        </p>
         <ul>
            <li>Visualizzare e modificare i suoi dati, come l'indirizzo e la password</li>
            <li>Visualizzare i prodotti in vendita ed eseguire un nuovo ordine</li>
            <li>Visualizzare la cronologia degli ordini richiesti in passato</li>
            <li>Visualizzare i contatti</li>       
        </ul> 
        <p>
            I dati relativi all'indirizzo dell'utente vengono utilizzati nel caso in cui questo richieda una spedizione a domicilio, 
            l'altra opzione possibile è il ritiro in negozio. 
            Nel caso in cui la spedizione debba avvenire ad un indirizzo diverso da quello gia presente, questo deve essere modificato
            nella sezione "anagrafica".
        </p>
        <p>
            Accedendo alla sezione "Ordina" viene visualizzato l'elenco delle prodotti disponibili e i rispettivi prezzi.
            Dopo avere selezionato la quantità e la dimensione dei prodotti desiderati, vieene visualizzata una
            schermata di riepilogo che comprende l'elenco dei prodotti scelti e il prezzo totale, comprensivo di un 
            eventuale incremento per la spese di spedizione.
            A questo putno il cliente può decidere se confermare o cancellare l'ordine.
        </p>
        <p>
            Le altre sezioni sono autoesplicative.
        </p>
        
        <p><strong>L'operatore</strong></p>
        <p>
            L'operatore e' colui che gestisce il sito del negozio.
            Ha la possibilità di eseguire 2 operazioni principali:
        </p>
         <ul>
            <li>Visualizzare gli ordini della giornata e contrassegnarli come pagati</li>
            <li>Ricercare gli ordini per data</li>      
        </ul> 
         <p>
            Tramite la pagina "Gestione ordini" un operatore può visualizzare gli ordini del giorno che devono ancora essere ritirati/consegati
            e pagati. Nel momento in cui questi vengono segnalati come pagati non vengono piu visualizzati nel suddetto elenco mentre è possibile
            visualizzarli nella pagina "Ricerca ordini". 
            Un ordine pagato registra l'ID dell'operatore che l'ha segnalato come pagato.
            Nella pagina "Ricerca ordini" è possibile ricercare qualsiasi ordine, qualunque sia il suo stato, tramite la scelta della data.
        </p>       
        
        <h2> Requisiti del progetto </h2>
        <ul>
            <li>Utilizzo di HTML e CSS</li>
            <li>Utilizzo di PHP e MySQL</li>
            <li>Utilizzo del pattern MVC </li>
            <li>Due ruoli (cliente e operatore)</li>
            <li>Transazione per il salvataggio/aggiornamento di un nuovo ordine. Visibile all'interno della classe OrdineFactory.php: metodi nuovoOrdine e aggiornaOrdine</li>
            <li>Caricamento ajax dei risultati della ricerca degli ordini da parte dell'addetto agli ordini</li>
        </ul>
        
    <h2>Accesso al progetto</h2>
    <p>
        Il codice clonabile del progetto si trova all'indirizzo <a href="php/login">https://github.com/lodovica/negozio</a>
    </p>
    <p>
        La homepage del progetto si trova all'indirizzo <a href="php/login">http://spano.sc.unica.it/amm2015/marchesiLodovica/php/login</a>
    <p>
    <p>E' possibile eseguire l'accesso al sito utilizzando le seguenti credenziali</p>
    <ul>
        <li>Ruoli Cliente:</li>
        <ul>
            <li>username: lodo</li>
            <li>password: lodo</li>
        </ul>
        <ul>
            <li>username: momo</li>
            <li>password: momo</li>
        </ul>
        <li>Ruolo Operatore:</li>
        <ul>
            <li>username: mario</li>
            <li>password: mario</li>
        </ul>
    </ul>
</body>
</html>
