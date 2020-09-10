<?php
class AreasException extends Exception{


}

class Areas{

    private $_idArea;
    private $_nomArea;
    private $_idSucursal;
    
    public function __construct($idArea, $nomArea, $idSucursal){        
        $this->_idArea = $idArea;
        $this->_nomArea = $nomArea;
        $this->_idSucursal = $idSucursal;        
    }

    public function getIdArea(){
        return $this->_idArea;
    }

    public function setIdArea($idArea){
        if($idArea !== null && !is_numeric($idArea)){
            throw new CiudadException("error en el id del area");
        }
        $this->_idArea = $idArea;
    }

    public function getNomArea(){
        return $this->_nomArea;
    }

    public function setNomArea($nomArea){
        if($nomArea !== null &&  strlen($nomArea)>50){
            throw new CiudadException("error en el nombre del area");
        }
        $this->_nomArea = $nomArea;
    }

    public function getIdSucursal(){
        return $this->_idSucursal;
    }

    public function setIdDepto($idSucursal){
        if($idSucursal !== null && !is_numeric($idSucursal)){
               throw new DepartamentoExcepton("erro en ID del area");
        }
        $this->_idSucursal = $idSucursal;    
    }

    public function returnAreaAsArray(){
        $area = array();
        $area['idArea'] = $this-> getIdArea();
        $area['nomArea'] = $this-> getNomArea();
        $area['idSucursal'] = $this-> getIdSucursal();
        return $area;
    }
}