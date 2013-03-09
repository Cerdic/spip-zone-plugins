<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function seo_autoriser(){}

function autoriser_seo_bouton_dist($faire, $type, $id,  $qui, $opt){
	global $connect_statut, $connect_toutes_rubriques;

	// seul les administrateurs globaux ont acces au bouton de configuration
	return $connect_statut 	&& $connect_toutes_rubriques;
}
?>