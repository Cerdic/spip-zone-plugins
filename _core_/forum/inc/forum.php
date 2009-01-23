<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');

// recuperer le critere SQL qui selectionne nos forums
// http://doc.spip.org/@critere_statut_controle_forum
function critere_statut_controle_forum($type, $id_rubrique=0, $recherche='') {

	if (is_array($id_rubrique))   $id_rubrique = join(',',$id_rubrique);
	if (!$id_rubrique) {
		$from = 'spip_forum AS F';
		$where = "";
		$and = "";
	} else {
		if (strpos($id_rubrique,','))
		  $eq = " IN ($id_rubrique)";
		else $eq = "=$id_rubrique";
	      
		$from = 'spip_forum AS F, spip_articles AS A';
		$where = "A.id_secteur$eq AND F.id_article=A.id_article";
		$and = ' AND ';
	}
   
	switch ($type) {
	case 'public':
		$and .= "F.statut IN ('publie', 'off', 'prop', 'spam') AND F.texte!=''";
		break;
	case 'prop':
		$and .= "F.statut='prop'";
		break;
	case 'spam':
		$and .= "F.statut='spam'";
		break;
	case 'interne':
		$and .= "F.statut IN ('prive', 'privrac', 'privoff', 'privadm') AND F.texte!=''";
		break;
	case 'vide':
		$and .= "F.statut IN ('publie', 'off', 'prive', 'privrac', 'privoff', 'privadm') AND F.texte=''";
		break;
	default:
		$where = '0=1';
		$and ='';
		break;
	}

	if ($recherche) {
		include_spip('inc/rechercher');
		if ($a = recherche_en_base($recherche, 'forum'))
			$and .= " AND ".sql_in('id_forum',
				array_keys(array_pop($a)));
		else
			$and .= " 0=1";
	}

	return array($from, "$where$and");
}

// Index d'invalidation des forums
// http://doc.spip.org/@calcul_index_forum
function calcul_index_forum($id_article, $id_breve, $id_rubrique, $id_syndic) {
	if ($id_article) return 'a'.$id_article; 
	if ($id_breve) return 'b'.$id_breve;
	if ($id_rubrique) return 'r'.$id_rubrique;
	if ($id_syndic) return 's'.$id_syndic;
}

//
// Recalculer tous les threads
//
// http://doc.spip.org/@calculer_threads
function calculer_threads() {
	// fixer les id_thread des debuts de discussion
	sql_update('spip_forum', array('id_thread'=>'id_forum'), "id_parent=0");
	// reparer les messages qui n'ont pas l'id_secteur de leur parent
	do {
		$discussion = "0";
		$precedent = 0;
		$r = sql_select("fille.id_forum AS id,	maman.id_thread AS thread", 'spip_forum AS fille, spip_forum AS maman', "fille.id_parent = maman.id_forum AND fille.id_thread <> maman.id_thread",'', "thread");
		while ($row = sql_fetch($r)) {
			if ($row['thread'] == $precedent)
				$discussion .= "," . $row['id'];
			else {
				if ($precedent)
					sql_updateq("spip_forum", array("id_thread" => $precedent), "id_forum IN ($discussion)");
				$precedent = $row['thread'];
				$discussion = $row['id'];
			}
		}
		sql_updateq("spip_forum", array("id_thread" => $precedent), "id_forum IN ($discussion)");
	} while ($discussion != "0");
}

// Calculs des URLs des forums (pour l'espace public)
// http://doc.spip.org/@racine_forum
function racine_forum($id_forum){
	if (!$id_forum = intval($id_forum)) return false;

	$row = sql_fetsel("id_parent, id_rubrique, id_article, id_breve, id_syndic, id_message, id_thread", "spip_forum", "id_forum=".$id_forum);

	if (!$row) return false;

	if ($row['id_parent']
	AND $row['id_thread'] != $id_forum) // eviter boucle infinie
		return racine_forum($row['id_thread']);

	if ($row['id_message'])
		return array('message', $row['id_message'], $id_forum);
	if ($row['id_rubrique'])
		return array('rubrique', $row['id_rubrique'], $id_forum);
	if ($row['id_article'])
		return array('article', $row['id_article'], $id_forum);
	if ($row['id_breve'])
		return array('breve', $row['id_breve'], $id_forum);
	if ($row['id_syndic'])
		return array('site', $row['id_syndic'], $id_forum);

	// On ne devrait jamais arriver ici, mais prevoir des cas de forums
	// poses sur autre chose que les objets prevus...
	spip_log("erreur racine_forum $id_forum");
	return array();
} 


// http://doc.spip.org/@parent_forum
function parent_forum($id_forum) {
	if (!$id_forum = intval($id_forum)) return;
	$row = sql_fetsel("id_parent, id_rubrique, id_article, id_breve, id_syndic", "spip_forum", "id_forum=".$id_forum);
	if(!$row) return array();
	if($row['id_parent']) return array('forum', $row['id_parent']);
	if($row['id_article']) return array('article', $row['id_article']);
	if($row['id_breve']) return array('breve', $row['id_breve']);
	if($row['id_rubrique']) return array('rubrique', $row['id_rubrique']);
	if($row['id_syndic']) return array('site', $row['id_syndic']);
} 


/**
 * Pour compatibilite : remplacer les appels par son contenu
 *
 * @param unknown_type $id_forum
 * @param unknown_type $args
 * @param unknown_type $ancre
 * @return unknown
 */
function generer_url_forum_dist($id_forum, $args='', $ancre='') {
	$generer_url_forum = charger_fonction('generer_url_forum','urls');
	return $generer_url_forum($id, $args, $ancre);
}


// http://doc.spip.org/@generer_url_forum_parent
function generer_url_forum_parent($id_forum) {
	if ($id_forum = intval($id_forum)) {
		list($type, $id) = parent_forum($id_forum);
		if ($type)
			return generer_url_entite($id, $type);
	}
	return '';
} 


// Quand on edite un forum, on tient a conserver l'original
// sous forme d'un forum en reponse, de statut 'original'
// http://doc.spip.org/@conserver_original
function conserver_original($id_forum) {
	$s = sql_fetsel("id_forum", "spip_forum", "id_parent=".sql_quote($id_forum)." AND statut='original'");

	if ($s)	return ''; // pas d'erreur

	// recopier le forum
	$t = sql_fetsel("*", "spip_forum", "id_forum=".sql_quote($id_forum));

	if ($t) {
		unset($t['id_forum']);
		$id_copie = sql_insertq('spip_forum', $t);
		if ($id_copie) {
			sql_updateq('spip_forum', array('id_parent'=> $id_forum, 'statut'=>'original'), "id_forum=$id_copie");
			return ''; // pas d'erreur
		}
	}

	return '&erreur';
}

// appelle conserver_original(), puis modifie le contenu via l'API inc/modifier
// http://doc.spip.org/@enregistre_et_modifie_forum
function enregistre_et_modifie_forum($id_forum, $c=false) {
	if ($err = conserver_original($id_forum)) {
		spip_log("erreur de sauvegarde de l'original, $err");
		return;
	}

	include_spip('action/editer_forum');
	return revision_forum($id_forum, $c);
}


?>