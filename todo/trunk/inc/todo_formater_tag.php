<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Transforme la chaine représentant le nom du tag en un lien vers la page du mot-clé
 * le cas échéans ou renvoie la chaine fournie en entrée.
 *
 * @param string $valeur
 * 		Le nom du tag qui peut coincinder avec le titre d'un mot-clé
 *
 * @return string
 * 		La valeur formatée en lien vers le mot-clé ou la valeur d'entrée sinon.
 */
function inc_todo_formater_tag_dist($valeur) {
	$tag = $valeur;

	if ($id = sql_getfetsel('id_mot', 'spip_mots', 'titre='. sql_quote($tag))) {
		include_spip('inc/utils');
		$url = generer_url_entite($id, 'mots');
		$tag = '<a href="' . $url . '" class="spip_in">' . $valeur . '</a>';
	}

	return $tag;
}

?>
