<?php
    // error handler function
    function customErrorHandler($errno, $errstr, $errfile, $errline) {

        $api = new Api;

        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }

        // $errstr may need to be escaped:
        $errstr = htmlspecialchars($errstr);

        $errfilebase = basename($errfile);

        switch ($errno) {
        case E_USER_ERROR:
            //Erreur fatale (4)
            $api->error(4, "[$errno] $errstr, in file $errfilebase:$errline ($errfile:$errline)");
            exit(1);

        case E_USER_WARNING:
            //Erreur warning (5)
            $api->error(5, "[$errno] $errstr, in file $errfilebase:$errline ($errfile:$errline)");
            break;

        case E_USER_NOTICE:
            //Erreur notice (6)
            $api->error(6, "[$errno] $errstr, in file $errfilebase:$errline ($errfile:$errline)");
            break;

        default:
            //Erreur inconnue (7)
            $api->error(7, "[$errno] $errstr, in file $errfilebase:$errline ($errfile:$errline)");
            break;
        }

        /* Don't execute PHP internal error handler */
        return true;
    }

    set_error_handler("customErrorHandler");