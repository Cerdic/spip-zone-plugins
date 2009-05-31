<?php

/*******************************************************************
 *
 * Copyright (c) 2007-2008
 * Xavier BUROT
 * fichier : balise/genea_notes.php
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL
 *
 * *******************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// --Balise affichant les notes liees a un individu ---------------------
function balise_GENEA_NOTES($p){
	return calculer_balise_dynamique($p, 'GENEA_NOTES', array('id_individu'));
}

function balise_GENEA_NOTES_stat($args, $filtres){
	return $args;
}

function balise_GENEA_NOTES_dyn($args, $filtres){
    return array('formulaires/genea_notes', 0, array('id_individu'));
}
?>