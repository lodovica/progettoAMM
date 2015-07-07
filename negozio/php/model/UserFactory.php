<?php

//include_once '../Db.php';
include_once 'User.php';
include_once 'Operatore.php';
include_once 'Cliente.php';

/**
 * Classe per la creazione degli utenti del sistema
 *
 * @author Lodovica Marchesi
 */
class UserFactory {

    private static $singleton;

    private function __constructor() {     
    }

    /**
     * Restiuisce un singleton per creare utenti
     * @return \UserFactory
     */
    public static function instance() {
        if (!isset(self::$singleton)) {
            self::$singleton = new UserFactory();
        }
        return self::$singleton;
    }

    /**
     * Carica un utente tramite username e password
     * @param string $username
     * @param string $password
     * @return \User|\Operatore|\Cliente
     */
    public function caricaUtente($username, $password) {    
    	$mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[caricaUtente] impossibile inizializzare il database");
            $mysqli->close();
            return null;
        }

        // cerco prima nella tabella clienti
        $query = "SELECT * FROM Clienti WHERE  username =  ? AND  password =  ?";
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[caricaUtente] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return null;
        }

        if (!$stmt->bind_param('ss', $username, $password)) {
            error_log("[caricaUtente] impossibile" .
                    " effettuare il binding in input ");
            $mysqli->close();
            return null;
        }

        $utente = self::caricaClienteDaStmt($stmt);
        if (isset($utente)) {
            // ho trovato un cliente
            $mysqli->close();
            return $utente;
        }

        // ora cerco un operatore
        $query = "SELECT * FROM Operatori WHERE username = ? and password = ?";

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[caricaUtente] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return null;
        }

        if (!$stmt->bind_param('ss', $username, $password)) {
            error_log("[caricaUtente] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return null;
        }

        $utente = self::caricaOperatoreDaStmt($stmt);
        if (isset($utente)) {
            // ho trovato un operatore
            $mysqli->close();
            return $utente;
        }
    }

    
    private function caricaClienteDaStmt(mysqli_stmt $stmt) {

        if (!$stmt->execute()) {
            error_log("[caricaClienteDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['cliente_id'], 
                $row['cliente_username'], 
                $row['cliente_password'],                
                $row['cliente_nome'], 
                $row['cliente_cognome'], 
                $row['cliente_via'],
                $row['cliente_civico'],
                $row['cliente_cap'],
                $row['cliente_citta'],
                $row['cliente_telefono']);
        
        if (!$bind) {
            error_log("[caricaClienteDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }

        if (!$stmt->fetch()) {
            return null;
        }

        $stmt->close();
        return self::creaClienteDaArray($row);
    }
    
    /**
     * Restituisce un array con gli utenti presenti nel sistema
     * @return array
     */
    public function &getListaClienti() {
        $clienti = array();
        $query = "SELECT * FROM Clienti";
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getListaClienti] impossibile inizializzare il database");
            $mysqli->close();
            return $clienti;
        }
        $result = $mysqli->query($query);
        if ($mysqli->errno > 0) {
            error_log("[getListaClienti] impossibile eseguire la query");
            $mysqli->close();
            return $clienti;
        }

        while ($row = $result->fetch_array()) {
            $clienti[] = self::creaClienteDaArray($row);
        }
       
        $mysqli->close();
        return $clienti;
    }

    /**
     * Crea un cliente da una riga del db
     * @param type $row
     * @return \Cliente
     */
    public function creaClienteDaArray($row) {
        $cliente = new Cliente();
        $cliente->setId($row['cliente_id']); 
        $cliente->setUsername($row['cliente_username']);
        $cliente->setPassword($row['cliente_password']);        
        $cliente->setNome($row['cliente_nome']);    
        $cliente->setCognome($row['cliente_cognome']);
        $cliente->setVia($row['cliente_via']);
        $cliente->setCivico($row['cliente_civico']);
        $cliente->setCitta($row['cliente_citta']);                  
        $cliente->setCap($row['cliente_cap']);
        $cliente->setTelefono($row['cliente_telefono']);        
        $cliente->setRuolo(User::Cliente);
        return $cliente;
    }
    
    /**
     * Restituisce la lista degli operatori presenti nel sistema
     * @return array
     */
    public function &getListaOperatore() {
        $operatore = array();
        $query = "SELECT * FROM Operatori";
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getListaOperatore] impossibile inizializzare il database");
            $mysqli->close();
            return $operatore;
        }
        $result = $mysqli->query($query);
        if ($mysqli->errno > 0) {
            error_log("[getListaOperatore] impossibile eseguire la query");
            $mysqli->close();
            return $operatore;
        }

        while ($row = $result->fetch_array()) {
            $operatore[] = self::creaOperatoreDaArray($row);
        }

        return $operatore;
    }




    /**
     * Crea un operatore da una riga del db
     * @param type $row
     * @return \Operatore
     */
    public function creaOperatoreDaArray($row) {
        $operatore = new Operatore();
        $operatore->setId($row['operatore_id']);
        $operatore->setNome($row['operatore_nome']);
        $operatore->setCognome($row['operatore_cognome']);
        $operatore->setVia($row['operatore_via']);
        $operatore->setCivico($row['operatore_civico']);
        $operatore->setCitta($row['operatore_citta']);                  
        $operatore->setCap($row['operatore_cap']);
        $operatore->setTelefono($row['operatore_telefono']);
        $operatore->setRuolo(User::Operatore);
        $operatore->setUsername($row['operatore_username']);
        $operatore->setPassword($row['operatore_password']);
        return $operatore;
    }

    /**
     * Salva i dati relativi ad un utente sul db
     * @param User $user
     * @return il numero di righe modificate
     */
    public function salva(User $user) {
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[salva] impossibile inizializzare il database");
            $mysqli->close();
            return 0;
        }

        $stmt = $mysqli->stmt_init();
        $count = 0;
        switch ($user->getRuolo()) {
            case User::Cliente:
                $count = $this->salvaCliente($user, $stmt);
                break;
            case User::Operatore:
                $count = $this->salvaOperatore($user, $stmt);
        }

        $stmt->close();
        $mysqli->close();
        return $count;
    }

    /**
     * Rende persistenti le modifiche all'anagrafica di un cliente sul db
     * @param Cliente $s il cliente considerato
     * @param mysqli_stmt $stmt un prepared statement
     * @return int il numero di righe modificate
     */
    private function salvaCliente(Cliente $c, mysqli_stmt $stmt) {
        $query = " UPDATE Clienti SET 
                    password = ?,
                    nome = ?,
                    cognome = ?,
                    via = ?,
                    civico = ?,
                    citta = ?,
                    cap = ?,
                    telefono = ?
                    WHERE Clienti.id = ?";
        
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[salvaCliente] impossibile" .
                    " inizializzare il prepared statement");
            return 0;
        }

        if (!$stmt->bind_param('ssssissii',
                $c->getPassword(),
                $c->getNome(),
                $c->getCognome(),
                $c->getVia(), 
                $c->getCivico(),
                $c->getCitta(),
                $c->getCap(),
                $c->getTelefono(),
                $c->getId())) {
            error_log("[salvaCliente] impossibile" .
                    " effettuare il binding in input 2");
            return 0;
        }

        if (!$stmt->execute()) {
            error_log("[salvaCliente] impossibile" .
                    " eseguire lo statement");
            return 0;
        }

        return $stmt->affected_rows;
    }
    
    /**
     * Rende persistenti le modifiche all'anagrafica di un operatore sul db
     * @param Operatore $d l'operatore considerato
     * @param mysqli_stmt $stmt un prepared statement
     * @return int il numero di righe modificate
     */
    private function salvaOperatore(Operatore $d, mysqli_stmt $stmt) {
        $query = " UPDATE Operatori SET 
                    password = ?,
                    nome = ?,
                    cognome = ?,
                    via = ?,
                    civico = ?,
                    citta = ?,
                    cap = ?,
                    telefono = ?,
                    WHERE Operatori.id = ?
                    ";
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[salvaOperatore] impossibile" .
                    " inizializzare il prepared statement");
            return 0;
        }

        if (!$stmt->bind_param('ssssissii', 
                $d->getPassword(), 
                $d->getNome(), 
                $d->getCognome(), 
                $d->getVia(), 
                $d->getCivico(),
                $d->getCitta(),
                $d->getCap(),
                $d->getTelefono(),
                $d->getId())) {
            error_log("[salvaOperatore] impossibile" .
                    " effettuare il binding in input");
            return 0;
        }

        if (!$stmt->execute()) {
            error_log("[salvaOperatore] impossibile" .
                    " eseguire lo statement");
            return 0;
        }

        return $stmt->affected_rows;
    }

    /**
     * Carica un operatore eseguendo un prepared statement
     * @param mysqli_stmt $stmt
     * @return null
     */
    private function caricaOperatoreDaStmt(mysqli_stmt $stmt) {

        if (!$stmt->execute()) {
            error_log("[caricaOperatoreDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['operatore_id'], 
                $row['operatore_username'], 
                $row['operatore_password'],                
                $row['operatore_nome'], 
                $row['operatore_cognome'], 
                $row['operatore_via'],
                $row['operatore_civico'],
                $row['operatore_cap'],
                $row['operatore_citta'],
                $row['operatore_telefono']);
        if (!$bind) {
            error_log("[caricaOperatoreDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }

        if (!$stmt->fetch()) {
            return null;
        }

        $stmt->close();
        return self::creaOperatoreDaArray($row);
    }
    
    /**
     * Cerca un utente per id
     * @param int $id
     * @return  un oggetto Cliente|Operatore nel caso sia stato trovato,
     * 			NULL altrimenti
     */
    public function cercaUtentePerId($id, $role) {
        $intval = filter_var($id, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (!isset($intval)) {
            return null;
        }
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[cercaUtentePerId] impossibile inizializzare il database");
            $mysqli->close();
            return null;
        }

        switch ($role) {
            case User::Cliente:
                $query = "SELECT  * FROM Clienti WHERE id = ?";
                $stmt = $mysqli->stmt_init();
                $stmt->prepare($query);
                if (!$stmt) {
                    error_log("[cercaUtentePerId] impossibile" .
                            " inizializzare il prepared statement");
                    $mysqli->close();
                    return null;
                }

                if (!$stmt->bind_param('i', $intval)) {
                    error_log("[cercaUtentePerId] impossibile" .
                            " effettuare il binding in input");
                    $mysqli->close();
                    return null;
                }

                return self::caricaClienteDaStmt($stmt);
                break;

            case User::Operatore:
                $query = "SELECT * FROM Operatori WHERE id = ?";

                $stmt = $mysqli->stmt_init();
                $stmt->prepare($query);
                if (!$stmt) {
                    error_log("[cercaUtentePerId] impossibile" .
                            " inizializzare il prepared statement");
                    $mysqli->close();
                    return null;
                }

                if (!$stmt->bind_param('i', $intval)) {
                    error_log("[cercaUtentePerId] impossibile" .
                            " effettuare il binding in input");
                    $mysqli->close();
                    return null;
                }

                $toRet =  self::caricaOperatoreDaStmt($stmt);
                $mysqli->close();
                return $toRet;
                break;

            default: return null;
        }       
    }
    
    /*
    * @param $id id del cliente da ricercare
    * @return dati del cliente corrispondenti all'id considerato
    */    
    public function getClientePerId($id) {
       $cliente = array();
        $query = "SELECT * FROM Clienti WHERE Clienti.id = ? ";          
        
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getClientePerId] impossibile inizializzare il database");
            $mysqli->close();
            return $cliente;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[getClientePerId] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return $cliente;
        }

        if (!$stmt->bind_param('i', $id)) {
            error_log("[getClientePerId] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return $cliente;
        } 
        
        $cliente = self::caricaClienteDaStmt($stmt);

        $mysqli->close();
        return $cliente;        
    }
}

?>