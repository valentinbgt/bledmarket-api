<?php
    $api->requieredMethod(Api::POST);

    $api->parameterCheck("login");
    $api->parameterCheck("password");

    extract($api->getParameters());

    $user->login($login, $password);