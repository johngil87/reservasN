<?php
class DepartamentoException extends Exception{


}

class Departamento{

    private $_idDepto;
    private $_nomDepto;

    //constructor
    public function __construct($idDepto, $nomDepto){
        $this->_idDepto = $idDepto;
        $this->_nomDepto = $nomDepto;
    }

    public function getIdDepto(){
        return $this->_idDepto;
    }

    public function setIdDepto($idDepto){
        if($idDepto !== null && !is_numeric($idDepto)){
               throw new DepartamentoException("error en ID de departamento");
        }
        $this->_idDepto = $idDepto;    
    }

    public function getNomDepto(){
        return $this->_nomDepto;
    }

    public function setNomDepto($nomDepto){
        if($nomDepto !== null && strlen($nomDepto)>50){
               throw new DepartamentoException("error en el nombre del departamento"); 
        }
        $this->_nomDepto = $nomDepto;
    }

    public function returnDepartamentoAsArray(){
        $depto = array();
        $depto['idDepto'] = $this->getIdDepto();
        $depto['nomDepto'] = $this->getNomDepto();
        return $depto;
    }
}