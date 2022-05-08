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

?>

<html lang="fr">
	<head>
		<title>Dracénie BnB</title>
            <!-- Required meta tags -->
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

            <!-- LOGO -->
            <link rel="icon" href="img/logo.png">

            <!-- CSS -->
            <link rel="stylesheet" href="plugins/font-awesome/css/all.min.css">
            <link rel="stylesheet" href="css/bootstrap.css">
            <link rel="stylesheet" href="css/jquery-ui.css">
            <link rel="stylesheet" href="css/style.css">

            <!-- JS -->
            <script src="js/jquery.js"></script>
            <script src="js/jquery-ui.js"></script>
            <script src="js/popper.js"></script>
            <script src="js/bootstrap.js"></script>
	</head>
    <body>
        <script>
            $(function () {
                initializeJsFunction();
            })
        </script>

        <?php if (isset($_SESSION[getSessionName()]['utilisateurId'])) { ?>
            <?php include_once "php/header.php"; ?>
                <section id="main" class="">
                    <?php
                    if (file_exists($page)) {
                        require $page;
                    } else {
                        echo '<div class="alert alert-danger text-center">En cours de développement !</div>';
                    }
                    ?>
                </section>
                <?php include_once "php/footer.php"; ?>
       <?php } else { ?>
            <div id="main">
                <?php
                require "php/auth.php";
                ?>
            </div>
       <?php } ?>

        <!-- JS -->
        <script src="js/functions.js"></script>
    </body>
</html>
