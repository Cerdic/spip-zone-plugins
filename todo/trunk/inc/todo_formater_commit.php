<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Transforme la chaine représentant le numéro du commit en chaine formatée pour la Zone ou le Core
 * le cas échéans ou renvoie la chaine fournie en entrée.
 * Pour la Zone ou le Core on renvoie un lien vers le log de commit associé.
 *
 * @param string $valeur
 * 		Le numéro du commit sous la forme z11111 ou c22222 ou une chaine quelconque
 * @param string &$info
 *		L'information typée mise à jour avec la valeur formatée pour la Zone ou le Core
 * 		ou la valeur d'entrée..
 *
 * @return void
 *
 */
function inc_todo_formater_commit_dist($valeur, &$info) {
	$commit = $valeur;

	if (preg_match('#^(z|c)([0-9]+)$#Uis', $valeur, $m)) {
		if ($m[1] == 'z')
			$href = 'http://zone.spip.org/trac/spip-zone/changeset/' . $m[2];
		else
			$href = 'http://core.spip.org/projects/spip/repository/revisions/' . $m[2];
		$commit = '<a class="spip_out" rel="external" href="' . $href . '">' . $m[2] . '</a>';
	}

	$info .= !$info ? $commit : ', ' . $commit;
}

?>
