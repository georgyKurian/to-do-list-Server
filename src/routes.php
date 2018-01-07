<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
/*
$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
*/

$app->get('/getdata', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Get data");
    $list = array();
    $handler = $this->db->query("SELECT id, heading, description, is_complete FROM items;");

    while($r = $handler->fetch()){
      if($r["is_complete"] == 0){
        $r["is_complete"]  = false;
      }
      else{
        $r["is_complete"]  = true;
      }
      
      array_push($list, $r);
    }

    $newResponse = $response->withHeader('Content-type', 'application/json');
    $newResponse = $newResponse->withJson($list);
    return $newResponse;
});

$app->get('/delete/{id}', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Delete data");
    $id = $route->getArgument('id');
    $handler = $this->db->query("SELECT * FROM items;");

    $newResponse = $response->withHeader('Content-type', 'application/json');
    $newResponse = $newResponse->withJson($list);
    return $newResponse;
});
