<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS['marqueur_skel'] = (isset($GLOBALS['marqueur_skel'])?$GLOBALS['marqueur_skel']:'').':archives';
