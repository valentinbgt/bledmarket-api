<?php
    $route = explode('/', $_SERVER['REQUEST_URI']);

    if(!empty($route[1])){
        $page = $route[1];

        $action = "index";
        if(!empty($route[2])) $action = $route[2];

        $scriptPath = __DIR__ . "/$page/$action.php";

        if(file_exists($scriptPath)){
            
            require($scriptPath);

        }else{
            $api->error(9, "La solution demandée est introuvable.");
        }
    }