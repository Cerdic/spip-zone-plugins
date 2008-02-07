<?php

/*******************************************************************
 *
 * Copyright (c) 2008
 * Xavier BUROT
 * fichier : inc/genea_autoriser
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL
 *
 * *******************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function autoriser_genea_voir($faire, $type, $id, $qui, $opt){
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

function autoriser_genea_voirfiche($faire, $type, $id, $qui, $opt){
	return $qui['statut'] == '0minirezo' OR $qui['statut'] == '1comite';
}

?>