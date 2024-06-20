<?php
    header('Content-Type: application/json');

    require_once('../Settings/constants.php');

    require_once(PROJECT_ROOT . 'Class/BledMarket/Api.php');
    $api = new Api;

    if(!file_exists(PROJECT_ROOT . 'Conf/conf.inc.php')) $api->error(4, "Le fichier de configuration de l'application est introuvable. Utilisez /Conf/conf.inc.sample.php pour en créer un.");
    require_once(PROJECT_ROOT . 'Conf/conf.inc.php');

    require_once(PROJECT_ROOT . 'app/rooter.php');

    $api->reply();