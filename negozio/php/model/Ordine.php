<?php

include_once 'Cliente.php';

class Ordine {
    private $id;
    private $spedizione;
    private $prezzo;
    private $stato;
    private $data;
    private $cliente;
    private $operatore;
   
   public function __construct() {        
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $intVal = filter_var($id, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (!isset($intVal)) {
            return false;
        }
        $this->id = $intVal;
    }
    
    public function getSpedizione() {
        return $this->spedizione;
    }
    
    public function setSpedizione($spedizione) {
        $this->spedizione = $spedizione;
        return true;
    }

    public function getPrezzo() {
        return $this->prezzo;
    }
    
    public function setPrezzo($prezzo) {
        $this->prezzo = $prezzo;
        return true;
    }
    
    public function getStato() {
        return $this->stato;
    }

    public function setStato($stato) {
        $this->stato = $stato;
        return true;
    }
    
    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }    
    
    public function getCliente() {
        return $this->cliente;
        return true;
    }

    public function setCliente($cliente_id) {
        $this->cliente = $cliente_id;
        return true;
    }
    
    public function getOperatore() {
        return $this->addettoOrdini;
    }
    
    public function setOperatore($operatore_id) {
        $this->addettoOrdini = $operatore_id;
        return true;
    }
}

?>