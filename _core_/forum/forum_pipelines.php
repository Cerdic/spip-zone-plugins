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


/**
 * Bloc sur les encours editoriaux en page d'accueil
 *
 * @param string $texte
 * @return string
 */
function forum_accueil_encours($texte){
	// si aucun autre objet n'est a valider, on ne dit rien sur les forum
	if ($GLOBALS['visiteur_session']['statut'] == '0minirezo') {
		// Les forums en attente de moderation
		$cpt = sql_countsel("spip_forum", "statut='prop'");
		if ($cpt) {
			if ($cpt>1)
				$lien = _T('info_liens_syndiques_3')." "._T('info_liens_syndiques_4');
			else
				$lien = _T('info_liens_syndiques_5')." "._T('info_liens_syndiques_6');
			$lien = "<small>$cpt $lien " ._T('info_liens_syndiques_7'). "</small>";
			if ($GLOBALS['connect_toutes_rubriques'])
				$lien = "<a href='" . generer_url_ecrire("controle_forum","statut=prop") . "' style='color: black;'>". $lien . ".</a>";
			$texte .= "\n<br />" . $lien;
		}
		if (strlen($texte) AND $GLOBALS['meta']['forum_prive_objets'] != 'non')
			$cpt2 = sql_countsel("spip_articles", "statut='prop'");
			if ($cpt2)
				$texte = _T('texte_en_cours_validation_forum') . $texte;
	}

	return $texte;
}


/**
 * Bloc sur les informations generales concernant chaque type d'objet
 *
 * @param string $texte
 * @return string
 */
function forum_accueil_informations($texte){
	include_spip('base/abstract_sql');
	$q = sql_select('COUNT(*) AS cnt, statut', 'spip_forum', sql_in('statut', array('publie', 'prop')), 'statut', '','', "COUNT(*)<>0");

	$where = count($GLOBALS['connect_id_rubrique']) ? sql_in('id_rubrique', $GLOBALS['connect_id_rubrique'])	: '';
	$cpt = array();
	$cpt2 = array();
	$defaut = $where ? '0/' : '';
	while($row = sql_fetch($q)) {
	  $cpt[$row['statut']] = $row['cnt'];
	  $cpt2[$row['statut']] = $defaut;
	}

	if ($cpt) {
		if ($where) {
		  include_spip('inc/forum');
		  list($f, $w) = critere_statut_controle_forum('public');
		  $q = sql_select("COUNT(*) AS cnt, F.statut", "$f", "$w ", "F.statut");
		  while($row = sql_fetch($q)) {
				$r = $row['statut'];
				$cpt2[$r] = intval($row['cnt']) . '/';
			}
		}

		$texte .= "<div class='accueil_informations forum verdana1'>";
		if (autoriser('modererforum'))
			$texte .= afficher_plus(generer_url_ecrire("controle_forum",""));
		$texte .= "<b>" ._T('onglet_messages_publics') ."</b>";
		$texte .= "<ul style='margin:0px; padding-".$GLOBALS['spip_lang_left'].": 20px; margin-bottom: 5px;'>";
		if (isset($cpt['prop'])) $texte .= "<li>"._T("texte_statut_attente_validation").": ".$cpt2['prop'] .$cpt['prop'] . '</li>';
		if (isset($cpt['publie'])) $texte .= "<li><b>"._T("texte_statut_publies").": ".$cpt2['publie'] .$cpt['publie'] . "</b>" .'</li>';
		$texte .= "</ul>";
		$texte .= "</div>";
	}
	return $texte;
}

/**
 * Affichage de la fiche complete des articles et rubriques
 *
 * @param array $flux
 * @return array
 */
function forum_afficher_fiche_objet($flux){
	if ($type = $flux['args']['type']=='article'){
		$id = $flux['args']['id'];
		$table = table_objet($type);
		$id_table_objet = id_table_objet($type);
		$discuter = charger_fonction('discuter', 'inc');
		$flux['data'] .= $discuter($id, $table, $id_table_objet, 'prive', _request('debut'));
	}
	if ($type = $flux['args']['type']=='rubrique'){
		$id_rubrique = $flux['args']['id'];
		if (autoriser('publierdans','rubrique',$id_rubrique) 
		  AND !sql_getfetsel('id_parent','spip_rubriques','id_rubrique='.intval($id_rubrique))) {
			include_spip('inc/forum');
			list($from, $where) = critere_statut_controle_forum('prop', $id_rubrique);
			$n_forums = sql_countsel($from, $where);
		}
		else
			$n_forums = 0;
		if ($n_forums)
	  	$flux['data'] .= icone_inline(_T('icone_suivi_forum', array('nb_forums' => $n_forums)), generer_url_ecrire("controle_forum","id_rubrique=$id_rubrique"), "suivi-forum-24.gif", "", 'center');
	}
	return $flux;
}

/**
 * Boite de configuration des objets articles
 *
 * @param array $flux
 * @return array
 */
function forum_afficher_config_objet($flux){
	if (($type = $flux['args']['type'])=='article'){
		$id = $flux['args']['id'];
		if (autoriser('modererforum', $type, $id)) {
			$table = table_objet($type);
			$id_table_objet = id_table_objet($type);		
			$flux['data'] .= recuperer_fond("prive/configurer/moderation",array($id_table_objet=>$id));
		}
	}
	return $flux;
}

/**
 * Message d'information relatif au statut d'un objet
 *
 * @param array $flux
 * @return array
 */
function forum_afficher_message_statut_objet($flux){
	if ($type = $flux['args']['type']=='article'){
		$statut = $flux['args']['statut'];
		if ($GLOBALS['meta']['forum_prive_objets'] != 'non'
		  AND $statut == 'prop')
			$flux['data'] .= "<p class='article_prop'>"._T('text_article_propose_publication_forum').'</p>';
	}
	return $flux;
}

/**
 * Nombre de forums d'un secteur dans la boite d'info
 *
 * @param array $flux
 * @return array
 */
function forum_boite_infos($flux){
	if ($type = $flux['args']['type']=='rubrique'){
		if (autoriser('publierdans','rubrique',$id_rubrique) AND !$flux['args']['row']['id_parent']) {
			include_spip('inc/forum');
			list($from, $where) = critere_statut_controle_forum('prop', $id_rubrique);
			$n_forums = sql_countsel($from, $where);
		}
		else
			$n_forums = 0;
		if ($n_forums){
			$aff = "<p class='forums'>"._T('icone_suivi_forum',array('nb_forums'=>$n_forums)).'</p>';
			if (($pos = strpos($flux['data'],'<!--nb_elements-->'))!==FALSE)
				$flux['data'] = substr($flux['data'],0,$pos) . $aff . substr($flux['data'],$pos);
			else
				$flux['data'] .= $aff;
		}
	}
	return $flux;
}

/**
 * Compter et afficher les contributions d'un visiteur
 * pour affichage dans la page auteurs
 *
 * @param array $flux
 * @return array
 */
function forum_compter_contributions_visiteur($flux){
	$id_auteur = intval($flux['args']['id_auteur']);
	if ($cpt = sql_countsel("spip_forum AS F", "F.id_auteur=".intval($flux['args']['id_auteur']))){
		// manque "1 message de forum"
		$contributions = ($cpt>1) ? $cpt.' '._T('public:messages_forum'):'1 '._T('public:message');
		$flux['data'] .= ($flux['data']?", ":"").$contributions;
	}
	return $flux;
}

/**
 * Definir les meta de configuration liee aux forums
 *
 * @param array $metas
 * @return array
 */
function forum_configurer_liste_metas($metas){
	$metas['mots_cles_forums'] =  'non';
	$metas['forums_titre'] = 'oui';
	$metas['forums_texte'] = 'oui';
	$metas['forums_urlref'] = 'non';
	$metas['forums_afficher_barre'] = 'oui';
	$metas['formats_documents_forum'] = '';
	$metas['forums_publics'] = 'posteriori';
	$metas['forum_prive'] = 'oui'; # forum global dans l'espace prive
	$metas['forum_prive_objets'] = 'oui'; # forum sous chaque article de l'espace prive
	$metas['forum_prive_admin'] = 'non'; # forum des administrateurs

	return $metas;
}

/**
 * Declarer le surnom des forums
 *
 * @param array $table
 * @return array
 */
function forum_declarer_tables_objets_surnoms($table){
	$table['forum'] = 'forums'; # hum hum redevient spip_forum par table_objet_sql mais casse par un bete "spip_".table_objet()
	return $table;
}



/**
 * Optimiser la base de donnee en supprimant les forums orphelins
 *
 * @param int $n
 * @return int
 */
function forum_optimiser_base_disparus($n){
	# les forums lies a une id_rubrique inexistante
	$res = sql_select("forum.id_forum AS id",
			"spip_forum AS forum
		        LEFT JOIN spip_rubriques AS rubriques
		          ON forum.id_rubrique=rubriques.id_rubrique",
			"rubriques.id_rubrique IS NULL
		         AND forum.id_rubrique>0");

	$n+= optimiser_sansref('spip_forum', 'id_forum', $res);
	

	# les forums lies a des articles effaces
	$res = sql_select("forum.id_forum AS id",
		        "spip_forum AS forum
		        LEFT JOIN spip_articles AS articles
		          ON forum.id_article=articles.id_article",
			"articles.id_article IS NULL
		         AND forum.id_article>0");

	$n+= optimiser_sansref('spip_forum', 'id_forum', $res);
	
	# les forums lies a des breves effacees
	$res = sql_select("forum.id_forum AS id",
		        "spip_forum AS forum
		        LEFT JOIN spip_breves AS breves
		          ON forum.id_breve=breves.id_breve",
			"breves.id_breve IS NULL
		         AND forum.id_breve>0");

	$n+= optimiser_sansref('spip_forum', 'id_forum', $res);
	

	# les forums lies a des sites effaces
	$res = sql_select("forum.id_forum AS id",
		        "spip_forum AS forum
		        LEFT JOIN spip_syndic AS syndic
		          ON forum.id_syndic=syndic.id_syndic",
			"syndic.id_syndic IS NULL
		         AND forum.id_syndic>0");

	$n+= optimiser_sansref('spip_forum', 'id_forum', $res);


	# les liens mots-forum sur des mots effaces
	$res = sql_select("mots_forum.id_mot AS id",
		        "spip_mots_forum AS mots_forum
		        LEFT JOIN spip_mots AS mots
		          ON mots_forum.id_mot=mots.id_mot",
			"mots.id_mot IS NULL");

	$n+= optimiser_sansref('spip_mots_forum', 'id_mot', $res);
	
	//
	// Forums
	//

	sql_delete("spip_forum", "statut='redac' AND maj < $mydate");


	# les liens mots-forum sur des forums effaces
	$res = sql_select("mots_forum.id_forum AS id",
		        "spip_mots_forum AS mots_forum
		        LEFT JOIN spip_forum AS forum
		          ON mots_forum.id_forum=forum.id_forum",
			"forum.id_forum IS NULL");

	$n+= optimiser_sansref('spip_mots_forum', 'id_forum', $res);
	
	return $n;
}

/**
 * Remplissage des champs a la creation d'objet
 *
 * @param array $flux
 * @return array
 */
function forum_pre_insertion($flux){
	if ($flux['args']['table']=='spip_articles'){
		$flux['args']['data']['accepter_forum'] =	substr($GLOBALS['meta']['forums_publics'],0,3);
	}
	return $flux;
}

/**
 * Regrouper les resultats de recherche par threads
 *
 * @param array $flux
 * @return array
 */
function forum_prepare_recherche($flux){
	# Pour les forums, unifier par id_thread et forcer statut='publie'
	if ($flux['args']['type']=='forum'
	  AND $points = $flux['data']){
	  $serveur =  $flux['args']['serveur'];
		$p2 = array();
		$s = sql_select("id_thread, id_forum", "spip_forum", "statut='publie' AND ".sql_in('id_forum', array_keys($points)), '','','','',$serveur);
		while ($t = sql_fetch($s, $serveur))
			$p2[intval($t['id_thread'])]['score']
				+= $points[intval($t['id_forum'])]['score'];
		$flux['data'] = $p2;
	}
	return $flux;
}


/**
 * Definir la liste des champs de recherche sur la table forum
 *
 * @param array $liste
 * @return array
 */
function forum_rechercher_liste_des_champs($liste){
	$liste['forum'] = array(
	  'titre' => 3, 'texte' => 1, 'auteur' => 2, 'email_auteur' => 2, 'nom_site' => 1, 'url_site' => 1
	);
	
	return $liste;
}

/**
 * Bloc en sur les encours d'une rubrique (page naviguer)
 *
 * @param array $flux
 * @return array
 */
function forum_rubrique_encours($flux){
	if (strlen($flux['data'])
	  AND $GLOBALS['meta']['forum_prive_objets'] != 'non')
		$flux['data'] = _T('texte_en_cours_validation_forum') . $flux['data'];
	return $flux;
}

/**
 * Supprimer les forums lies aux objets du core lors de leur suppression
 *
 * @param array $objets
 * @return array
 */
function forum_supprimer_objets_lies($objets){
	foreach($objets as $objet){
		if ($objet['type']=='message')
			sql_delete("spip_forum", "id_message=".sql_quote($objet['id']));
		if ($objet['type']=='mot')
			sql_delete("spip_mots_forum", "id_mot=".intval($objet['id']));
	}
	return $objets;
}

?>