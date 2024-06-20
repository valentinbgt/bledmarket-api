<?php
    session_start();

    require_once('../Includes/constants.inc.php');

    require_once(PROJECT_ROOT . 'Class/BledMarket/Api.php');
    $api = new Api;

    require_once(PROJECT_ROOT . 'Includes/errorHandler.inc.php');

    if(!file_exists(PROJECT_ROOT . 'Conf/conf.inc.php')) $api->error(3, "Le fichier de configuration de l'application est introuvable. Utilisez /Conf/conf.inc.sample.php pour en créer un.");
    require_once(PROJECT_ROOT . 'Conf/conf.inc.php');

    require_once(PROJECT_ROOT . 'Class/BledMarket/Database.php');
    // $db = new Database();

    require_once(PROJECT_ROOT . 'Class/BledMarket/User.php');
    $user = new User();

    require_once(PROJECT_ROOT . 'app/rooter.php');


    $api->reply();