<?php
session_start();
require_once ("vendor/autoload.php");

use \Slim\Slim;
use \easyPlanning\Page;
use \easyPlanning\Model\User;
use easyPlanning\Model\Organization;

$app = new Slim();

$app->config('debug', true);

// LOGIN VIEW
$app->get('/login', function () {
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('login');
});

$app->post('/login', function () {
    try {
        User::login($_POST["login"], $_POST["password"]);
        header("Location: /");
    } catch (Exception $e) {
        header("Location: /login");
    }
    exit();
});

$app->get('/logout', function () {
    User::logout();
    header("Location: /login");
    exit();
});

// HOME
$app->get('/', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl("index");
});

// #########################################################################################
// USER LIST
$app->get('/users', function () {
    User::verifyLogin();
    $users = User::listAll();
    $page = new Page();
    $page->setTpl('users', array(
        "users" => $users
    ));
});

// USER VIEW CREATE
$app->get('/users/create', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('users-create');
});

// USER DELETE
$app->get('/users/:iduser/delete', function ($iduser) {
    User::verifyLogin();
    $user = new User();
    $user->get((int) $iduser);
    $user->delete();
    header("Location: /users");
    exit();
});

// USER VIEW UPDATE
$app->get('/users/:iduser', function ($iduser) {
    User::verifyLogin();
    $user = new User();
    $user->get((int) $iduser);
    $page = new Page();
    $page->setTpl('users-update', array(
        "user" => $user->getValues()
    ));
});

// USER SAVE CREATE
$app->post('/users/create', function () {
    User::verifyLogin();
    $user = new User();
    $_POST["user_isadmin"] = isset($_POST["user_isadmin"]) ? 1 : 0;
    $user->setData($_POST);
    $user->save();
    header("Location: /users");
    exit();
});

// USER SAVE UPDATE
$app->post('/users/:iduser', function ($iduser) {
    User::verifyLogin();
    $user = new User();
    $_POST["user_isadmin"] = isset($_POST["user_isadmin"]) ? 1 : 0;
    $user->get((int) $iduser);
    $user->setData($_POST);
    $user->update();
    header("Location: /users");
    exit();
});

$app->get('/forgot', function () {
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('forgot');
});

$app->post('/forgot', function () {
    $user = User::getForgot($_POST["email"]);
    header("Location: /forgot/sent");
    exit();
});

$app->get('/forgot/sent', function () {
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('forgot-sent');
});

$app->get('/forgot/reset', function () {
    $user = User::validForgotDecrypt($_GET["code"]);
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    
    $page->setTpl('forgot-reset', array(
        "name" => $user["person_name"],
        "code" => $_GET["code"]
    ));
});

$app->post('/forgot/reset', function () {
    $forgot = User::validForgotDecrypt($_POST["code"]);
    User::setForgotUsed($forgot["recovery_id"]);
    $user = new User();
    $user->get((int) $forgot["user_id"]);
    
    $pass = password_hash($_POST["password"], PASSWORD_DEFAULT, [
        "cost" => 12
    ]);
    $user->setPassword($pass);
    
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('forgot-reset-success');
});

// ORGANIZATION ############################################################################
// LIST
$app->get('/orgs', function () {
    User::verifyLogin();
    $orgs = Organization::listAll();
    $page = new Page();
    $page->setTpl('orgs', array(
        "orgs" => $orgs
    ));
});

// CREATE
$app->get('/orgs/create', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('orgs-create',array(
        "legalnatures"=>Organization::getLegalNatureList()
    ));
});

// DELETE
$app->get('/orgs/:idorg/delete', function ($idorg) {
    User::verifyLogin();
    $org = new Organization();
    $org->get((int) $idorg);
    $org->delete();
    header("Location: /orgs");
    exit();
});

// VIEW UPDATE
$app->get('/orgs/:idorg', function ($idorg) {
    User::verifyLogin();
    $org = new Organization();
    $org->get((int) $idorg);
    $page = new Page();
    $page->setTpl('orgs-update', array(
        "org" => $org->getValues(),
        "legalnatures"=>Organization::getLegalNatureList()
    ));
});

// SAVE CREATE
$app->post('/orgs/create', function () {
    User::verifyLogin();
    $org = new Organization();
    $_POST["org_notification"] = isset($_POST["org_notification"]) ? 1 : 0;
    $org->setData($_POST);
    $org->save();
    header("Location: /orgs");
    exit();
});

// SAVE UPDATE
$app->post('/orgs/:idorg', function ($idorg) {
    User::verifyLogin();
    $org = new Organization();
    $_POST["org_notification"] = isset($_POST["org_notification"]) ? 1 : 0;
    $org->get((int) $idorg);
    $org->setData($_POST);
    $org->update();
    header("Location: /orgs");
    exit();
});

$app->run();

?>