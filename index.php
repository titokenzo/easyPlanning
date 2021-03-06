<?php
session_start();
require_once ("vendor/autoload.php");

const _DS_ = DIRECTORY_SEPARATOR;

use \Slim\Slim;
use \easyPlanning\Page;
use \easyPlanning\Model\User;
use easyPlanning\Model\Organization;
use easyPlanning\Model\QSet;
use easyPlanning\Model\Perspective;
use easyPlanning\Model\Question;
use easyPlanning\Model\Plan;
use easyPlanning\Model\Respondent;
use easyPlanning\Model\Objective;
use easyPlanning\Model\Diagnostic;
use easyPlanning\Controller\Report;
use easyPlanning\PagePrint;
use easyPlanning\Model\Target;
use easyPlanning\Model\Monitoring;
use easyPlanning\Model\MonitoringObjective;

$app = new Slim(array(
    'mode' => 'development'
));

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

function verifyLogin()
{
    return function () {
        if (! isset($_SESSION[User::SESSION]) || ! $_SESSION[User::SESSION] || ! (int) $_SESSION[User::SESSION]["user_id"] > 0) {
            $app = \Slim\Slim::getInstance();
            // $app->flash('error', 'Você precisa estar logado');
            $app->response->redirect('/login');
        }
    };
}

function verifyLoginAdmin()
{
    return function () {
        if (! verifyLogin() || ! $_SESSION[User::SESSION]["org_id"] == 0 || ! $_SESSION[User::SESSION]["user_isadmin"] == 1) {
            $app = \Slim\Slim::getInstance();
            $app->flash('error', 'Você precisa estar logado');
            $app->response->redirect('/login');
        }
    };
}

// LOGIN VIEW
$app->get('/login', function () {
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $error = isset($_SESSION['slim.flash']['error']) ? $_SESSION['slim.flash']['error'] : NULL;
    $page->setTpl('login', array(
        "error" => $error
    ));
});

$app->post('/login', function () use ($app) {
    try {
        User::login($_POST["login"], $_POST["password"]);
        if (isset($_SESSION[User::SESSION]["org_id"])) {
            $app->response->redirect("/", 301);
        } else {
            $app->response->redirect("/loginOrganization", 301);
        }
    } catch (\Exception $e) {
        $app->flash('error', $e->getMessage());
        $app->response->redirect("/login", 301);
    }
});

$app->get('/loginOrganization', verifyLogin(), function () {
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('loginOrganization', array(
        "orgs" => User::getSessionUserOrganizations(),
        "user_isamdin" => (int) $_SESSION[User::SESSION]["user_isadmin"]
    ));
});

$app->post('/loginOrganization', verifyLogin(), function () use ($app) {
    $idorg = NULL;
    if (isset($_POST["org_id"])) {
        $idorg = $_POST["org_id"];
    } else {
        $app->response->redirect("/loginOrganization");
    }
    User::setSessionOrganization($idorg);
    $app->response->redirect("/easy.php");
});

$app->get('/logout', function () use ($app) {
    User::logout();
    $app->response->redirect("/login");
});

// HOME / DEFAULT
$app->get('/', verifyLogin(), function () use ($app) {
    $logged = $_SESSION[User::SESSION];
    if ($logged["org_id"] == 0 and $logged["user_isadmin"] == 1) {
        $app->response->redirect('/admin/orgs');
    } elseif ($logged["userorg_type"] == 1) {
        $app->response->redirect('/plans/view');
    } else {
        $app->response->redirect('/plans/view');
    }
});

$app->post('/', verifyLogin(), function () use ($app) {
    $app->response->redirect('/');
});

$app->get('/forgot', function () {
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('forgot');
});

$app->post('/forgot', function () {
    $error = NULL;
    try {
        $user = User::getForgot($_POST["email"]);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('forgot-sent', array(
        "error" => $error
    ));
});

// Função desabilitada, chamada direto pelo endereço "/forgot"
$app->get('/forgot/sent', function () {
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('forgot-sent');
});

$app->get('/forgot/reset', function () {
    $error = NULL;
    $user = NULL;
    try {
        $user = User::validForgotDecrypt($_GET["code"])["user_name"];
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    $page = new Page([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl('forgot-reset', array(
        "name" => $user,
        "code" => $_GET["code"],
        "error" => $error
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

// RESPOND
$app->get('/respond', function () use ($app) {
    $error = NULL;
    $user = NULL;
    $objs = NULL;
    $obj = NULL;
    try {
        $obj = new Respondent();
        $obj->setData(Respondent::validFormDecrypt($_GET["code"]));
        $intern = $obj->listAllQuestions(0);
        $extern = $obj->listAllQuestions(1);
        $pintern = $obj->listDistinctPerspectives(0);
        $pextern = $obj->listDistinctPerspectives(1);
        $responsesInputs = $obj->listInputs();
        $page = new Page([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl('respond', array(
            "obj" => $obj->getValues(),
            "pintern" => $pintern,
            "pextern" => $pextern,
            "intern" => $intern,
            "extern" => $extern,
            "responsesInputs" => $responsesInputs,
            "error" => $error
        ));
    } catch (Exception $e) {
        $app->flash("error", $e->getMessage());
        $app->response->redirect('/login', 301);
    }
});

// RESPOND PRINT
$app->get('/respond/print', function () use ($app) {
    $error = NULL;
    $user = NULL;
    $objs = NULL;
    $obj = NULL;
    try {
        $obj = new Respondent();
        $obj->setData(Respondent::validFormDecrypt($_GET["code"]));
        $obj->setresp_id(0);
        $intern = $obj->listAllQuestions(0);
        $extern = $obj->listAllQuestions(1);
        $pintern = $obj->listDistinctPerspectives(0);
        $pextern = $obj->listDistinctPerspectives(1);
        $responsesInputs = $obj->listInputs();
        $page = new Page([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl('respond-print', array(
            "obj" => $obj->getValues(),
            "pintern" => $pintern,
            "pextern" => $pextern,
            "intern" => $intern,
            "extern" => $extern,
            "responsesInputs" => $responsesInputs,
            "error" => $error
        ));
    } catch (Exception $e) {
        $app->flash("error", $e->getMessage());
        $app->response->redirect('/login', 301);
    }
});

// RESPOND-SAVE UPDATE
$app->post('/respond/save', function () {
    $idresp = $_POST["resp"];
    $idquest = $_POST["quest"];
    $type = $_POST["type"];
    $grade = $_POST["grade"];
    $obj = new Respondent();
    try {
        $obj->get((int) $idresp);
        $obj->updateResponses($idquest, $type, $grade);
        $message = "Dados salvos";
    } catch (Exception $e) {
        $message = $e->getMessage();
    } finally {
        echo json_encode($message);
        exit();
    }
});

// RESPOND-SAVE INPUT
$app->post('/respond/input', function () {
    $idresp = $_POST["resp"];
    $type = $_POST["type"];
    $input = $_POST["grade"];
    $obj = new Respondent();
    try {
        $obj->get((int) $idresp);
        $message = $obj->insertInputs($type, $input);
    } catch (Exception $e) {
        $message = $e->getMessage();
    } finally {
        echo json_encode($message);
        exit();
    }
});

// RESPOND-REMOVE INPUT
$app->post('/respond/remove', function () {
    $idresp = $_POST["resp"];
    $idinput = $_POST["quest"];
    $obj = new Respondent();
    try {
        $obj->get((int) $idresp);
        $obj->removeInputs($idinput);
        $message = "Dados salvos";
    } catch (Exception $e) {
        $message = $e->getMessage();
    } finally {
        echo json_encode($message);
        exit();
    }
});

// #########################################################################################
// USERS
// #########################################################################################
// USER LIST
$app->group('/users', verifyLogin(), function () use ($app) {
    $app->get('/', function () {
        $objs = User::listFromOrganization($_SESSION[User::SESSION]['org_id']);
        $page = new Page();
        $page->setTpl('users', array(
            "objs" => $objs
        ));
    });
    
    // USER VIEW CREATE
    $app->get('/create', function () {
        $page = new Page();
        $page->setTpl('users-create', array(
            "types" => USer::getUserTypeList()
        ));
    });
    
    // USER SAVE CREATE
    $app->post('/create', function () use ($app) {
        $obj = new User();
        // ATRIBUIR A ORG_ID=LOGGED.ORG_ID, O TIPO=2, ISADMIN=0
        $_POST["user_isadmin"] = 0;
        $idorg = $_SESSION[User::SESSION]['org_id'];
        $obj->setData($_POST);
        $obj->saveColaborador($idorg);
        $app->response->redirect('/users', 301);
    });
    
    // USER DELETE
    $app->get('/:idobj/delete', function ($idobj) use ($app) {
        // EXCLUIR APENAS DA ORGANIZAÇÃO!!
        $obj = new User();
        $obj->get((int) $idobj);
        $obj->delete();
        $app->response->redirect('/users', 301);
    });
    
    // USER VIEW UPDATE
    $app->get('/:idobj', function ($idobj) {
        // VERIFICAR SE O USUÁRIO É DA ORGANIZAÇÃO E SE NÃO É ADMINISTRADOR
        $obj = new User();
        $obj->get((int) $idobj);
        $page = new Page();
        $page->setTpl('users-update', array(
            "obj" => $obj->getValues(),
            "types" => User::getUserTypeList()
        ));
    });
    
    // USER SAVE UPDATE
    $app->post('/:idobj', function ($idobj) use ($app) {
        $obj = new User();
        $_POST["user_isadmin"] = isset($_POST["user_isadmin"]) ? 1 : 0;
        $obj->get((int) $idobj);
        $obj->setData($_POST);
        $obj->update();
        $app->response->redirect('/users', 301);
    });
});

// #########################################################################################
// STRATEGIC PLANNING
// #########################################################################################
// LIST
$app->group('/plans', verifyLogin(), function () use ($app) {
    $app->get('/', function () {
        $objs = Plan::listPlansFromOrg($_SESSION[User::SESSION]["org_id"]);
        $page = new Page();
        $page->setTpl('plans', array(
            "objs" => $objs,
            "status" => Plan::getPlanStatusList()
        ));
    });
    
    // VIEW
    $app->get('/view', function () {
        $obj = Plan::getActiveOrgPlan($_SESSION[User::SESSION]["org_id"]);
        $new = false;
        if (! $obj) {
            $new = true;
        }
        $page = new Page();
        $page->setTpl('plans-view', array(
            "obj" => $obj,
            "status" => Plan::getPlanStatusList(),
            "new" => $new
        ));
    });
    
    // CREATE
    $app->get('/create', function () {
        $page = new Page();
        $page->setTpl('plans-create', array(
            "status" => Plan::getPlanStatusList()
        ));
    });
    
    // SAVE CREATE
    $app->post('/create', function () use ($app) {
        $obj = new Plan();
        $_POST["plan_isopen"] = isset($_POST["plan_isopen"]) ? 1 : 0;
        $obj->setData($_POST);
        $obj->save();
        $app->response->redirect("/plans", 301);
    });
    
    // DELETE
    $app->get('/:idobj/delete', function ($idobj) use ($app) {
        $obj = new Plan();
        $obj->get((int) $idobj);
        $obj->delete();
        $app->response->redirect("/plans", 301);
    });
    
    // VIEW UPDATE
    $app->get('/:idobj', function ($idobj) {
        $obj = new Plan();
        $obj->get((int) $idobj);
        $page = new Page();
        $page->setTpl('plans-update', array(
            "obj" => $obj->getValues(),
            "status" => Plan::getPlanStatusList()
        ));
    });
    
    // SAVE UPDATE
    $app->post('/:idobj', function ($idobj) use ($app) {
        $obj = new Plan();
        $obj->get((int) $idobj);
        $obj->setData($_POST);
        $obj->update();
        $app->response->redirect("/plans", 301);
    });
});

// #########################################################################################
// DIAGNOSTIC
// #########################################################################################
// LIST
$app->group('/diagnostics', verifyLogin(), function () use ($app) {
    $app->get('/', verifyLogin(), function () {
        $objs = Diagnostic::listAllFromOrg($_SESSION[User::SESSION]["org_id"]);
        $status = Diagnostic::getStatusList();
        $page = new Page();
        $page->setTpl('diagnostics', array(
            "objs" => $objs,
            "status" => $status
        ));
    });
    
    // CREATE
    $app->get('/create', verifyLogin(), function () {
        $page = new Page();
        $page->setTpl('diagnostics-create');
    });
    
    // SAVE CREATE
    $app->post('/create', verifyLogin(), function () use ($app) {
        $obj = new Diagnostic();
        $obj->setData($_POST);
        $obj->save();
        $app->response->redirect("/diagnostics", 301);
    });
    
    // DELETE
    $app->get('/:idobj/delete', verifyLogin(), function ($idobj) use ($app) {
        $obj = new Diagnostic();
        $obj->get((int) $idobj);
        $obj->delete();
        $app->response->redirect("/diagnostics", 301);
    });
    
    // VIEW UPDATE
    $app->get('/:idobj', verifyLogin(), function ($idobj) {
        $obj = new Diagnostic();
        $obj->get((int) $idobj);
        $status = Diagnostic::getStatusList();
        $page = new Page();
        $page->setTpl('diagnostics-update', array(
            "obj" => $obj->getValues(),
            "status" => $status
        ));
    });
    
    // SAVE UPDATE
    $app->post('/:idobj', verifyLogin(), function ($idobj) use ($app) {
        $obj = new Diagnostic();
        $obj->get((int) $idobj);
        $obj->setData($_POST);
        $obj->update();
        $app->response->redirect("/diagnostics", 301);
    });
});

// ########################################################################################
// RESPONDENTS
// #########################################################################################
// LIST

$app->group('/respondents', verifyLogin(), function () use ($app) {
    $app->get('/', function () {
        $data = $_SESSION[User::SESSION];
        $page = new Page();
        $page->setTpl('respondents', array(
            "objs" => Respondent::getFromActiveDiagnostic($data["org_id"]),
            "levels" => Respondent::getOrganizationLevelList()
        ));
    });
    
    // CREATE
    $app->get('/create', function () {
        $error = isset($_SESSION['slim.flash']['error']) ? $_SESSION['slim.flash']['error'] : NULL;
        $page = new Page();
        $page->setTpl('respondents-create', array(
            "levels" => Respondent::getOrganizationLevelList(),
            "error" => $error
        ));
    });
    
    // SAVE CREATE
    $app->post('/create', function () use ($app) {
        $obj = new Respondent();
        $_POST["resp_allowpartial"] = isset($_POST["resp_allowpartial"]) ? 1 : 0;
        $_POST["resp_allowreturn"] = isset($_POST["resp_allowreturn"]) ? 1 : 0;
        $obj->setData($_POST);
        try {
            $obj->saveRespodents();
            $app->response->redirect('/respondents', 301);
        } catch (Exception $e) {
            $app->flash('error', $e->getMessage());
            $app->response->redirect('/respondents/create');
        }
    });
    
    // DELETE
    $app->get('/:idobj/delete', function ($idobj) use ($app) {
        $obj = new Respondent();
        $obj->get((int) $idobj);
        $obj->delete();
        $app->response->redirect('/respondents', 301);
    });
    
    // VIEW UPDATE
    $app->get('/:idobj', function ($idobj) {
        $obj = new Respondent();
        $obj->get((int) $idobj);
        $page = new Page();
        $page->setTpl('respondents-update', array(
            "obj" => $obj->getValues(),
            "levels" => Respondent::getOrganizationLevelList()
        ));
    });
    
    // SAVE UPDATE
    $app->post('/:idobj', function ($idobj) use ($app) {
        $obj = new Respondent();
        $obj->get((int) $idobj);
        $_POST["resp_allowpartial"] = isset($_POST["resp_allowpartial"]) ? 1 : 0;
        $_POST["resp_allowreturn"] = isset($_POST["resp_allowreturn"]) ? 1 : 0;
        $obj->setData($_POST);
        $obj->update();
        $app->response->redirect('/respondents', 301);
    });
    
    // RE-SEND EMAIL
    $app->get('/:idobj/resend', function ($idobj) use ($app) {
        $obj = new Respondent();
        $obj->get((int) $idobj);
        $obj->setData($_POST);
        $obj->sendDiagnosticEmail();
        $app->response->redirect('/respondents', 301);
    });
});

// ########################################################################################
// OBJECTIVES
// #########################################################################################
// LIST

$app->group('/objectives', verifyLogin(), function () use ($app) {
    $app->get('/', function () {
        $data = $_SESSION[User::SESSION];
        $objs = Objective::listObjectivesFromActivePlan($data["org_id"]);
        $page = new Page();
        $page->setTpl('objectives', array(
            "objs" => $objs,
            "plan" => Plan::getActiveOrgPlan($_SESSION[User::SESSION]["org_id"]),
            "users" => User::listFromOrganization($_SESSION[User::SESSION]["org_id"])
        ));
    });
    
    // CREATE
    $app->get('/create', function () {
        $error = isset($_SESSION['slim.flash']['error']) ? $_SESSION['slim.flash']['error'] : NULL;
        $page = new Page();
        $page->setTpl('objectives-create', array(
            "perspectives" => Perspective::listAll(),
            "plan" => Plan::getActiveOrgPlan($_SESSION[User::SESSION]["org_id"]),
            "users" => User::listFromOrganization($_SESSION[User::SESSION]["org_id"]),
            "error" => $error
        ));
    });
    
    // DELETE
    $app->get('/:idobj/delete', function ($idobj) use ($app) {
        $obj = new Objective();
        $obj->get((int) $idobj);
        $obj->delete();
        $app->response->redirect('/objectives', 301);
    });
    
    // VIEW UPDATE
    $app->get('/:idobj', function ($idobj) {
        $error = isset($_SESSION['slim.flash']['error']) ? $_SESSION['slim.flash']['error'] : NULL;
        $data = $_SESSION[User::SESSION];
        $obj = new Objective();
        $obj->get((int) $idobj);
        $plan = Plan::getActiveOrgPlan($_SESSION[User::SESSION]["org_id"]);
        $page = new Page();
        $page->setTpl('objectives-update', array(
            "obj" => $obj->getValues(),
            "plan" => $plan,
            "users" => User::listFromOrganization($_SESSION[User::SESSION]["org_id"]),
            "perspectives" => Perspective::listAll(),
            "objs" => Objective::getObjDepends($idobj),
            "error" => $error
        ));
    });
    
    // SAVE CREATE
    $app->post('/create', function () use ($app) {
        $obj = new Objective();
        $obj->setData($_POST);
        try {
            $obj->save();
            $app->response->redirect('/objectives', 301);
        } catch (Exception $e) {
            $app->flash('error', $e->getMessage());
            $app->response->redirect('/objectives/create');
        }
    });
    
    // SAVE UPDATE
    $app->post('/:idobj', function ($idobj) use ($app) {
        $obj = new Objective();
        $obj->get((int) $idobj);
        $obj->setData($_POST);
        try {
            $obj->update();
            $app->response->redirect('/objectives', 301);
        } catch (Exception $e) {
            $app->flash('error', $e->getMessage());
            $app->response->redirect('/objectives/' . $idobj);
        }
    });
    
    // ########################################################################################
    // TARGETS
    // #########################################################################################
    // LIST
    
    $app->get('/:idobj/targets', function ($idobj) {
        $data = $_SESSION[User::SESSION];
        $objs = Target::listObjectiveTargets($idobj);
        $objective = new Objective();
        $objective->get((int) $idobj);
        $page = new Page();
        $page->setTpl('targets', array(
            "objs" => $objs,
            "objective" => $objective->getValues(),
            "plan" => Plan::getActiveOrgPlan($_SESSION[User::SESSION]["org_id"]),
            "users" => User::listFromOrganization($_SESSION[User::SESSION]["org_id"])
        ));
    });
    
    // CREATE
    $app->get('/:idobj/targets/create', function ($idobj) {
        $error = isset($_SESSION['slim.flash']['error']) ? $_SESSION['slim.flash']['error'] : NULL;
        $objective = new Objective();
        $objective->get((int) $idobj);
        $page = new Page();
        $page->setTpl('targets-create', array(
            "objective" => $objective->getValues(),
            "plan" => Plan::getActiveOrgPlan($_SESSION[User::SESSION]["org_id"]),
            "users" => User::listFromOrganization($_SESSION[User::SESSION]["org_id"]),
            "error" => $error
        ));
    });
    
    // DELETE
    $app->get('/:idobj/targets/:idtarget/delete', function ($idobj, $idtarget) use ($app) {
        $obj = new Target();
        $obj->get((int) $idtarget);
        $obj->delete();
        $app->response->redirect('/objectives/' . $idobj . '/targets', 301);
    });
    
    // VIEW UPDATE
    $app->get('/:idobj/targets/:idtarget', function ($idobj, $idtarget) {
        $error = isset($_SESSION['slim.flash']['error']) ? $_SESSION['slim.flash']['error'] : NULL;
        $data = $_SESSION[User::SESSION];
        $objective = new Objective();
        $objective->get((int) $idobj);
        $obj = new Target();
        $obj->get((int) $idtarget);
        $plan = Plan::getActiveOrgPlan($_SESSION[User::SESSION]["org_id"]);
        $page = new Page();
        $page->setTpl('targets-update', array(
            "obj" => $obj->getValues(),
            "objective" => $objective->getValues(),
            "plan" => $plan,
            "users" => User::listFromOrganization($_SESSION[User::SESSION]["org_id"]),
            "frequenceUnits" => Target::getFrequenceUnitsList(),
            "error" => $error
        ));
    });
    
    // SAVE CREATE
    $app->post('/:idobj/targets/create', function ($idobj) use ($app) {
        $obj = new Target();
        $obj->setData($_POST);
        try {
            $obj->save();
            $app->response->redirect('/objectives/' . $idobj . '/targets', 301);
        } catch (Exception $e) {
            $app->flash('error', $e->getMessage());
            $app->response->redirect('/objectives/' . $idobj . '/targets/create');
        }
    });
    
    // SAVE UPDATE
    $app->post('/:idobj/targets/:idtarget', function ($idobj, $idtarget) use ($app) {
        $obj = new Target();
        $obj->get((int) $idtarget);
        $obj->setData($_POST);
        try {
            $obj->update();
            $app->response->redirect('/objectives/' . $idobj . '/targets', 301);
        } catch (Exception $e) {
            $app->flash('error', $e->getMessage());
            $app->response->redirect('/objectives/' . $idobj . '/targets/' . $idtarget);
        }
    });
});

// ########################################################################################
// MONITORING
// #########################################################################################
// LIST
$app->group('/monitoring', verifyLogin(), function () use ($app) {
    $app->get('/', function () {
        $data = $_SESSION[User::SESSION];
        $objs = Monitoring::listPlanLastResults($data["plan_id"]);
        $page = new Page();
        $page->setTpl('monitoring', array(
            "objs" => $objs,
            "objectives" => Monitoring::proccessObjectivesResults($objs),
            "users" => User::listFromOrganization($data["org_id"])
        ));
    });
    
    // VIEW TARGET CREATE
    $app->get('/target/create/:idtarget', function ($idtarget) {
        $error = isset($_SESSION['slim.flash']['error']) ? $_SESSION['slim.flash']['error'] : NULL;
        $data = $_SESSION[User::SESSION];
        $target = new Target();
        $target->get((int) $idtarget);
        $page = new Page();
        $page->setTpl('monitoring-target-create', array(
            "objs" => Monitoring::listTargetResults($idtarget),
            "target" => $target->getValues(),
            "users" => User::listFromOrganization($data["org_id"]),
            "frequenceUnits" => Target::getFrequenceUnitsList(),
            "error" => $error
        ));
    });
    
    // SAVE TARGET CREATE
    $app->post('/target/create', function () use ($app) {
        $obj = new Monitoring();
        $obj->setData($_POST);
        try {
            $obj->save();
            $app->response->redirect('/monitoring', 301);
        } catch (Exception $e) {
            $app->flash('error', $e->getMessage());
            $app->response->redirect('/monitoring/target/create/' . $_POST["target_id"]);
        }
    });
    
    // VIEW TARGET UPDATE
    $app->get('/target/update/:idmon', function ($idmon) {
        $error = isset($_SESSION['slim.flash']['error']) ? $_SESSION['slim.flash']['error'] : NULL;
        $data = $_SESSION[User::SESSION];
        $obj = new Monitoring();
        $obj->get((int) $idmon);
        $target = new Target();
        $target->get((int) $obj->gettarget_id());
        $page = new Page();
        $page->setTpl('monitoring-target-update', array(
            "obj" => $obj->getValues(),
            "objs" => Monitoring::listTargetResults($obj->gettarget_id()),
            "target" => $target->getValues(),
            "users" => User::listFromOrganization($data["org_id"]),
            "frequenceUnits" => Target::getFrequenceUnitsList(),
            "error" => $error
        ));
    });
    
    // SAVE TARGET UPDATE
    $app->post('/target/update/:idmon', function ($idmon) use ($app) {
        $obj = new Monitoring();
        $obj->get((int) $idmon);
        $obj->setData($_POST);
        try {
            $obj->update();
            $app->response->redirect('/monitoring', 301);
        } catch (Exception $e) {
            $app->flash('error', $e->getMessage());
            $app->response->redirect('/monitoring/target/update/' . $idmon);
        }
    });
    
    // VIEW OBJECTIVE CREATE
    $app->get('/obj/create/:idobj', function ($idobj) {
        $error = isset($_SESSION['slim.flash']['error']) ? $_SESSION['slim.flash']['error'] : NULL;
        $data = $_SESSION[User::SESSION];
        $objective = new Objective();
        $objective->get((int) $idobj);
        $page = new Page();
        $page->setTpl('monitoring-obj-create', array(
            "objective" => $objective->getValues(),
            "objs" => MonitoringObjective::listObjectiveMonitoring($idobj),
            "targets" => Monitoring::listObjectiveLastResults($idobj),
            "users" => User::listFromOrganization($data["org_id"]),
            "error" => $error
        ));
    });
});

// #############################################################################################
// REPORTS
// #############################################################################################
$app->group('/reports', verifyLogin(), function () use ($app) {
    $app->get('/questions', function () use ($app) {
        $user = NULL;
        $objs = NULL;
        $obj = NULL;
        try {
            $obj = new Report();
            $intern = $obj->listAllQuestions(0);
            $extern = $obj->listAllQuestions(1);
            $pintern = $obj->listDistinctPerspectives(0);
            $pextern = $obj->listDistinctPerspectives(1);
            $page = new PagePrint();
            $page->setTpl('print-questions', array(
                "pintern" => $pintern,
                "pextern" => $pextern,
                "intern" => $intern,
                "extern" => $extern
            ));
        } catch (Exception $e) {
            $app->flash("error", $e->getMessage());
            $app->response->redirect('/login', 301);
        }
    });
    
    $app->get('/diagnostics', function () use ($app) {
        $user = NULL;
        $objs = NULL;
        $obj = NULL;
        try {
            $obj = new Report();
            $grade1 = $obj->listGrade1Swot(1);
            $grade2 = $obj->listGrade2Swot(1);
            $responsesInputs = $obj->listInputs(1);
            $page = new PagePrint();
            $page->setTpl('print-diagnostic', array(
                "grade1" => $grade1,
                "grade2" => $grade2,
                "responsesInputs" => $responsesInputs
            ));
        } catch (Exception $e) {
            $app->flash("error", $e->getMessage());
            $app->response->redirect('/login', 301);
        }
    });
});

// #############################################################################################
// ADMIN ROUTES
// #############################################################################################
$app->group('/admin', verifyLoginAdmin(),
    function () use ($app) {
        $app->get('/', function () use ($app) {
            $app->response->redirect('/admin/orgs');
        });
        // #####################################################################################
        // ADMIN ORGANIZATIONS
        // #####################################################################################
        // LIST
        $app->group('/orgs', function () use ($app) {
            $app->get('/', function () {
                $objs = Organization::listAll();
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'orgs', array(
                    "objs" => $objs
                ));
            });
            
            // CREATE
            $app->get('/create', function () {
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'orgs-create', array(
                    "legalnatures" => Organization::getOrgLegalNatureList(),
                    "status" => Organization::getOrgStatusList(),
                    "sizes" => Organization::getOrgSizeList()
                ));
            });
            
            // DELETE
            $app->get('/:idobj/delete', function ($idobj) use ($app) {
                $obj = new Organization();
                $obj->get((int) $idobj);
                $obj->delete();
                $app->response->redirect('/admin/orgs', 301);
            });
            
            // VIEW UPDATE
            $app->get('/:idobj', function ($idobj) {
                $obj = new Organization();
                $obj->get((int) $idobj);
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'orgs-update', array(
                    "obj" => $obj->getValues(),
                    "legalnatures" => Organization::getOrgLegalNatureList(),
                    "status" => Organization::getOrgStatusList(),
                    "sizes" => Organization::getOrgSizeList()
                ));
            });
            
            // SAVE CREATE
            $app->post('/create', function () use ($app) {
                $obj = new Organization();
                $_POST["org_notification"] = isset($_POST["org_notification"]) ? 1 : 0;
                $obj->setData($_POST);
                $obj->save();
                $app->response->redirect('/admin/orgs', 301);
            });
            
            // SAVE UPDATE
            $app->post('/:idobj', function ($idobj) use ($app) {
                $obj = new Organization();
                $_POST["org_notification"] = isset($_POST["org_notification"]) ? 1 : 0;
                $obj->get((int) $idobj);
                $obj->setData($_POST);
                $obj->update();
                $app->response->redirect('/admin/orgs', 301);
            });
        });
        // #####################################################################################
        // ADMIN USERS
        // #####################################################################################
        // USER LIST
        $app->group('/users', function () use ($app) {
            $app->get('/', function () {
                $objs = User::listAll();
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'users', array(
                    "objs" => $objs
                ));
            });
            
            // USER VIEW CREATE
            $app->get('/create', function () {
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'users-create', array(
                    "types" => USer::getUserTypeList(),
                    "orgs" => User::getUserOrganizations()
                ));
            });
            
            // USER DELETE
            $app->get('/:idobj/delete', function ($idobj) use ($app) {
                $obj = new User();
                $obj->get((int) $idobj);
                $obj->delete();
                $app->response->redirect('\admin\users', 301);
            });
            
            // USER-ORG UPDATE
            $app->post('/organization', function () {
                $iduser = $_POST["user_id"];
                $idorg = $_POST["org_id"];
                $type = $_POST["type"];
                $obj = new User();
                try {
                    $obj->get((int) $iduser);
                    $obj->updatePermission($idorg, $type);
                    $message = "Dados salvos";
                } catch (Exception $e) {
                    $message = $e->getMessage();
                } finally {
                    echo json_encode($message);
                    exit();
                }
            });
            
            // USER TEST
            $app->get('/test', function () {
                $page = new Page([
                    "header" => false,
                    "footer" => false
                ]);
                $page->setTpl('admin' . _DS_ . 'users-test');
            });
            
            // USER VIEW UPDATE
            $app->get('/:idobj', function ($idobj) {
                $obj = new User();
                $obj->get((int) $idobj);
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'users-update', array(
                    "obj" => $obj->getValues(),
                    "types" => USer::getUserTypeList(),
                    "orgs" => User::getUserOrganizations($idobj)
                ));
            });
            
            // USER SAVE CREATE
            $app->post('/create', function () use ($app) {
                $obj = new User();
                $_POST["user_isadmin"] = isset($_POST["user_isadmin"]) ? 1 : 0;
                $obj->setData($_POST);
                $obj->save();
                $app->response->redirect('\admin\users', 301);
            });
            
            // USER SAVE UPDATE
            $app->post('/:idobj', function ($idobj) use ($app) {
                $obj = new User();
                $_POST["user_isadmin"] = isset($_POST["user_isadmin"]) ? 1 : 0;
                $obj->get((int) $idobj);
                $obj->setData($_POST);
                $obj->update();
                $app->response->redirect('\admin\users', 301);
            });
        });
        // #####################################################################################
        // ADMIN QUESTIONS SETS
        // #####################################################################################
        // LIST
        $app->group('/qsets', function () use ($app) {
            $app->get('/', function () {
                $objs = QSet::listAll();
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'qsets', array(
                    "objs" => $objs
                ));
            });
            
            // CREATE
            $app->get('/create', function () {
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'qsets-create');
            });
            
            // DELETE
            $app->get('/:idobj/delete', function ($idobj) use ($app) {
                $obj = new QSet();
                $obj->get((int) $idobj);
                $obj->delete();
                $app->response->redirect('\admin\qsets', 301);
            });
            
            // VIEW UPDATE
            $app->get('/:idobj', function ($idobj) {
                $obj = new QSet();
                $obj->get((int) $idobj);
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'qsets-update', array(
                    "obj" => $obj->getValues()
                ));
            });
            
            // SAVE CREATE
            $app->post('/create', function () use ($app) {
                $obj = new QSet();
                $obj->setData($_POST);
                $obj->save();
                $app->response->redirect('\admin\qsets', 301);
            });
            
            // SAVE UPDATE
            $app->post('/:idobj', function ($idobj) use ($app) {
                $obj = new QSet();
                $obj->get((int) $idobj);
                $obj->setData($_POST);
                $obj->update();
                $app->response->redirect('\admin\qsets', 301);
            });
        });
        // #####################################################################################
        // ADMIN PERSPECTIVES
        // #####################################################################################
        // LIST
        $app->group('/perspectives', function () use ($app) {
            $app->get('/', function () {
                $objs = Perspective::listAll();
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'perspectives', array(
                    "objs" => $objs
                ));
            });
            
            // CREATE
            $app->get('/create', function () {
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'perspectives-create');
            });
            
            // DELETE
            $app->get('/:idobj/delete', function ($idobj) use ($app) {
                $obj = new Perspective();
                $obj->get((int) $idobj);
                $obj->delete();
                $app->response->redirect('\admin\perspectives', 301);
            });
            
            // VIEW UPDATE
            $app->get('/:idobj', function ($idobj) {
                $obj = new Perspective();
                $obj->get((int) $idobj);
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'perspectives-update', array(
                    "obj" => $obj->getValues()
                ));
            });
            
            // SAVE CREATE
            $app->post('/create', function () use ($app) {
                $obj = new Perspective();
                $_POST["persp_color"] = isset($_POST["persp_color"]) ? str_replace("#", "", $_POST["persp_color"]) : "006666";
                $obj->setData($_POST);
                $obj->save();
                $app->response->redirect('\admin\perspectives', 301);
            });
            
            // SAVE UPDATE
            $app->post('/:idobj', function ($idobj) use ($app) {
                $obj = new Perspective();
                $obj->get((int) $idobj);
                $_POST["persp_color"] = isset($_POST["persp_color"]) ? str_replace("#", "", $_POST["persp_color"]) : "006666";
                $obj->setData($_POST);
                $obj->update();
                $app->response->redirect('\admin\perspectives', 301);
            });
        });
        
        // #####################################################################################
        // ADMIN QUESTIONS
        // #####################################################################################
        // LIST
        $app->group('/questions', function () use ($app) {
            $app->get('/', function () {
                $objs = Question::listAll();
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'questions', array(
                    "objs" => $objs
                ));
            });
            
            // CREATE
            $app->get('/create', function () {
                $page = new Page();
                $page->setTpl('admin\questions-create', array(
                    "status" => Question::getStatusList(),
                    "environments" => Question::getEnvironmentList(),
                    "qsets" => QSet::listAll(),
                    "perspectives" => Perspective::listAll()
                ));
            });
            
            // DELETE
            $app->get('/:idobj/delete', function ($idobj) use ($app) {
                User::verifyLogin();
                $obj = new Question();
                $obj->get((int) $idobj);
                $obj->delete();
                $app->response->redirect('\admin\questions', 301);
            });
            
            // VIEW UPDATE
            $app->get('/:idobj', function ($idobj) {
                $obj = new Question();
                $obj->get((int) $idobj);
                $page = new Page();
                $page->setTpl('admin' . _DS_ . 'questions-update', array(
                    "obj" => $obj->getValues(),
                    "status" => Question::getStatusList(),
                    "environments" => Question::getEnvironmentList(),
                    "qsets" => QSet::listAll(),
                    "perspectives" => Perspective::listAll()
                ));
            });
            
            // SAVE CREATE
            $app->post('/create', function () use ($app) {
                $obj = new Question();
                $_POST["quest_iscriticalkey"] = isset($_POST["quest_iscriticalkey"]) ? 1 : 0;
                $obj->setData($_POST);
                $obj->save();
                $app->response->redirect('\admin\questions', 301);
            });
            
            // SAVE UPDATE
            $app->post('/:idobj', function ($idobj) use ($app) {
                $obj = new Question();
                $obj->get((int) $idobj);
                $_POST["quest_iscriticalkey"] = isset($_POST["quest_iscriticalkey"]) ? 1 : 0;
                $obj->setData($_POST);
                $obj->update();
                $app->response->redirect('\admin\questions', 301);
            });
        });
    });

$app->run();

?>