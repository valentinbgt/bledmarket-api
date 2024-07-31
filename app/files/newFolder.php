<?php
    $user->require();

    $api->requieredMethod(Api::POST);

    $api->parameterCheck('repertory', 'path');

    extract($api->getParameters());

    $api->checkRepertory($repertory);
    if($repertory == "public") $user->checkPublicUploadAllowed();

    $file = new File();

    $result = $file->newFolder($repertory, $path);

    if($result){
        $api->addToResponse('folder_id', $result);
        $api->validRequest();
    }else{
        $api->error(26);
    };


