<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \easyPlanning\Page;
use \easyPlanning\Model\User;

$app = new Slim();

$app->config('debug', true);

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

//User::verifyLogin();
    
$app->get('/', function() {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl("index");
});

$app->get('/users', function(){
    User::verifyLogin();
    $users = User::listAll();
    $page = new Page();
    $page->setTpl('users', array(
        "users"=>$users
    ));
});

$app->get('/users/create', function(){
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('users-create');
});

//USER DELETE
$app->get('/users/:iduser/delete', function($iduser){
    User::verifyLogin();
    $user=new User();
    $user->get((int)$iduser);
    $user->delete();
    header("Location: /users");
    exit;
});

//USER UPDATE
$app->get('/users/:iduser', function($iduser){
    User::verifyLogin();
    $user = new User();
    $user->get((int)$iduser);
    $page = new Page();
    $page->setTpl('users-update', array(
        "user"=>$user->getValues()
    ));
});

//USER SAVE CREATE
$app->post('/users/create', function(){
    User::verifyLogin();
    $user = new User();
    $_POST["user_isadmin"] = isset($_POST["user_isadmin"])?1:0;
    $user->setData($_POST);
    $user->save();
    header("Location: /users");
    exit;
});

//USER SAVE UPDATE
$app->post('/users/:iduser', function($iduser){
    User::verifyLogin();
    $user=new User();
    $_POST["user_isadmin"] = isset($_POST["user_isadmin"])?1:0;
    $user->get((int)$iduser);
    $user->setData($_POST);
    $user->update();
    header("Location: /users");
    exit;
});

$app->run();

 ?>