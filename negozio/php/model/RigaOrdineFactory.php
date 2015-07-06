<?php

include_once 'RigaOrdine.php';
include_once 'Prodotto.php';
include_once 'Ordine.php';

class RigaOrdineFactory {

    private static $singleton;

    private function __constructor() {
        
    }

    /**
     * Restiuisce un singleton per creare Modelli
     * @return ModelloFactory
     */
    public static function instance() {
        if (!isset(self::$singleton)) {
            self::$singleton = new RigaOrdineFactory();
        }

        return self::$singleton;
    }
    
    /*
     * La funzione crea un nuovo RigaOrdine
    * @param $idProdotto id del prodotto considerato
    * @param $idOrdine id dell'ordine considerato
    * @param $quantita di prodotti
    * @param $dimensione dei prodotti
    * @return il numero di righe create
    */    
    public function creaRigaOrdine($idProdotto, $idOrdine, $quantita, $dimensione) {
        $query = "INSERT INTO `RigheOrdini`(`prodotto_id`, `ordine_id`, `quantita`, `dimensione`) VALUES (?, ?, ?, ?)";

        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[creaRigaOrdine] impossibile inizializzare il database");
            return 0;
        }

        $stmt = $mysqli->stmt_init();

        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[creaRigaOrdine] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return 0;
        }

        if (!$stmt->bind_param('iiis', $idProdotto, $idOrdine, $quantita, $dimensione)) {
            error_log("[creaRigaOrdine] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return 0;
        }

       if (!$stmt->execute()) {
            error_log("[creaRigaOrdine] impossibile" .
                    " eseguire lo statement");
            $mysqli->close();
            return 0;
        }
        $mysqli->close();
        return $stmt->affected_rows;
    }

    /*
     * La funzione cancella RigaOrdine
    * @param $id id dell'ordine considerato
    * @return il numero di righe cancellate
    */    
    public function cancellaRigaOrdine($id){
        $query = "DELETE FROM RigheOrdini WHERE ordine_id = ?";       
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[cancellaRigaOrdine] impossibile inizializzare il database");
            return false;
        }

        $stmt = $mysqli->stmt_init();

        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[cancellaRigaOrdine] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return false;
        }

        if (!$stmt->bind_param('i', $id)){
        error_log("[cancellaRigaOrdine] impossibile" .
                " effettuare il binding in input");
        $mysqli->close();
        return false;
        }

        if (!$stmt->execute()) {
            error_log("[cancellaRigaOrdine] impossibile" .
                    " eseguire lo statement");
            $mysqli->close();
            return false;
        }

        $mysqli->close();
        return $stmt->affected_rows;        
    }
    

    /*
    * La funzione fornisce il prezzo di un insieme di prodotti dello stesso tipo appartenente allo stesso ordine valutandone la dimensione  
    * @param $RigaOrdine RigaOrdine di riferimento
    * @return il prezzo dell'insieme di prodotti
    */    
    public function getPrezzoPerProdotti(RgaOrdine $RigaOrdine){
        $query = "SELECT
                RigheOrdini.quantita quantita,
                RigheOrdini.dimensione dimensione,
                prodotti.prezzo prodotto_prezzo                
                FROM RigheOrdini
                JOIN Prodotti 
                ON RigheOrdini.prodotto_id = prodotti.id
                WHERE RigheOrdini.id = ?";

        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getPrezzoPerProdotti] impossibile inizializzare il database");
            $mysqli->close();
            return false;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[getPrezzoPerProdotti] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return false;
        }

        if (!$stmt->bind_param('i', $RigaOrdine->getId())) {
            error_log("[getPrezzoPerProdotti] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return false;
        }

        $prezzo = self::caricaPrezzoRigaOrdineDaStmt($stmt);

        $mysqli->close();
        return $prezzo;            
    }

    /*
    * La funzione calcola il prezzo totale dell'ordine senza aggiungere i costi del trasporto a domicilio
    * @param $ordine ordine di riferimento
    * @return prezzo dell'ordine
    */       
    public function getPrezzoParziale(Ordine $ordine){
        
        $query = "SELECT
                RigheOrdini.quantita quantita,
                RigheOrdini.dimensione dimensione,
                prodotti.prezzo prodotto_prezzo
                
                FROM RigheOrdini
                JOIN prodotti ON RigheOrdini.prodotto_id = prodotti.id
                WHERE RigheOrdini.ordine_id = ?";

        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getPrezzoParziale] impossibile inizializzare il database");
            $mysqli->close();
            return true;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[getPrezzoParziale] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return false;
        }

        if (!$stmt->bind_param('i', $ordine->getId())) {
            error_log("[getPrezzoParziale] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return false;
        }

        $prezzo = self::caricaPrezzoRigaOrdineDaStmt($stmt);

        $mysqli->close();
        return $prezzo;        
    }
    
    
        public function &caricaPrezzoRigaOrdineDaStmt(mysqli_stmt $stmt) {
        //40% in piu del prezzo normale se il vasetto e' grande
        $perc = 30/100;    
        if (!$stmt->execute()) {
            error_log("[caricaPrezzoRigaOrdineDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['quantita'],
                $row['dimensione'],
                $row['prodotto_prezzo']);

        if (!$bind) {
            error_log("[caricaPrezzoRigaOrdineDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }
        $prezzo = 0;
        while ($stmt->fetch()) {
            if($row['dimensione'] == "normale") $prezzo += $row['quantita'] * $row['prodotto_prezzo'];
            else $prezzo += $row['quantita'] * ($row['prodotto_prezzo']+($row['prodotto_prezzo']*$perc));
        }

        $stmt->close();
        return $prezzo;
    }         

    /*
    * @param $id id dell'ordine di riferimento
    * @return la quantita'  di prodotti relativi all'ordine di riferimento
    */    
    public function getNrProdotti($id){
        $query = "SELECT RigheOrdini.quantita quantita 
                  FROM RigheOrdini 
                  WHERE RigheOrdini.ordine_id = ?";

        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getNrProdotti] impossibile inizializzare il database");
            $mysqli->close();
            return true;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[getNrProdotti] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return null;
        }

        if (!$stmt->bind_param('i', $id)) {
            error_log("[getNrProdotti] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return null;
        }

       if (!$stmt->execute()) {
            error_log("[getNrProdotti] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result($row['quantita']);

        if (!$bind) {
            error_log("[getNrProdotti] impossibile" .
                    " effettuare il binding in output");
            return null;
        }
        $nrProdotti = 0;
        while ($stmt->fetch()) {
            $nrProdotti += $row['quantita'];
        }

        $mysqli->close();
        return $nrProdotti;                
    }        

    /*
    * @param $ordine ordine di riferimento
    * @return un determinato record in cui l'id dell'ordine e' quello dato come riferimento
    */     
    public function getRigaOrdinePerIdOrdine(Ordine $ordine){
        $rigaOrdine = array();
        $query = "SELECT * FROM RigheOrdini
                  WHERE RigheOrdini.ordine_id = ?";          
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getRigaOrdinePerIdOrdine] impossibile inizializzare il database");
            $mysqli->close();
            return 0;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[getRigaOrdinePerIdOrdine] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return 0;
        }

        if (!$stmt->bind_param('i', $ordine->getId())) {
            error_log("[getRigaOrdinePerIdOrdine] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return 0;
        }        
        
        $rigaOrdine = self::caricaRigaOrdineDaStmt($stmt);

        $mysqli->close();
        return $rigaOrdine;        
    }    
    
    public function &caricaRigaOrdineDaStmt(mysqli_stmt $stmt) {
        $rigaOrdine = array();
        if (!$stmt->execute()) {
            error_log("[caricaRigaOrdineDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['prodottoId'], 
                $row['ordineId'],
                $row['id'],
                $row['quantita'],
                $row['dimensione']);

        if (!$bind) {
            error_log("[caricaRigaOrdineDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }
        while ($stmt->fetch()) {
            $rigaOrdine[] = self::creaRigaOrdineDaArray($row);
        }

        $stmt->close();
        return $rigaOrdine;
    }                
        
    public function creaRigaOrdineDaArray($row) {
        $rigaOrdine = new Prodotto_ordine();
        $rigaOrdine->setProdotto($row['prodottoId']);
        $rigaOrdine->setOrdine($row['ordineId']);        
        $rigaOrdine->setId($row['id']);
        $rigaOrdine->setQuantita($row['quantita']);       
        $rigaOrdine->setDimensione($row['dimensione']);         
        return $rigaOrdine;
    }    
}
?>
