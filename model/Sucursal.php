<?php
class SucursalException extends Exception{


}

class Sucursal{

    private $_idSucursal;
    private $_nomSucursal;
    private $_dirSucursal;
    private $_telSucursal;
    private $_idCiudad;
    
    public function __construct($idSucursal, $nomSucursal, $dirSucursal, $telSucursal, $idCiudad){        
        $this->_idSucursal = $idSucursal;
        $this->_nomSucursal = $nomSucursal;
        $this->_dirSucursal = $dirSucursal;  
        $this->_telSucursal = $telSucursal; 
        $this->_idCiudad = $idCiudad; 
    }

    public function getIdSucursal(){
        return $this->_idSucursal;
    }

    public function setIdSucursal($idSucursal){
        if($idSucursal !== null && !is_numeric($idSucursal)){
            throw new CiudadException("error en el id de la sucursal");
        }
        $this->_idSucursal = $idSucursal;
    }

    public function getNomSucursal(){
        return $this->_nomSucursal;
    }

    public function setNomSucursal($nomSucursal){
        if($nomSucursal !== null &&  strlen($nomSucursal)>50){
            throw new CiudadException("error en el nombre de la sucursal");
        }
    }

    public function getDirSucursal(){
        return $this->_dirSucursal;
    }

    public function setDirSucursal($dirSucursal){       
        $this->_dirSucursal = $dirSucursal;    
    }

    public function getTelSucursal(){
        return $this->_telSucursal;
    }

    public function setTelSucursal($telSucursal){
        if($telSucursal !== null && !is_numeric($telSucursal)){
            throw new CiudadException("error en el telefono de la sucursal");
        }       
        $this->_telSucursal = $telSucursal;    
    }

    public function getIdCiudad(){
        return $this->_idCiudad;
    }

    public function setIdCiudad($idCiudad){   
        if($idCiudad !== null && !is_numeric($idCiudad)){
            throw new CiudadException("error en la id de ciudad");
        }     
        $this->_idCiudad = $idCiudad;    
    }

    public function returnSucursalAsArray(){
        $sucursal = array();
        $sucursal['idSucursal'] = $this-> getIdSucursal();
        $sucursal['nomSucursal'] = $this-> getNomSucursal();
        $sucursal['dirSucursal'] = $this-> getDirSucursal();
        $sucursal['telSucursal'] = $this-> getTelSucursal();
        $sucursal['idCiudad'] = $this-> getIdCiudad();
        return $sucursal;
    }
}