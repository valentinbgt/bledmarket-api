<?php
    $user->require();

    $api->requieredMethod(Api::POST);

    $api->parameterCheck("fileId");

    extract($api->getParameters());

    $result = $db->fetch('files', 'file_public_id', $fileId);

    $file = new File();

    if(is_array($result)){
        if($result["user_id"] == $user->id){
            $file->delete($fileId);
        }
    }

    $api->validRequest();   