<?php

include_once 'Ordine.php';
include_once 'User.php';
include_once 'Prodotto.php';
include_once 'RigaOrdine.php';

class OrdineFactory {
	
    private static $singleton;

    private function __constructor() {    
    }

    /**
     * Restiuisce un singleton per creare Modelli
     * @return ModelloFactory
     */
    public static function instance() {
        if (!isset(self::$singleton)) {
            self::$singleton = new OrdineFactory();
        }

        return self::$singleton;
    }

    /*
    * La funzione crea un nuovo ordine attribuendogli esclusivamente un ID. tutti gli altri campi rimangono vuoti
    * @param $id id nuovo ordine
    * @return il numero di righe create
    */    
    public function nuovoOrdine($ordine){
        $query = "INSERT INTO Ordini (`id`) VALUES (?)";   
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[nuovoOrdine] impossibile inizializzare il database");
            return 0;
        }

        $stmt = $mysqli->stmt_init();

        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[nuovoOrdine] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return 0;
        }
        
        if (!$stmt->bind_param('i',
                $ordine->getId())) {
            error_log("[nuovoOrdine] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return 0;
        }
        // inizio la transazione
        
        $mysqli->autocommit(false);
        if (!$stmt->execute()) {
            error_log("[nuovoOrdine] impossibile" .
                    " eseguire lo statement");
            $mysqli->rollback();
            $mysqli->close();
            return 0;
        }
        //query eseguita correttamente, termino la transazione
        $mysqli->commit();
        $mysqli->autocommit(true);

        $mysqli->close();
        return $stmt->affected_rows;       
    }

    
    /*
    * La funzione aggiorna un ordine creato con la funzione precedente aggiornando i dati mancanti
    * @param $user dati dell'utente che sta eseguendo la query
    * @param $ordine dati dell'ordine che si sta per aggiornare
    * @param $spedizione indica se e' stata richiesta o meno la spedizione
    * @return true se l'operazione e' andata a buon fine
    */       
    public function aggiornaOrdine($user, $ordine, $spedizione){
        $query = "UPDATE `Ordini` SET 
            `spedizione`= ?,
            `prezzo`= ?,
            `stato`= ?,
            `data`= ?,            
            `cliente_id`= ?,
            `operatore_id`= ?,
            `orario_id`= ? 
            WHERE id = ?";   
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[aggiornaOrdine] impossibile inizializzare il database");
            return false;
        }

        $stmt = $mysqli->stmt_init();

        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[aggiornaOrdine] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return false;
        }
        $prezzo = OrdineFactory::instance()->getPrezzoTotale($ordine); 
        $data = date('Y-m-d');
        $cliente_id = $user->getId();
        $operatore_id = 1;
        $stato = "non pagato";
        if (!$stmt->bind_param('sdssiiii',
                $spedizione,
                $prezzo,
                $stato,
                $data,
                $cliente_id,
                $operatore_id,
                $orario,
                $ordine->getId())) {
            error_log("[aggiornaOrdine] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return false;
        }
        
       // inizio la transazione  
       $mysqli->autocommit(false);

        if (!$stmt->execute()) {
            error_log("[aggiornaOrdine] impossibile" .
                    " eseguire lo statement");
            $mysqli->close();
            $mysqli->rollback();
            $mysqli->close();
            return false;
        }
        
        //la transazione e' andata a buon fine
        $mysqli->commit();
        $mysqli->autocommit(true);
        $mysqli->close();

        return true;        
    }
 
    /*
    * La funzione calcola il prezzo totale dell'ordine (costo prodotti + costo spedizione)
    * @param $ordine ordine che si sta prendendo in considerazione
    * @return il prezzo totale
    */       
    public function getPrezzoTotale(Ordine $ordine){
        $spedizione = 1.5;
        $prezzoParziale = RigaOrdineFactory::instance()->getPrezzoParziale($ordine);
        if ($ordine->getSpedizione() == "si") return  $prezzoParziale + $spedizione;
        else return $prezzoParziale;
    }       

    /*
    * @param $id id dell'ordine da cancellare
    * @return numero di righe cancellate
    */           
    public function cancellaOrdine($id){
        $query = "DELETE FROM Ordini WHERE id = ?";
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[cancellaOrdine] impossibile inizializzare il database");
            return false;
        }

        $stmt = $mysqli->stmt_init();

        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[cancellaOrdine] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return false;
        }

        if (!$stmt->bind_param('i', $id)){
        error_log("[cancellaOrdine] impossibile" .
                " effettuare il binding in input");
        $mysqli->close();
        return false;
        }

        if (!$stmt->execute()) {
            error_log("[cancellaOrdine] impossibile" .
                    " eseguire lo statement");
            $mysqli->close();
            return false;
        }

        $mysqli->close();
        return $stmt->affected_rows;        
    }

    /*
    * @return ultimo id ordine inserito nella tabella ordini +1
    */      
    public function getLastId(){
        $query = "SELECT Ordini.id ordine_id FROM Ordini ORDER BY Id DESC LIMIT 1";

        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getLastId] impossibile inizializzare il database");
            $mysqli->close();
        }
        
        $result = $mysqli->query($query);
        $res = $result->fetch_row();

        $mysqli->close();
        return $res[0]+1;         
        
    }   
    
    /*
    * @param $id id dell'ordine di riferimento
    * @return l'ordine al quale faceva riferimento l'id
    */     
    public function getOrdine($id){

        $query = "SELECT * FROM Ordini WHERE id = ?";
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getOrdine] impossibile inizializzare il database");
            $mysqli->close();
            return false;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[getOrdine] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return false;
        }

        if (!$stmt->bind_param('i', $id)) {
            error_log("[getOrdine] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return false;
        }        
        $ordine = self::caricaOrdineDaStmt($stmt);

        $mysqli->close();
        return $ordine;                
    }
 
// per un singolo risultato
    public function &caricaOrdineDaStmt(mysqli_stmt $stmt) {
        $ordine = array();
        if (!$stmt->execute()) {
            error_log("[caricaOrdiniDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['ordine_id'], 
                $row['ordine_spedizione'],
                $row['ordine_prezzo'],
                $row['ordine_stato'], 
                $row['ordine_data'],                
                $row['cliente_id'], 
                $row['operatore_id']);

        if (!$bind) {
            error_log("[caricaOrdiniDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }
        while ($stmt->fetch()) {
            $ordine = self::creaOrdineDaArray($row);
        }
        $stmt->close();
        return $ordine;
    }
    
    //per un array di risultati
    public function &caricaOrdiniDaStmt(mysqli_stmt $stmt) {
        $ordini = array();
        if (!$stmt->execute()) {
            error_log("[caricaOrdiniDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['ordine_id'], 
                $row['ordine_spedizione'],
                $row['ordine_prezzo'],
                $row['ordine_stato'],
                $row['ordine_data'],                
                $row['cliente_id'], 
                $row['operatore_id']);

        if (!$bind) {
            error_log("[caricaOrdiniDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }
        while ($stmt->fetch()) {
            $ordini[] = self::creaOrdineDaArray($row);
        }
        $stmt->close();
        return $ordini;
    }                
              
    
    public function creaOrdineDaArray($row) {
        $ordine = new Ordine();
        $ordine->setId($row['ordine_id']);
        $ordine->setDomicilio($row['ordine_spedizione']);        
        $ordine->setPrezzo($row['ordine_prezzo']);
        $ordine->setStato($row['ordine_stato']);
        $ordine->setData($row['ordine_data']);        
        $ordine->setCliente($row['cliente_id']);
        $ordine->setOperatore($row['operatore_id']); 
        return $ordine;
    }
    
       /*
       * @param $cliente il cliente di riferimento
       * @return tutti gli ordini fatti dal cliente di riferimento
       */    
        public function getOrdiniPerIdCliente($cliente){
        $ordini = array();
        $query = "SELECT * FROM Ordini WHERE Ordini.cliente_id = ? ";
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getOrdiniPerIdCliente] impossibile inizializzare il database");
            $mysqli->close();
            return false;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[getOrdiniPerIdCliente] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return false;
        }

        if (!$stmt->bind_param('i', $cliente->getId())) {
            error_log("[getOrdiniPerIdCliente] impossibile" .
                    " effettuare il binding in input".$cliente->getId());
            $mysqli->close();
            return false;
        }

        $ordini = self::caricaOrdiniDaStmt($stmt);
        $mysqli->close();
        return $ordini;
    }
 
    /*
    * @return tutti gli ordini che non sono ancora stati pagati
    */     
    public function getOrdiniNonPagati(){
        $ordini = array();
        $query = "SELECT * FROM Ordini WHERE Ordini.stato = ? AND Ordini.data LIKE ?";  
        
        $stato = "non pagato";
        $data = date('Y\-m\-d').'%';
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getOrdiniNonPagati] impossibile inizializzare il database");
            $mysqli->close();
            return false;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[getOrdiniNonPagati] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return false;
        }

        if (!$stmt->bind_param('ss', $stato, $data)) {
            error_log("[getOrdiniNonPagati] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return false;
        } 
        
        $ordini = self::caricaOrdiniDaStmt($stmt);
        $mysqli->close();
        return $ordini;             
    }
 
       /*
       *La funzione permette di impostare nello stato "pagato" un ordine che era in stato "da pagare" 
       * @param $ordineId id dell'ordine di riferimento
       * @param $user utente di riferimento
       * @return il numero di record modificati
       */     
    public function setPagato($ordineId, $user){
        $query = "UPDATE `Ordini` SET 
            `stato`= ?,
            `operatore_id`= ?
            WHERE id = ?";   
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[setPagato] impossibile inizializzare il database");
            return 0;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[setPagato] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return 0;
        }

        $stato = "pagato";       
        if (!$stmt->bind_param('sii', $stato, $user->getId(), $ordineId)) {
            error_log("[setPagato] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return 0;
        }

        if (!$stmt->execute()) {
            error_log("[setPagato] impossibile" .
                    " eseguire lo statement");
            $mysqli->close();
            return 0;
        }

        $mysqli->close();
        return $stmt->affected_rows;                
    }

       /* 
       * @return tutte le date in ordine decrescente in cui sono stati effettuati degli ordini
       */        
    public function getDate(){
        $date = array();
        $query = "SELECT DISTINCT `data` FROM  `Ordini` ORDER BY data DESC";
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getDate] impossibile inizializzare il database");
            $mysqli->close();
           
        }
        $result = $mysqli->query($query);
        if ($mysqli->errno > 0) {
            error_log("[getDate] impossibile eseguire la query");
            $mysqli->close();       
        }
        while ($row = $result->fetch_array()) {
            $date[] = $row['data'];
        }

        $mysqli->close();
        return $date;        
    } 
  
    public function getOrdiniPerData($data){
        $ordini = array();
        $query = "SELECT * FROM Ordini WHERE Ordini.data = ?"; 
      
        $newData = substr($data,0,4)."\\".substr($data,4,3)."\\".substr($data,7,3).'%';
        //la forma della data per la query risulta per esempio 2014\-08\-19% per adattarsi all'sql
        
       $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getOrdiniPerData] impossibile inizializzare il database");
            $mysqli->close();
            return 0;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[getOrdiniPerData] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return 0;
        }

        if (!$stmt->bind_param('si', $newData)) {
            error_log("[ricercaPerDataOra] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return 0;
        } 
        
        $ordini = self::caricaOrdiniDaStmt($stmt);

        $mysqli->close();
        return $ordini;                        
    }
}

?>
