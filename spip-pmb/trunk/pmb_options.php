<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// indiquer qu'on peut s'authentifier via une auth PMB
$GLOBALS['liste_des_authentifications']['pmb'] = 'pmb';

$GLOBALS['pmb_statut_nouvel_auteur'] = '6forum';

// ne pas faire planter #URL_x dans une boucle (pmb:n)
$GLOBALS['exception_des_connect'][] = 'pmb';


?>
