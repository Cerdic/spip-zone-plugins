<?php
/**
 * @name 		Vider poubelle
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Actions
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_vider_poubelle_pubban(){
	include_spip('base/abstract_sql');
	$resultat1 = sql_select("id_publicite", 'spip_publicites', "statut='5poubelle'", '', '', '', '');
	if (sql_count($resultat1) > 0) {
		while ($row = spip_fetch_array($resultat1)) {
			$id_supp = $row['id_publicite'];
			if (intval($id_supp)) {
				sql_delete('spip_publicites', "id_publicite=".$id_supp);
				sql_delete('spip_bannieres_publicites', "id_publicite=".$id_supp);
			}
		}
	}
	$resultat2 = sql_select("id_banniere", 'spip_bannieres', "statut='5poubelle'", '', '', '', '');
	if (sql_count($resultat2) > 0) {
		while ($row = spip_fetch_array($resultat2)) {
			$id_supp = $row['id_banniere'];
			if (intval($id_supp)) {
				sql_delete('spip_bannieres', "id_banniere=".$id_supp);
				sql_delete('spip_bannieres_publicites', "id_banniere=".$id_supp);
			}
		}
	}
	return;
}
?>