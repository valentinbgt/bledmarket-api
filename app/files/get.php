<?php
    $api->requieredMethod(Api::GET);

    $api->parameterCheck("repertory");

    extract($api->getParameters());

    if($repertory == "public"){

    }
    else if($repertory == "private"){

    }