<?php
session_start();
require_once ("vendor/autoload.php");

use \Slim\Slim;
use \easyPlanning\Page;
use \easyPlanning\Model\User;
use easyPlanning\Model\Organization;
use easyPlanning\Model\QSet;
use easyPlanning\Model\Perspective;

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

// #########################################################################################
// USERS
// #########################################################################################
// USER LIST
$app->get('/users', function () {
    User::verifyLogin();
    $objs = User::listAll();
    $page = new Page();
    $page->setTpl('users', array(
        "objs" => $objs
    ));
});

// USER VIEW CREATE
$app->get('/users/create', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('users-create');
});

// USER DELETE
$app->get('/users/:idobj/delete', function ($idobj) {
    User::verifyLogin();
    $obj = new User();
    $obj->get((int) $idobj);
    $obj->delete();
    header("Location: /users");
    exit();
});

// USER VIEW UPDATE
$app->get('/users/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new User();
    $obj->get((int) $idobj);
    $page = new Page();
    $page->setTpl('users-update', array(
        "obj" => $obj->getValues()
    ));
});

// USER SAVE CREATE
$app->post('/users/create', function () {
    User::verifyLogin();
    $obj = new User();
    $_POST["user_isadmin"] = isset($_POST["user_isadmin"]) ? 1 : 0;
    $obj->setData($_POST);
    $obj->save();
    header("Location: /users");
    exit();
});

// USER SAVE UPDATE
$app->post('/users/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new User();
    $_POST["user_isadmin"] = isset($_POST["user_isadmin"]) ? 1 : 0;
    $obj->get((int) $idobj);
    $obj->setData($_POST);
    $obj->update();
    header("Location: /users");
    exit();
});

// #########################################################################################
// ORGANIZATIONS
// #########################################################################################
// LIST
$app->get('/orgs', function () {
    User::verifyLogin();
    $objs = Organization::listAll();
    $page = new Page();
    $page->setTpl('orgs', array(
        "objs" => $objs
    ));
});

// CREATE
$app->get('/orgs/create', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('orgs-create', array(
        "legalnatures" => Organization::getLegalNatureList(),
        "sizes" => Organization::getSizeList()
    ));
});

// DELETE
$app->get('/orgs/:idobj/delete', function ($idobj) {
    User::verifyLogin();
    $obj = new Organization();
    $obj->get((int) $idobj);
    $obj->delete();
    header("Location: /orgs");
    exit();
});

// VIEW UPDATE
$app->get('/orgs/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new Organization();
    $obj->get((int) $idobj);
    $page = new Page();
    $page->setTpl('orgs-update', array(
        "obj" => $obj->getValues(),
        "legalnatures" => Organization::getLegalNatureList(),
        "status" => Organization::getStatusList(),
        "sizes" => Organization::getSizeList()
    ));
});

// SAVE CREATE
$app->post('/orgs/create', function () {
    User::verifyLogin();
    $obj = new Organization();
    $_POST["org_notification"] = isset($_POST["org_notification"]) ? 1 : 0;
    $obj->setData($_POST);
    $obj->save();
    header("Location: /orgs");
    exit();
});

// SAVE UPDATE
$app->post('/orgs/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new Organization();
    $_POST["org_notification"] = isset($_POST["org_notification"]) ? 1 : 0;
    $obj->get((int) $idobj);
    $obj->setData($_POST);
    $obj->update();
    header("Location: /orgs");
    exit();
});

// #########################################################################################
// QUESTIONS SETS
// #########################################################################################
// LIST
$app->get('/qsets', function () {
    User::verifyLogin();
    $objs = QSet::listAll();
    $page = new Page();
    $page->setTpl('qsets', array(
        "objs" => $objs
    ));
});

// CREATE
$app->get('/qsets/create', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('qsets-create');
});

// DELETE
$app->get('/qsets/:idobj/delete', function ($idobj) {
    User::verifyLogin();
    $obj = new QSet();
    $obj->get((int) $idobj);
    $obj->delete();
    header("Location: /qsets");
    exit();
});

// VIEW UPDATE
$app->get('/qsets/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new QSet();
    $obj->get((int) $idobj);
    $page = new Page();
    $page->setTpl('qsets-update', array(
        "obj" => $obj->getValues()
    ));
});

// SAVE CREATE
$app->post('/qsets/create', function () {
    User::verifyLogin();
    $obj = new QSet();
    $obj->setData($_POST);
    $obj->save();
    header("Location: /qsets");
    exit();
});

// SAVE UPDATE
$app->post('/qsets/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new QSet();
    $obj->get((int) $idobj);
    $obj->setData($_POST);
    $obj->update();
    header("Location: /qsets");
    exit();
});

// #########################################################################################
// PERSPECTIVES
// #########################################################################################
// LIST
$app->get('/perspectives', function () {
    User::verifyLogin();
    $objs = Perspective::listAll();
    $page = new Page();
    $page->setTpl('perspectives', array(
        "objs" => $objs
    ));
});

// CREATE
$app->get('/perspectives/create', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('perspectives-create');
});

// DELETE
$app->get('/perspectives/:idobj/delete', function ($idobj) {
    User::verifyLogin();
    $obj = new Perspective();
    $obj->get((int) $idobj);
    $obj->delete();
    header("Location: /perspectives");
    exit();
});

// VIEW UPDATE
$app->get('/perspectives/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new Perspective();
    $obj->get((int) $idobj);
    $page = new Page();
    $page->setTpl('perspectives-update', array(
        "obj" => $obj->getValues()
    ));
});

// SAVE CREATE
$app->post('/perspectives/create', function () {
    User::verifyLogin();
    $obj = new Perspective();
    $_POST["persp_color"] = isset($_POST["persp_color"]) ? str_replace("#","",$_POST["persp_color"]) : "006666";
    $obj->setData($_POST);
    $obj->save();
    header("Location: /perspectives");
    exit();
});

// SAVE UPDATE
$app->post('/perspectives/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new Perspective();
    $obj->get((int) $idobj);
    $_POST["persp_color"] = isset($_POST["persp_color"]) ? str_replace("#","",$_POST["persp_color"]) : "006666";
    $obj->setData($_POST);
    $obj->update();
    header("Location: /perspectives");
    exit();
});

$app->run();

?>