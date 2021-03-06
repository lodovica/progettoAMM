<?php

include_once 'Db.php';
include_once 'Prodotto.php';

class ProdottoFactory {
    
    private static $singleton;
    
    private function __constructor() {        
    }

    /**
     * Restiuisce un singleton per creare Modelli
     * @return ProdottoFactory
     */
    public static function instance() {
        if (!isset(self::$singleton)) {
            self::$singleton = new ProdottoFactory();
        }
        return self::$singleton;
    }
    
    /*
    * @return tutti i prodotti esistenti all'interno della tabella prodotti   
    */
    public function &getProdotti() {
        $prodotti = array();
        $query = "SELECT * FROM Prodotti";       
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getProdotti] impossibile inizializzare il database");
            $mysqli->close();
            return 0;           
        }
        
        $result = $mysqli->query($query);
        if ($mysqli->errno > 0) {
            error_log("[getProdotti] impossibile eseguire la query");
            $mysqli->close();
            return 0;         
        }

        while ($row = $result->fetch_array()) {
            $prodotti[] = self::creaProdottoDaArray($row);
        }

        $mysqli->close();
        return $prodotti;
    }    
    
    private function creaProdottoDaArray($row) {
        $prodotto = new Prodotto();
        $prodotto->setId($row['id']);
        $prodotto->setNome($row['nome']);
        $prodotto->setDescrizione($row['descrizione']);
        $prodotto->setPrezzo($row['prezzo']);
        return $prodotto;
    }
    
    /*
    * @param $id id prodotto
    * @return il prodotto al quale corrisponde quel determinato id  
    */
    public function getProdottoPerId($id) {
        $prodotto = array();
        $query = "SELECT * FROM Prodotti WHERE id = ?";
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getProdottoPerId] impossibile inizializzare il database");
            $mysqli->close();
            return 0;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[getProdottoPerId] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return 0;
        }

        if (!$stmt->bind_param('i', $id)) {
            error_log("[getProdottoPerId] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return 0;
        }  
        
        $prodotto = self::creaProdottoDaStmt($stmt);       
        $mysqli->close();
        return $prodotto;        
    }
    
    public function &creaProdottoDaStmt(mysqli_stmt $stmt) {
        $prodotto = array();
        if (!$stmt->execute()) {
            error_log("[creaProdottoDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['id'], 
                $row['nome'],
                $row['descrizione'],
                $row['prezzo']);

        if (!$bind) {
            error_log("[creaProdottoDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }

        while ($stmt->fetch()) {
            $prodotto = self::creaProdottoDaArray($row);
        }
        $stmt->close();
        return $prodotto;
    }     
    /*
    * @return solo gli id di tutti i prodotti presenti all'interno della tabella prodotti
    */
    public function  getIdProdotti() {
        $prodottiId = array();
        $query = "SELECT Prodotti.id id 
                  FROM prodotti";       
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[ getIdProdotti] impossibile inizializzare il database");
            $mysqli->close();
            return 0;        
        }
        
        $result = $mysqli->query($query);
        if ($mysqli->errno > 0) {
            error_log("[getIdProdotti] impossibile eseguire la query");
            $mysqli->close();
            return 0;         
        }
        
        $i = 0;
        while ($row = $result->fetch_array()) {
            $prodottiId[$i] = $row['id'];
            $i++;
        }

        $mysqli->close();
        return $prodottiId;
    }   
}

?>
