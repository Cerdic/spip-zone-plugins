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

		@sql_updateq("spip_forum", array("id_article" => $id_nouvart), "id_thread=$id_forum");

		break;
	case "fermer":
		spipbb_invalider_page($row); // invalider les pages comportant ce forum
		// il faudrait peut être tester avant pour eviter le risque d'erreur de duplicate key
		$spipbb_meta=@unserialize($GLOBALS['meta']['spipbb']);
		@sql_insertq("spip_mots_forum", array("id_forum" => $id_forum,"id_mot"=>$spipbb_meta['id_mot_ferme']));
		break;
	case "ouvrir":
		spipbb_invalider_page($row); // invalider les pages comportant ce forum
		$spipbb_meta=@unserialize($GLOBALS['meta']['spipbb']);
		$result = sql_select("*", "spip_mots_forum", array("id_forum=$id_forum","id_mot=".$spipbb_meta['id_mot_ferme']) );
		if ($rowo = sql_fetch($result)) {
			$reqd=sql_delete("spip_mots_forum","id_forum=$id_forum AND id_mot=".$spipbb_meta['id_mot_ferme']);
		} else {
			spipbb_log("clef non trouvee ouvrir : $id_forum AND id_mot=".$spipbb_meta['id_mot_ferme'],3,"action_spipbb_agir_forum");
		}
		break;
	case "diviser":
		$liste = _request('liste'); // on deplace tous les posts marques
		$suite = _request('suite'); // on demarre apres le post marque
		$sel_id = _request('sel_id'); // liste des posts selectionnes

		if ((!$liste AND !$suite) OR !is_array($sel_id)) redirige_par_entete($r);
		if ($suite AND count($sel_id)>1) redirige_par_entete($r);

		// on identifie le forum(article) dans lequel on le deplace et on verifie son existence
		$id_nouvart = _request('nouveau_forum');
		$result = sql_select("*", "spip_articles", "id_article=$id_nouvart");
		if (!($rowart = sql_fetch($result))) return;

		// on cherche le premier post selectionne et on verifie son existence
		$in_liste_id=sql_in('id_forum',$sel_id);

		if ( !$premier = sql_fetsel("*", 'spip_forum', $in_liste_id,'','id_forum') ) return;

		spipbb_invalider_page($row); // invalider les pages comportant ce forum

		// le premier post va devenir un nouveau parent
		@sql_updateq("spip_forum", array("id_article" => $id_nouvart,
									"id_parent"=>0,
									"id_thread"=>$premier['id_forum']),
					"id_forum=".$premier['id_forum']);

		if ($liste) {
			$where_enfants=array($in_liste_id, "id_parent=$id_forum");
			$where_forum=array($in_liste_id, "id_parent!=$id_forum");
		}
		if ($suite) {
			$where_enfants=array("id_forum>".$premier['id_forum'], "id_parent=$id_forum");
			$where_forum=array("id_forum>".$premier['id_forum'], "id_parent!=$id_forum","id_thread=$id_forum");
		}
		// on regreffe les enfants qui étaient rattachés à l'ancien forum sur le nouveau parent
		@sql_updateq("spip_forum", array("id_article" => $id_nouvart,
								"id_parent"=>$premier['id_forum'],
								"id_thread"=>$premier['id_forum']),
				$where_enfants);
		// on rattache les autres messages sélectionnés au forum tout simplement
		@sql_updateq("spip_forum", array("id_article" => $id_nouvart,
								"id_thread"=>$premier['id_forum']),
				$where_forum);

		// Une fois la greffe faite on devrait aussi dupliquer les stats de vues
		// car ces messages ont été vus en même temps que les autres...
		$stats=sql_fetsel("SUM(visites) AS total",'spip_visites_forums',"id_forum=$id_forum");
		@sql_insertq("spip_visites_forums", array("date"=>"NOW()","id_forum" => $premier['id_forum'],"visites"=>$stats['total'],"maj"=>"NOW()"));

		// on invalide aussi le forum d'accueil !
		$nouv_premier = sql_fetsel("*", 'spip_forum', "id_forum=".$premier['id_forum']);
		spipbb_invalider_page($nouv_premier); // invalider les pages comportant ce forum

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
