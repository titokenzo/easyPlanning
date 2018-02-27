<?php
namespace easyPlanning;

use easyPlanning\Model\User;
use easyPlanning\Model\Respondent;

class SysRouter{
    static function goRespondentCreate($error=NULL){
        User::verifyLogin();
        $page = new Page();
        $page->setTpl('respondents-create', array(
            "levels" => Respondent::getOrganizationLevelList(),
            "error" => $error
        ));
    }
}
?>