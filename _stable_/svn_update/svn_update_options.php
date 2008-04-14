<?php

    if (!defined("_ECRIRE_INC_VERSION")) return;    #securite

    if (!defined(SVN_UPDATE_AUTEURS)) {
        define('_SVN_UPDATE_AUTEURS', implode(";",lire_config('svn_update/administrateurs',array('35'))));
    }
?>
