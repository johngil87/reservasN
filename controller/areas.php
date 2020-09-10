<?php
require_once('db.php');
require_once('../model/Areas.php');
require_once('../model/Response.php');

try{
    $db = DB::conectarDB();
   }catch(PDOException $ex){
       $response = new Response();
       $response->setSuccess(false);
       $response->setHttpStatusCode(500);
       $response->addMessage("Error de conexion a BD");
       $response->send();
       exit;
   }

if(array_key_exists("idArea", $_GET)){
    $idArea = $_GET['idArea'];
    if($idArea === '' || !is_numeric($idArea) ){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage("Id area  valido");
        $response->send();
        exit;
    }
}

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(array_key_exists("idArea", $_GET)){
        try{
            $query = $db->prepare('select id_area, nom_area, id_sucursal from areas  where id_area= :idArea');
            $query->bindParam(':idArea', $idArea);
            $query->execute();

            $rowCount = $query->rowCount();
            if($rowCount === 0){
                $response = new Response();
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("departamento NO ENCONTRADO");
                $response->send();
                exit;     
            }
            while($row = $query -> fetch(PDO::FETCH_ASSOC)){
                $area = new Areas($row['id_area'], $row['nom_area'], $row['id_sucursal']);
                $areaArray[] = $area->returnAreaAsArray();            
            }
            $returnData = array();
            $returnData['nro_filas'] = $rowCount;
            $returnData['area'] = $areaArray;

            $response = new Response();
            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->setData($returnData);
            $response->send();
                exit; 


        }catch(DepartamentoException $ex){
            $response = new Response();
                $response->setSuccess(false);
                $response->setHttpStatusCode(500);
                $response->addMessage($ex->getMessage());
                $response->send();
                exit; 
        }catch(PDOException $ex){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("error conectando a base de datos");
            $response->send();
            exit; 
        }
    }else{
        try{
            $query = $db->prepare('select id_area, nom_area, id_sucursal from areas');
            $query->execute();

            $rowCount = $query->rowCount();
            $areasArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $area = new Areas($row['id_area'], $row['nom_area'], $row['id_sucursal']);
                $areaArray[] = $area->returnAreaAsArray();
            }

            $returnData = array();
            $returnData['filas_retornadas'] = $rowCount;
            $returnData['area'] = $areaArray;

            $response = new Response();
            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;

        }catch(DepartamentoException $ex){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }catch(PDOException $ex){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(500);
            $response->addMessage("Error conectando a Base de Datos");
            $response->send();
            exit;
        }
    }    
}elseif($_SERVER['REQUEST_METHOD']== 'DELETE'){
    try{
        $query = $db->prepare('delete from areas where id_area = :idArea');
        $query->bindParam(':idArea', $idArea);
        $query->execute();

        $rowCount = $query -> rowCount();

        if($rowCount === 0){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(404);
            $response->addMessage('ciudad no encontrada');
            $response->send();
            exit();
        }
        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(200);
        $response->addMessage('ciudad eliminada');
        $response->send();
        exit();


    }catch(PDOException $ex){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(500);
        $response->addMessage('Error eliminando cuidad');
        $response->send();
        exit();
    }
}elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
    try{
        if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Content Type no corresponde a formato JSON');
            $response->send();
            exit();
        }   
        $rawPOSTData = file_get_contents('php://input');
        if(!$jsonData = json_decode($rawPOSTData)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Request Body no corresponde a formato JSON');
            $response->send();
            exit();
        }
        if(!isset($jsonData->nomArea)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Nombre del area es obligatorio');
            $response->send();
            exit();
        }
        if(!isset($jsonData->idSucursal)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Identificador de la sucursal es obligatorio');
            $response->send();
            exit();
        }
        $newArea = new Areas(null, $jsonData->nomArea,  $jsonData->idSucursal);
        $query = $db->prepare('insert into areas (nom_area, id_sucursal) values (:nomArea, :idSucursal)');
        $query->bindParam(':nomArea', $newArea->getNomArea(), PDO::PARAM_STR);
        $query->bindParam(':idSucursal', $newArea->getIdSucursal(), PDO::PARAM_STR);
        $query->execute();
        $rowCount = $query->rowCount();
        
        if($rowCount===0){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Falló creación de departamento');
            $response->send();
            exit();
        }
        $lastIdDepto = $db->lastInsertId();

        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(201);
        $response->addMessage('Area creada');
        $response->setData($lastIdDepto);
        $response->send();
        exit();
    }catch(AreasException $ex){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage($ex->getMessage());
        $response->send();
        exit();
    }catch(PDOException $ex){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(500);
        $response->addMessage('Falló conexión a BD');
        $response->send();
        exit();
    }
}