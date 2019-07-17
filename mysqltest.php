<?php
$servidor = "easyplanning.mysql.uhserver.com";
$usuario = "easyplanning_us";
$banco = "easyplanning";
$senha = "3@syP@55f0rMy5q1";
//Não Alterar abaixo:
$conmysql = mysql_connect($servidor.":3306",$usuario,$senha);
$db = mysql_select_db($banco, $conmysql);
if ($conmysql && $db){
echo "Parabens!! A conexão ao banco de dados ocorreu normalmente!";
} else {
echo "Nao foi possivel conectar ao banco MYSQL";
}
?>