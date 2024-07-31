<?php
    $user->require();

    $api->requieredMethod(Api::POST);

    $api->parameterCheck('fileId', 'newName');

    extract($api->getParameters());

    $result = $db->fetch('files', 'file_public_id', $fileId);

    if(is_array($result)){
        $file = new File();

        if($result["user_id"] == $user->id){
            if($file->rename($fileId, $newName)){
                $api->validRequest();
            };
        }
    }else{
        $api->error(28);
    }