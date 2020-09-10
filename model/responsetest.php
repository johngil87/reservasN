<?php
require_once('Response.php');

$response = new Response();

$response->setSuccess(true);
$response->setHttpStatusCode(200);
$response->addMessage("mensaje prueba 1");
$response->addMessage("mensaje prueba 2");
$response->send();