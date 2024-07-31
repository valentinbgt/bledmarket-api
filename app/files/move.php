<?php
    $user->require();

    $api->requieredMethod(Api::POST);

    $api->parameterCheck('fileId', 'destination');

    extract($api->getParameters());

    $result = $db->fetch('files', 'file_public_id', $fileId);

    $destination = $db->fetch('files', 'file_public_id', $destination);

    if(!is_array($destination)) $api->error(29);

    if($destination['file_is_folder'] != 1) $api->error(30);

    $newPath = $destination['file_path'];
    if(!str_ends_with($newPath, '/')) $newPath .= '/';
    $newPath .= $destination['file_public_id'];

    $file = new File();

    if(is_array($result)){
        if($result["user_id"] == $user->id){
            $file->move($fileId, $newPath);
        }
    }

    $api->validRequest();