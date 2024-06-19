<?php
    require_once('../Settings/constants.php');

    require_once(PROJECT_ROOT . 'Class/BledMarket/Api.php');
    $api = new Api;

    require_once(PROJECT_ROOT . 'app/rooter.php');

    
    header('Content-Type: application/json');


    $api->reply();