<?php
session_start();
require_once ("vendor/autoload.php");

use \Slim\Slim;
use \easyPlanning\Page;
use \easyPlanning\Model\User;
use easyPlanning\Model\Organization;
use easyPlanning\Model\QSet;
use easyPlanning\Model\Perspective;
use easyPlanning\Model\Question;
use easyPlanning\Model\Plan;
use easyPlanning\Model\Respondent;

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
        if (isset($_SESSION[User::SESSION]["org_id"])) {
            header("Location: /");
        } else {
            header("Location: /loginOrganization");
        }
    } catch (Exception $e) {
        $page = new Page([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl('login', array(
            "error" => $e->getMessage()
        ));
    }
    exit();
});

$app->get('/loginOrganization', function () {
    User::verifyLogin();
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('loginOrganization', array(
        "orgs" => User::getSessionUserOrganizations(),
        "user_isamdin" => (int) $_SESSION[User::SESSION]["user_isadmin"]
    ));
});

$app->post('/loginOrganization', function () {
    User::verifyLogin();
    $idorg = isset($_POST["org_id"]) ? $_POST["org_id"] : 0;
    User::setSessionOrganization($idorg);
    header("Location: /");
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
    $page = new Page([
        "data" => array(
            "logged" => $_SESSION[User::SESSION]
        )
    ]);
    $page->setTpl("index", array(
        "logged" => $_SESSION[User::SESSION]
    ));
});

$app->get('/forgot', function () {
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('forgot');
});

$app->post('/forgot', function () {
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    try{
        $user = User::getForgot($_POST["email"]);
        $page->setTpl('forgot-sent');
    }catch(Exception $e){
        $page->setTpl('forgot-sent', array(
            "error" => $e->getMessage()
        ));
    }
});

//Função desabilitada, chamada direto pelo endereço "/forgot"
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
    
    $user->setPassword($_POST["password"]);
    
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
    $page->setTpl('users-create', array(
        "types" => USer::getUserTypeList()
    ));
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
        "obj" => $obj->getValues(),
        "types" => User::getUserTypeList()
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
        "legalnatures" => Organization::getOrgLegalNatureList(),
        "status" => Organization::getOrgStatusList(),
        "sizes" => Organization::getOrgSizeList()
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
        "legalnatures" => Organization::getOrgLegalNatureList(),
        "status" => Organization::getOrgStatusList(),
        "sizes" => Organization::getOrgSizeList()
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
    $_POST["persp_color"] = isset($_POST["persp_color"]) ? str_replace("#", "", $_POST["persp_color"]) : "006666";
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
    $_POST["persp_color"] = isset($_POST["persp_color"]) ? str_replace("#", "", $_POST["persp_color"]) : "006666";
    $obj->setData($_POST);
    $obj->update();
    header("Location: /perspectives");
    exit();
});

// #########################################################################################
// QUESTIONS
// #########################################################################################
// LIST
$app->get('/questions', function () {
    User::verifyLogin();
    $objs = Question::listAll();
    $page = new Page();
    $page->setTpl('questions', array(
        "objs" => $objs
    ));
});

// CREATE
$app->get('/questions/create', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('questions-create', array(
        "status" => Question::getStatusList(),
        "environments" => Question::getEnvironmentList(),
        "qsets" => QSet::listAll(),
        "perspectives" => Perspective::listAll()
    ));
});

// DELETE
$app->get('/questions/:idobj/delete', function ($idobj) {
    User::verifyLogin();
    $obj = new Question();
    $obj->get((int) $idobj);
    $obj->delete();
    header("Location: /questions");
    exit();
});

// VIEW UPDATE
$app->get('/questions/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new Question();
    $obj->get((int) $idobj);
    $page = new Page();
    $page->setTpl('questions-update', array(
        "obj" => $obj->getValues(),
        "status" => Question::getStatusList(),
        "environments" => Question::getEnvironmentList(),
        "qsets" => QSet::listAll(),
        "perspectives" => Perspective::listAll()
    ));
});

// SAVE CREATE
$app->post('/questions/create', function () {
    User::verifyLogin();
    $obj = new Question();
    $_POST["quest_iscriticalkey"] = isset($_POST["quest_iscriticalkey"]) ? 1 : 0;
    $obj->setData($_POST);
    $obj->save();
    header("Location: /questions");
    exit();
});

// SAVE UPDATE
$app->post('/questions/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new Question();
    $obj->get((int) $idobj);
    $_POST["quest_iscriticalkey"] = isset($_POST["quest_iscriticalkey"]) ? 1 : 0;
    $obj->setData($_POST);
    $obj->update();
    header("Location: /questions");
    exit();
});

// #########################################################################################
// STRATEGIC PLANNING
// #########################################################################################
// LIST
$app->get('/plans', function () {
    User::verifyLogin();
    $objs = Plan::listAll();
    $page = new Page();
    $page->setTpl('plans', array(
        "objs" => $objs
    ));
});

// CREATE
$app->get('/plans/create', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('plans-create');
});

// DELETE
$app->get('/plans/:idobj/delete', function ($idobj) {
    User::verifyLogin();
    $obj = new Plan();
    $obj->get((int) $idobj);
    $obj->delete();
    header("Location: /plans");
    exit();
});

// VIEW UPDATE
$app->get('/plans/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new Plan();
    $obj->get((int) $idobj);
    $page = new Page();
    $page->setTpl('plans-update', array(
        "obj" => $obj->getValues()
    ));
});

// SAVE CREATE
$app->post('/plans/create', function () {
    User::verifyLogin();
    $obj = new Plan();
    $_POST["plan_isopen"] = isset($_POST["plan_isopen"]) ? 1 : 0;
    $obj->setData($_POST);
    $obj->save();
    header("Location: /plans");
    exit();
});

// SAVE UPDATE
$app->post('/plans/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new Plan();
    $_POST["plan_isopen"] = isset($_POST["plan_isopen"]) ? 1 : 0;
    $obj->get((int) $idobj);
    $obj->setData($_POST);
    $obj->update();
    header("Location: /plans");
    exit();
});

// ########################################################################################
// RESPONDENTS
// #########################################################################################
// LIST
$app->get('/respondents', function () {
    User::verifyLogin();
    $data = $_SESSION[User::SESSION];
    $objs = Respondent::getFromOrganization($data["org_id"]);
    $page = new Page();
    $page->setTpl('respondents', array(
        "objs" => $objs
    ));
});

// CREATE
$app->get('/respondents/create', function () {
    User::verifyLogin();
    $page = new Page();
    $page->setTpl('respondents-create', array(
        "levels" => Respondent::getOrganizationLevelList()
    ));
});

// DELETE
$app->get('/respondents/:idobj/delete', function ($idobj) {
    User::verifyLogin();
    $obj = new Respondent();
    $obj->get((int) $idobj);
    $obj->delete();
    header("Location: /respondents");
    exit();
});

// VIEW UPDATE
$app->get('/respondents/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new Respondent();
    $obj->get((int) $idobj);
    $page = new Page();
    $page->setTpl('respondents-update', array(
        "obj" => $obj->getValues(),
        "levels" => Respondent::getOrganizationLevelList()
    ));
});

// SAVE CREATE
$app->post('/respondents/create', function () {
    User::verifyLogin();
    $obj = new Respondent();
    $_POST["resp_allowpartial"] = isset($_POST["resp_allowpartial"]) ? 1 : 0;
    $_POST["resp_allowreturn"] = isset($_POST["resp_allowreturn"]) ? 1 : 0;
    $obj->setData($_POST);
    $obj->save();
    header("Location: /respondents");
    exit();
});

// SAVE UPDATE
$app->post('/respondents/:idobj', function ($idobj) {
    User::verifyLogin();
    $obj = new Respondent();
    $_POST["respondent_isopen"] = isset($_POST["respondent_isopen"]) ? 1 : 0;
    $obj->get((int) $idobj);
    $obj->setData($_POST);
    $obj->update();
    header("Location: /respondents");
    exit();
});

$app->run();

?>