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
use easyPlanning\Model\Objective;
use easyPlanning\Model\Diagnostic;

$app = new Slim();

$app->config('debug', true);

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
    $app->response->redirect("/");
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

$app->get('/diagnostics', function () use($app) {
    $error = NULL;
    $user = NULL;
    $objs = NULL;
    $obj = NULL;
    try {
        $obj = Respondent::validFormDecrypt($_GET["code"]);
        $intern = Question::listAllFromEnvironment(0);
        $extern = Question::listAllFromEnvironment(1);
        $pintern = Question::listDistinctlPerspectives(0);
        $pextern = Question::listDistinctlPerspectives(1);
        $page = new Page([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl('diagnostics', array(
            "obj"=>$obj, 
            "pintern"=>$pintern,
            "pextern"=>$pextern,
            "intern"=>$intern, 
            "extern"=>$extern, 
            "error"=>$error
        ));
    } catch (Exception $e) {
        $app->flash("error", $e->getMessage());
        $app->response->redirect('/login',301);
    }
});

// #########################################################################################
// USERS
// #########################################################################################
// USER LIST
$app->group('/users', verifyLogin(), function () use ($app) {
    $app->get('/', function () {
        $objs = User::listAll();
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
    $app->get('/', verifyLogin(), function () {
        $objs = Plan::listFromOrg($_SESSION[User::SESSION]["org_id"]);
        $page = new Page();
        $page->setTpl('plans', array(
            "objs" => $objs
        ));
    });
    
    // VIEW
    $app->get('/view', verifyLogin(), function () {
        $obj = Plan::getActive($_SESSION[User::SESSION]["org_id"]);
        $new = false;
        if (! $obj) {
            $new = true;
        }
        $page = new Page();
        $page->setTpl('plans-view', array(
            "obj" => $obj,
            "new" => $new
        ));
    });
    
    // CREATE
    $app->get('/create', verifyLogin(), function () {
        $page = new Page();
        $page->setTpl('plans-create');
    });
    
    // SAVE CREATE
    $app->post('/create', verifyLogin(), function () use ($app) {
        $obj = new Plan();
        $_POST["plan_isopen"] = isset($_POST["plan_isopen"]) ? 1 : 0;
        $obj->setData($_POST);
        $obj->save();
        $app->response->redirect("/plans", 301);
    });
    
    // DELETE
    $app->get('/:idobj/delete', verifyLogin(), function ($idobj) use ($app) {
        $obj = new Plan();
        $obj->get((int) $idobj);
        $obj->delete();
        $app->response->redirect("/plans", 301);
    });
    
    // VIEW UPDATE
    $app->get('/:idobj', verifyLogin(), function ($idobj) {
        $obj = new Plan();
        $obj->get((int) $idobj);
        $page = new Page();
        $page->setTpl('plans-update', array(
            "obj" => $obj->getValues()
        ));
    });
    
    // SAVE UPDATE
    $app->post('/:idobj', verifyLogin(), function ($idobj) use ($app) {
        $obj = new Plan();
        $_POST["plan_isopen"] = isset($_POST["plan_isopen"]) ? 1 : 0;
        $obj->get((int) $idobj);
        $obj->setData($_POST);
        $obj->update();
        $app->response->redirect("/plans", 301);
    });
});

// ########################################################################################
// RESPONDENTS
// #########################################################################################
// LIST

$app->group('/respondents', verifyLogin(), function () use ($app) {
    $app->get('/', function () {
        $data = $_SESSION[User::SESSION];
        $objs = Respondent::getFromOrganization($data["org_id"]);
        $page = new Page();
        $page->setTpl('respondents', array(
            "objs" => $objs,
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
    
    // SAVE CREATE
    $app->post('/create', function () use ($app) {
        $obj = new Respondent();
        $_POST["resp_allowpartial"] = isset($_POST["resp_allowpartial"]) ? 1 : 0;
        $_POST["resp_allowreturn"] = isset($_POST["resp_allowreturn"]) ? 1 : 0;
        $obj->setData($_POST);
        try {
            $obj->saveRespodents();
        } catch (Exception $e) {
            $app->flash('error', $e->getMessage());
            $app->response->redirect('/respondents/create');
        }
        $app->response->redirect('/respondents', 301);
    });
    
    // SAVE UPDATE
    $app->post('/:idobj', function ($idobj) use ($app) {
        $obj = new Respondent();
        $_POST["resp_allowpartial"] = isset($_POST["resp_allowpartial"]) ? 1 : 0;
        $_POST["resp_allowreturn"] = isset($_POST["resp_allowreturn"]) ? 1 : 0;
        $obj->get((int) $idobj);
        $obj->setData($_POST);
        $obj->update();
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
        $objs = Objective::getFromPlan($data["plan_id"]);
        $page = new Page();
        $page->setTpl('objectives', array(
            "objs" => $objs,
            "levels" => Objective::getOrganizationLevelList()
        ));
    });
    
    // CREATE
    $app->get('/create', function () {
        $error = isset($_SESSION['slim.flash']['error']) ? $_SESSION['slim.flash']['error'] : NULL;
        $page = new Page();
        $page->setTpl('objectives-create', array(
            "levels" => Objective::getOrganizationLevelList(),
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
        $obj = new Objective();
        $obj->get((int) $idobj);
        $page = new Page();
        $page->setTpl('objectives-update', array(
            "obj" => $obj->getValues(),
            "levels" => Objective::getOrganizationLevelList()
        ));
    });
    
    // SAVE CREATE
    $app->post('/create', function () use ($app) {
        $obj = new Objective();
        $_POST["resp_allowpartial"] = isset($_POST["resp_allowpartial"]) ? 1 : 0;
        $_POST["resp_allowreturn"] = isset($_POST["resp_allowreturn"]) ? 1 : 0;
        $obj->setData($_POST);
        try {
            $obj->saveObjectives();
        } catch (Exception $e) {
            $app->flash('error', $e->getMessage());
            $app->response->redirect('/objectives/create');
        }
        $app->response->redirect('/objectives', 301);
    });
    
    // SAVE UPDATE
    $app->post('/:idobj', function ($idobj) use ($app) {
        $obj = new Objective();
        $_POST["resp_allowpartial"] = isset($_POST["resp_allowpartial"]) ? 1 : 0;
        $_POST["resp_allowreturn"] = isset($_POST["resp_allowreturn"]) ? 1 : 0;
        $obj->get((int) $idobj);
        $obj->setData($_POST);
        $obj->update();
        $app->response->redirect('/objectives', 301);
    });
});

// #############################################################################################
// ADMIN ROUTES
// #############################################################################################
$app->group('/admin', verifyLoginAdmin(),
    function () use ($app) {
        $app->get('/', function () use ($app) {
            $app->response->redirect('\admin\orgs');
        });
        // #####################################################################################
        // ADMIN ORGANIZATIONS
        // #####################################################################################
        // LIST
        $app->group('/orgs', function () use ($app) {
            $app->get('/', function () {
                $objs = Organization::listAll();
                $page = new Page();
                $page->setTpl('admin\orgs', array(
                    "objs" => $objs
                ));
            });
            
            // CREATE
            $app->get('/create', function () {
                $page = new Page();
                $page->setTpl('admin\orgs-create', array(
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
                $app->response->redirect('\admin\orgs', 301);
            });
            
            // VIEW UPDATE
            $app->get('/:idobj', function ($idobj) {
                $obj = new Organization();
                $obj->get((int) $idobj);
                $page = new Page();
                $page->setTpl('admin\orgs-update', array(
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
                $app->response->redirect('\admin\orgs', 301);
            });
            
            // SAVE UPDATE
            $app->post('/:idobj', function ($idobj) use ($app) {
                $obj = new Organization();
                $_POST["org_notification"] = isset($_POST["org_notification"]) ? 1 : 0;
                $obj->get((int) $idobj);
                $obj->setData($_POST);
                $obj->update();
                $app->response->redirect('\admin\orgs', 301);
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
                $page->setTpl('admin\users', array(
                    "objs" => $objs
                ));
            });
            
            // USER VIEW CREATE
            $app->get('/create', function () {
                $page = new Page();
                $page->setTpl('admin\users-create', array(
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
                $page->setTpl('admin\users-test');
            });
            
            // USER VIEW UPDATE
            $app->get('/:idobj', function ($idobj) {
                $obj = new User();
                $obj->get((int) $idobj);
                $page = new Page();
                $page->setTpl('admin\users-update', array(
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
                $page->setTpl('admin\qsets', array(
                    "objs" => $objs
                ));
            });
            
            // CREATE
            $app->get('/create', function () {
                $page = new Page();
                $page->setTpl('admin\qsets-create');
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
                $page->setTpl('admin\qsets-update', array(
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
                $page->setTpl('admin\perspectives', array(
                    "objs" => $objs
                ));
            });
            
            // CREATE
            $app->get('/create', function () {
                $page = new Page();
                $page->setTpl('admin\perspectives-create');
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
                $page->setTpl('admin\perspectives-update', array(
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
                $page->setTpl('admin\questions', array(
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
                $page->setTpl('admin\questions-update', array(
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