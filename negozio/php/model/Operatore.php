<?php

include_once 'User.php';

/**
 * Classe che rappresenta un Operatore
 *
 * @author Lodovica Marchesi
 */
class Operatore extends User {

    public function __construct() {
        // richiamiamo il costruttore della superclasse
        parent::__construct();
        $this->setRuolo(User::Operatore);
    }

}

?>
