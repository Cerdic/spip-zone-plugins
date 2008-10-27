<?php
/**
 * Plugin Portfolio/Gestion des documents
 * Licence GPL (c) 2006-2008 Cedric Morin, romy.tetue.net
 *
 */

/**
 * Afficher un message "une truc"/"N trucs"
 *
 * @param int $nb
 * @return string
 */
function gestdoc_affiche_un_ou_plusieurs($nb,$chaine_un,$chaine_plusieurs,$var='nb'){
	if (!$nb=intval($nb)) return "";
	if ($nb>1) return _T($chaine_plusieurs, array($var => $nb));
	else return _T($chaine_un);
}

?>