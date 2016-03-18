<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Des champs par défaut pour identite extra
if (!isset($GLOBALS['identite_extra']))
	$GLOBALS['identite_extra'] = array('nom_organisation','telephone', 'adresse', 'ville', 'code_postal','region','pays');

