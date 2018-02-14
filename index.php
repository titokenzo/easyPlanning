<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \easyPlanning\Page;
use \easyPlanning\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    User::verifyLogin();
	$page = new Page();
	$page->setTpl("index");
});

$app->get('/login',function(){
   $page = new Page([
       "header"=>false,
       "footer"=>false
   ]);
   $page->setTpl('login');
});

$app->post('/login', function(){
    User::login($_POST["login"], $_POST["password"]);
    header("Location: /");
    exit;
});

$app->get('/logout', function(){
    User::logout();
    header("Location: /login");
    exit;
});

$app->run();

 ?>