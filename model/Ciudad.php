<?php
class CiudadException extends Exception{


}

class Ciudad{

    private $_idCiudad;
    private $_nomCiudad;
    private $_idDepto;
    
    public function __construct($idCiudad, $nomCiudad, $idDepto){        
        $this->_idCiudad = $idCiudad;
        $this->_nomCiudad = $nomCiudad;
        $this->_idDepto = $idDepto;        
    }

    public function getIdCiudad(){
        return $this->_idCiudad;
    }

    public function setIdCiudad($idCiudad){
        if($idCiudad !== null && !is_numeric($idCiudad)){
            throw new CiudadException("error en el id de la ciudad");
        }
        $this->_idCiudad = $idCiudad;
    }

    public function getNomCiudad(){
        return $this->_nomCiudad;
    }

    public function setNomCiudad($nomCiudad){
        if($nomCiudad !== null &&  strlen($nomCiudad)>50){
            throw new CiudadException("error en el nombre del departamento");
        }
        $this->_nomCiudad = $nomCiudad;
    }

    public function getIdDepto(){
        return $this->_idDepto;
    }

    public function setIdDepto($idDepto){
        if($idDepto !== null && !is_numeric($idDepto)){
               throw new DepartamentoExcepton("erro en ID de departamento");
        }
        $this->_idDepto = $idDepto;    
    }

    public function returnCiudadAsArray(){
        $ciudad = array();
        $ciudad['idCiudad'] = $this-> getIdCiudad();
        $ciudad['nomCiudad'] = $this-> getNomCiudad();
        $ciudad['idDepto'] = $this-> getIdDepto();
        return $ciudad;
    }
}