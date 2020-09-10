<?php
require_once('db.php');
require_once('../model/Sucursal.php');
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

if(array_key_exists("idSucursal", $_GET)){
    $idSucursal = $_GET['idSucursal'];
    if($idSucursal === '' || !is_numeric($idSucursal) ){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage("Id ciudad  valido");
        $response->send();
        exit;
    }
}

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(array_key_exists("idSucursal", $_GET)){
        try{
            $query = $db->prepare('select id_sucursal, nom_sucursal, dir_sucursal, tel_sucursal, id_ciudad from sucursales  where id_sucursal= :idSucursal');
            $query->bindParam(':idSucursal', $idSucursal);
            $query->execute();

            $rowCount = $query->rowCount();
            if($rowCount === 0){
                $response = new Response();
                $response->setSuccess(false);
                $response->setHttpStatusCode(404);
                $response->addMessage("SUCURSAL NO ENCONTRADA");
                $response->send();
                exit;     
            }
            while($row = $query -> fetch(PDO::FETCH_ASSOC)){
                $sucursal = new Sucursal($row['id_sucursal'], $row['nom_sucursal'], $row['dir_sucursal'],$row['tel_sucursal'],$row['id_ciudad']);
                $sucursalArray[] = $sucursal->returnSucursalAsArray();            
            }
            $returnData = array();
            $returnData['nro_filas'] = $rowCount;
            $returnData['sucursal'] = $sucursalArray;

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
            $query = $db->prepare('select id_sucursal, nom_sucursal, dir_sucursal, tel_sucursal, id_ciudad from sucursales');
            $query->execute();

            $rowCount = $query->rowCount();
            $sucursalArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $sucursal = new Sucursal($row['id_sucursal'], $row['nom_sucursal'], $row['dir_sucursal'], $row['tel_sucursal'], $row['id_ciudad']);
                $sucursalArray[] = $sucursal->returnSucursalAsArray();
            }

            $returnData = array();
            $returnData['filas_retornadas'] = $rowCount;
            $returnData['deptos'] = $sucursalArray;

            $response = new Response();
            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;

        }catch(SucursalException $ex){
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
        $query = $db->prepare('delete from sucursales where id_sucursal = :idSucursal');
        $query->bindParam(':idSucursal', $idSucursal);
        $query->execute();

        $rowCount = $query -> rowCount();

        if($rowCount === 0){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(404);
            $response->addMessage('sucursal no encontrada');
            $response->send();
            exit();
        }
        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(200);
        $response->addMessage('sucursal eliminada');
        $response->send();
        exit();


    }catch(PDOException $ex){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(500);
        $response->addMessage('Error eliminando la sucursal');
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
        if(!isset($jsonData->nomSucursal)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Nombre de departamento es obligatorio');
            $response->send();
            exit();
        }
        if(!isset($jsonData->dirSucursal)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('direccion de la sucursal es obligatorio');
            $response->send();
            exit();
        }
        if(!isset($jsonData->telSucursal)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('telefono de la sucursal es obligatorio');
            $response->send();
            exit();
        }
        if(!isset($jsonData->idCiudad)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('identificador de la ciudad es obligatorio');
            $response->send();
            exit();
        }
        
        $newSucursal = new Sucursal(null, $jsonData->nomSucursal, $jsonData->dirSucursal, $jsonData->telSucursal, $jsonData->idCiudad);
        $query = $db->prepare('insert into sucursales (nom_sucursal, dir_sucursal, tel_sucursal, id_ciudad) values (:nomSucursal, :dirSucursal, :telSucursal, :idCiudad )');
        $query->bindParam(':nomSucursal', $newSucursal->getNomSucursal(), PDO::PARAM_STR);
        $query->bindParam(':dirSucursal', $newSucursal->getDirSucursal(), PDO::PARAM_STR);
        $query->bindParam(':telSucursal', $newSucursal->getTelSucursal(), PDO::PARAM_STR);
        $query->bindParam(':idCiudad', $newSucursal->getIdCiudad(), PDO::PARAM_STR);
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
        $lastIdSucursal = $db->lastInsertId();

        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(201);
        $response->addMessage('Sucursal creada');
        $response->setData($lastIdSucursal);
        $response->send();
        exit();
    }catch(SucursalException $ex){
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
