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
    $id = (int)$args['id'];

    $handler = $this->db->prepare("DELETE FROM items WHERE id = :id;");
    $handler->bindParam(':id', $id);
    $handler->execute();

    $newResponse = $response->withHeader('Content-type', 'application/json');
    $newResponse = $newResponse->withJson(array("result" => "Deleted successfully"));
    return $newResponse;
});

$app->post('/update/{id}', function (Request $request, Response $response, array $args) {
    $this->logger->info("Update data");
    $id = (int)$args['id'];

    $data = $request->getParsedBody();

    //$jsonData = json_decode($data, true);

    $handler = $this->db->prepare("UPDATE items SET heading = :heading, description = :description, is_complete = :is_complete, updated_at=NOW() WHERE id = :id;");
    $handler->bindParam(':id', $id);
    $handler->bindParam(':heading', $data["heading"]);
    $handler->bindParam(':description', $data["description"]);
    $handler->bindParam(':is_complete', convertBooleanToInt($data["is_complete"]));

    if($handler->execute()){
      $handler = $this->db->prepare("SELECT id, heading, description, is_complete FROM items WHERE id = :id;");
      $handler->bindParam(':id', $id);
      $handler->execute();

      if($r = $handler->fetch(PDO::FETCH_ASSOC)){
        $r["is_complete"] = convertIntToBoolean($r["is_complete"]);
        $newResponse = $response->withHeader('Content-type', 'application/json');
        $newResponse = $newResponse->withJson($r);
        return $newResponse;
      }
    }
    $newResponse = $response->withHeader('Content-type', 'application/json');
    $newResponse = $newResponse->withJson(array("Error"=>"error in request"));
    return $newResponse;
});

$app->post('/insert', function (Request $request, Response $response, array $args) {
    $this->logger->info("Insert data");

    $data = $request->getParsedBody();

    $handler = $this->db->prepare("INSERT INTO items (heading , description, is_complete) VALUES(:heading, :description, :is_complete);");
    $handler->bindParam(':heading', $data["heading"]);
    $handler->bindParam(':description', $data["description"]);
    $handler->bindParam(':is_complete', convertBooleanToInt($data["is_complete"]));

    if($handler->execute()){
      $id = $this->db->lastInsertId();

      $handler = $this->db->prepare("SELECT id, heading, description, is_complete FROM items WHERE id = :id;");
      $handler->bindParam(':id', $id);
      $handler->execute();

      if($r = $handler->fetch(PDO::FETCH_ASSOC)){
        $r["is_complete"] = convertIntToBoolean($r["is_complete"]);
        $newResponse = $response->withHeader('Content-type', 'application/json');
        $newResponse = $newResponse->withJson($r);
        return $newResponse;
      }
    }
    $newResponse = $response->withHeader('Content-type', 'application/json');
    $newResponse = $newResponse->withJson(array("Error"=>"error in data send"));
    return $newResponse;
});

function convertIntToBoolean($data){
  if($data == 0)
    return false;
  else
    return true;
}

function convertBooleanToInt($data){
  if($data == false)
    return 0;
  else
    return 1;
}
