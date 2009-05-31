<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

function balise_FORMS_TEXTE_REPONDU_TITRE ($p) {
  return calculer_balise_dynamique($p,'FORMS_TEXTE_REPONDU_TITRE',array());
}

function balise_FORMS_TEXTE_REPONDU_TITRE_stat($args, $filtres) {
	return $args;
}
function balise_FORMS_TEXTE_REPONDU_TITRE_dyn($valeur='',$texte='',$texte_autres='') {
	
	if (!$GLOBALS['auteur_session']) return $texte_autres;
	$id_auteur=$GLOBALS['auteur_session']['id_auteur'];
	if (sql_countsel(
	  "spip_forms_donnees as donnees,spip_forms as forms",
	  "forms.id_form=donnees.id_form AND forms.titre=".sql_quote($valeur)." AND id_auteur=".intval($id_auteur))
	  )
		return $texte;
	else
		return $texte_autres;
}
?>