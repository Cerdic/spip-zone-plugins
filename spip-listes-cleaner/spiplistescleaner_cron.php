<?php

function spiplistescleaner_taches_generales_cron($taches_generales)
{
    $taches_generales['spiplistescleaner_cron'] = 3600; // Every hours
    return $taches_generales;
}
