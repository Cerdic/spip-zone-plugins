<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function inscription2_ajouts(){
	global $connect_id_auteur, $spip_display;

	$id_auteur = intval(_request('id_auteur'));
	$nom_table = "spip_auteurs_elargis";
	$redirect = _request('redirect');
	$echec = _request('echec');
	$new = _request('new');

	$auteur = sql_getfetsel("id_auteur","".$nom_table."","id_auteur=$id_auteur");

	if (!$auteur AND !$new) {
		sql_insertq($nom_table, array('id_auteur'=>$id_auteur));
		$auteur = sql_fetsel("id_auteur",$nom_table,"id_auteur=$id_auteur");
	}

	$legender_auteur_supp = charger_fonction('legender_auteur_supp', 'inc');
	$fiche = $legender_auteur_supp($auteur, $new, $echec, $redirect);
	
	return $fiche;
}
?>