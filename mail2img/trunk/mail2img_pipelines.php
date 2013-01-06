<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

    function mail2img_taches_generales_cron($taches){
        $taches['mail2img'] =  60; // toutes les minutes / tous les jours en période hors concours
        return $taches;
    }

?>