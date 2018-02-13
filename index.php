<?php 

require_once("vendor/autoload.php");

$app = new \Slim\Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$sql = new p2a\DB\Sql();
	$results = $sql->select("SELECT * FROM empresa");
	echo json_encode($results);
});

$app->run();

 ?>