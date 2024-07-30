<?php
    $user->require();

    $api->requieredMethod(Api::POST);

    $api->parameterCheck('fileId', 'destination');

    extract($api->getParameters);

    $result = $db->fetch('files', 'file_public_id', $fileId);

    $file = new File();

    if(is_array($result)){
        if($result["user_id"] == $user->id){
            $file->move($fileId, $destination);
        }
    }

    //$api->validRequest();