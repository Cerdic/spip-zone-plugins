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
	$resultat1 = sql_select("id_pub", $GLOBALS['_PUBBAN_CONF']['table_pub'], "statut='5poubelle'", '', '', '', '', _BDD_PUBBAN);
	if (sql_count($resultat1) > 0) {
		while ($row = spip_fetch_array($resultat1)) {
			$id_supp = $row['id_pub'];
			if (intval($id_supp)) {
				sql_delete($GLOBALS['_PUBBAN_CONF']['table_pub'], "id_pub=".$id_supp, _BDD_PUBBAN);
				sql_delete($GLOBALS['_PUBBAN_CONF']['table_join'], "id_pub=".$id_supp, _BDD_PUBBAN);
			}
		}
	}
	$resultat2 = sql_select("id_empl", $GLOBALS['_PUBBAN_CONF']['table_empl'], "statut='5poubelle'", '', '', '', '', _BDD_PUBBAN);
	if (sql_count($resultat2) > 0) {
		while ($row = spip_fetch_array($resultat2)) {
			$id_supp = $row['id_empl'];
			if (intval($id_supp)) {
				sql_delete($GLOBALS['_PUBBAN_CONF']['table_empl'], "id_empl=".$id_supp, _BDD_PUBBAN);
				sql_delete($GLOBALS['_PUBBAN_CONF']['table_join'], "id_empl=".$id_supp, _BDD_PUBBAN);
			}
		}
	}
	return;
}
?>