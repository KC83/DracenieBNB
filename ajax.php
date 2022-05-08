<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

require "class/DB/DB.php";
require "class/DataList/DataList.php";
require "class/Renderer/Renderer.php";

require "inc/config.php";
require "inc/fonctions.php";

$do = $_REQUEST['do'] ?? '';
$action = $_REQUEST['action'] ?? '';

$page = "";
if (!empty($do)) {
	$page .= $do."/";
}
if (!empty($action)) {
	$page .= $action.".php";
}

if (empty($page)) {
	$page = "php/accueil.php";
}

if (isset($_SESSION[getSessionName()]['utilisateurId'])) {
	if (file_exists($page)) {
		require $page;
	} else {
		echo '<div class="alert alert-danger text-center">En cours de développement !</div>';
	}
} else {
    echo '<div class="alert alert-danger text-center">L\'utilisateur n\'est pas connecté !</div>';
}

exit();