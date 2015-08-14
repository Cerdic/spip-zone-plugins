<?php
/**
 * Fonctions utiles au plugin Thèmes CLIL
 *
 * @plugin     Thèmes CLIL
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Clil\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Nombre de sous-thèmes pour un secteur
 * si aucun sous-thèmes, retourne False
 * 
 * @id_secteur Int
 * @return mixed
 */
function clil_themes_nombre_sous_themes($id_secteur){
	if (($res = sql_countsel('spip_clil_themes', "id_secteur=$id_secteur AND id_clil_theme <> $id_secteur")) > 0)
		return $res;
	else return false; 
}

/**
 * Nombre de sous-thèmes sélectionés pour un secteur
 * si aucun sous-thèmes sélectionnés, retourne False
 * 
 * @id_secteur Int
 * @return mixed
 */
function clil_themes_checked($id_secteur) {
	if (($res = sql_countsel('spip_clil_themes', "id_secteur=$id_secteur AND id_clil_theme <> $id_secteur AND tag='oui'")) > 0)
		return $res;
	else return false; 
}

?>