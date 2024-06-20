<?php
    $api->requieredMethod(Api::GET);

    $api->parameterCheck("repertory");

    extract($api->getParameters());

    $file = new File();

    $fileList = $file->get($user, $repertory);

    $api->addToResponse("fileList", $fileList);
    $api->validRequest();