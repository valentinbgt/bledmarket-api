<?php
    $api->requieredMethod(Api::POST);

    $api->parameterCheck('current_password', 'new_password');

    extract($api->getParameters());

    $api->error(2, "This feature is in development.");