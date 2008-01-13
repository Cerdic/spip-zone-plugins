<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : action/spipbb_agir_forum                      #
#  Authors : chryjs, 2008                                  #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs¡@!free¡.!fr                            #
#----------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

// inspire de ecrire/action/configurer.php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

function action_spipbb_agir_forum() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$r = rawurldecode(_request('redirect'));

	list($id_forum, $agir) = preg_split('/\W/', $arg);
	$id_forum = intval($id_forum);
	$result = sql_select("*", "spip_forum", "id_forum=$id_forum");
	if (!($row = sql_fetch($result)))
		return;

	switch ($agir) {
	case "deplacer" :
		$confirme = _request('confirme');
		$annule = _request('annule');

		if (!$confirme) redirige_par_entete($r);

		spipbb_invalider_page($row); // invalider les pages comportant ce forum

		// on identifie le forum(article) dans lequel on le deplace et on verifie son existence
		$id_nouvart = _request('nouveau_forum');
		$result = sql_select("*", "spip_articles", "id_article=$id_nouvart");
		if (!($rowart = sql_fetch($result)))
			return;

		sql_updateq("spip_forum", array("id_article" => $id_nouvart), "id_thread=$id_forum");
		break;
	case "fermer":
		spipbb_invalider_page($row); // invalider les pages comportant ce forum
		// il faudrait peut être tester avant pour eviter le risque d'erreur de duplicate key
		$spipbb_meta=@unserialize($GLOBALS['meta']['spipbb']);
		sql_insertq("spip_mots_forum", array("id_forum" => $id_forum,"id_mot"=>$spipbb_meta['id_mot_ferme']));
		break;
	case "ouvrir":
		spipbb_invalider_page($row); // invalider les pages comportant ce forum
		$spipbb_meta=@unserialize($GLOBALS['meta']['spipbb']);
		$result = sql_select("*", "spip_mots_forum", array("id_forum=$id_forum","id_mot=".$spipbb_meta['id_mot_ferme']) );
		if ($rowo = sql_fetch($result)) {
			$reqd=sql_query("DELETE from spip_mots_forum WHERE id_forum=$id_forum AND id_mot=".$spipbb_meta['id_mot_ferme']);
		} else {
			spipbb_log("clef non trouvee ouvrir : $id_forum AND id_mot=".$spipbb_meta['id_mot_ferme'],3,"action_spipbb_agir_forum");
		}
		break;
	} // switch (agir)

	redirige_par_entete($r);
} // action_spipbb_agir_forum

function spipbb_invalider_page($row) {
	// invalider les pages comportant ce forum
	include_spip('inc/invalideur');
	include_spip('inc/forum');
	$index_forum = calcul_index_forum($row['id_article'], $row['id_breve'], $row['id_rubrique'], $row['id_syndic']);
	suivre_invalideur("id='id_forum/$index_forum'");
} // spipbb_invalider_page

?>
