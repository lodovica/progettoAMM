<?php

class Prodotto {
    private $id;    
    private $nome;
    private $descrizione;
    private $prezzo;
    
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

    public function getNome() {
        return $this->nome;
    }
    
    public function setNome($nome) {
        $this->nome = $nome;
        return true;
    }

    public function getDescrizione() {
        return $this->descrizione;
    }

    public function setDescrizione($descrizione) {
        $this->descrizione = $descrizione;
        return true;
    }
    
    public function getPrezzo() {
        return $this->prezzo;
    }

    public function setPrezzo($prezzo) {
        $this->prezzo = $prezzo;
    }
}
?>
