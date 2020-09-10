<?php
require_once('db.php');
require_once('../model/Ciudad.php');
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

if(array_key_exists("idCiudad", $_GET)){
    $idCiudad = $_GET['idCiudad'];
    if($idCiudad === '' || !is_numeric($idCiudad) ){
        $response = new Response();
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage("Id ciudad  valido");
        $response->send();
        exit;
    }
}

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(array_key_exists("idCiudad", $_GET)){
        try{
            $query = $db->prepare('select id_ciudad, nom_ciudad, id_depto from ciudades  where id_ciudad= :idCiudad');
            $query->bindParam(':idCiudad', $idCiudad);
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
                $ciudad = new Ciudad($row['id_ciudad'], $row['nom_ciudad'], $row['id_depto']);
                $ciudadArray[] = $ciudad->returnCiudadAsArray();            
            }
            $returnData = array();
            $returnData['nro_filas'] = $rowCount;
            $returnData['ciudad'] = $ciudadArray;

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
            $query = $db->prepare('select id_ciudad, nom_ciudad, id_depto from ciudades');
            $query->execute();

            $rowCount = $query->rowCount();
            $cuidadArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $ciudad = new Ciudad($row['id_ciudad'], $row['nom_ciudad'], $row['id_depto']);
                $ciudadArray[] = $ciudad->returnCiudadAsArray();
            }

            $returnData = array();
            $returnData['filas_retornadas'] = $rowCount;
            $returnData['ciudades'] = $ciudadArray;

            $response = new Response();
            $response->setSuccess(true);
            $response->setHttpStatusCode(200);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;

        }catch(CiudadException $ex){
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
        $query = $db->prepare('delete from ciudades where id_ciudad = :idCiudad');
        $query->bindParam(':idCiudad', $idCuidad);
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
        if(!isset($jsonData->nomCiudad)){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Nombre de departamento es obligatorio');
            $response->send();
            exit();
        }
        $newCiudad = new Ciudad(null, $jsonData->nomCiudad, $jsonData->idDepto);
        $query = $db->prepare('insert into ciudades (nom_ciudad , id_depto) values (:nomCiudad, :idDepto)');
        $query->bindParam(':nomCiudad', $newCiudad->getNomCiudad(), PDO::PARAM_STR);
        $query->bindParam(':idDepto', $newCiudad->getIdDepto(), PDO::PARAM_STR);
        $query->execute();
        $rowCount = $query->rowCount();
        
        if($rowCount===0){
            $response = new Response();
            $response->setSuccess(false);
            $response->setHttpStatusCode(400);
            $response->addMessage('Falló creación de ciudades');
            $response->send();
            exit();
        }
        $lastIdCiudad = $db->lastInsertId();

        $response = new Response();
        $response->setSuccess(true);
        $response->setHttpStatusCode(201);
        $response->addMessage('Ciudad creada');
        $response->setData($lastIdCiudad);
        $response->send();
        exit();
    }catch(CiudadException $ex){
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
