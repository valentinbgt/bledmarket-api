<?php
    //if file public OK

    //if file shared OK

    //if owner OK

    //else no


    $api->requieredMethod(Api::GET);

    $api->parameterCheck('fileKey');

    extract($api->getParameters());

    $file = new File();

    $display = isset($display);

    $file->download($fileKey, $display);

    //PAS FINIIIIII
    