<?php
/**
 * Plugin mesfavoris
 * (c) 2009-2012 Olivier Sallou, Cedric Morin
 * Distribue sous licence GPL
 *
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function mesfavoris_autoriser(){}

function autoriser_favori_modifier_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut'] == '0minirezo' AND !$qui['restreint'])
		return true;
	else{
		$auteur_favori = sql_getfetsel('id_auteur','spip_favoris','id_favori='.intval($id));
		return ($qui['id_auteur'] == $auteur_favori);
	}
}

?>