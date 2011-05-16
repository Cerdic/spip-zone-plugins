<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 *
 *
 */

function action_editer_evenement_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_article n'est pas un nombre, c'est une creation
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_evenement = intval($arg)) {
		$id_parent = _request('id_parent');
		if (!$id_evenement = agenda_action_insert_evenement($id_parent))
			return array(false,_L('echec'));
	}

	$err = action_evenement_set($id_evenement);
	return array($id_evenement,$err);
}

function action_evenement_set($id_evenement, $set=null){
	$err = '';

	if (is_null($set)){
		$c = array();
		foreach (array(
			'titre', 'descriptif', 'lieu', 'id_parent',
			'inscription','places','adresse',
		) as $champ)
			$c[$champ] = _request($champ);

		$c['horaire'] = _request('horaire')=='non'?'non':'oui';
		include_spip('inc/date_gestion');
		$date_debut = verifier_corriger_date_saisie('debut',$c['horaire']=='oui',$erreurs);
		$date_fin = verifier_corriger_date_saisie('fin',$c['horaire']=='oui',$erreurs);

		$c['date_debut'] = date('Y-m-d H:i:s',$date_debut);
		$c['date_fin'] = date('Y-m-d H:i:s',$date_fin);
	}
	else
		$c = $set;

	include_spip('inc/modifier');
	agenda_action_revision_evenement($id_evenement, $c);
	agenda_action_revision_evenement_mots($id_evenement, _request('mots',$set));
	agenda_action_revision_evenement_repetitions($id_evenement,_request('repetitions',$set), _request('mots',$set));

	// Modification de statut, changement de rubrique ?
	$c = array();
	foreach (array(
		'id_parent'
	) as $champ)
		$c[$champ] = _request($champ,$set);
	$err .= agenda_action_instituer_evenement($id_evenement, $c);

	return $err;
}

function agenda_action_revision_evenement_repetitions($id_evenement,$repetitions="",$liste_mots=array()){
	include_spip('inc/filtres');
	$repetitions = preg_split(",[^0-9\-\/],",$repetitions);
	// gestion des repetitions
	$rep = array();
	foreach($repetitions as $date){
		if (strlen($date)){
			$date = recup_date($date);
			if ($date=mktime(0,0,0,$date[1],$date[2],$date[0]))
				$rep[] = $date;
		}
	}
	agenda_action_update_repetitions($id_evenement, $rep, $liste_mots);
}

function agenda_action_update_repetitions($id_evenement,$repetitions,$liste_mots){
	// evenement source
	if ($row = sql_fetsel("*", "spip_evenements","id_evenement=".intval($id_evenement))){
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$horaire = $row['horaire'];
		$lieu = $row['lieu'];
		$adresse = $row['adresse'];
		$date_debut = strtotime($row['date_debut']);
		$date_fin = strtotime($row['date_fin']);
		$duree = $date_fin - $date_debut;
		$id_evenement_source = $row['id_evenement_source'];
		$id_article = $row['id_article'];
		$inscription = $row['inscription'];
		$places = $row['places'];
		if ($id_evenement_source!=0)
			return; // pas un evenement source donc rien a faire ici

		$repetitions_updated = array();
		// mettre a jour toutes les repetitions deja existantes ou les supprimer si plus lieu
		$res = sql_select("id_evenement,date_debut","spip_evenements","id_evenement_source=".sql_quote($id_evenement));
		while ($row = sql_fetch($res)){
			$date = strtotime(date('Y-m-d',strtotime($row['date_debut'])));
			if (in_array($date,$repetitions)){
				// il est maintenu, on l'update
				$repetitions_updated[] = $date;
				$update_date_debut = date('Y-m-d',$date)." ".date('H:i:s',$date_debut);
				$update_date_fin = date('Y-m-d H:i:s',strtotime($update_date_debut)+$duree);

				// TODO : prendre en charge la mise a jour uniquement si conforme a l'original
				$update_titre = $titre;
				$update_descriptif = $descriptif;
				$update_lieu = $lieu;
				$update_adresse = $adresse;
				$update_inscription = $inscription;
				$update_places = $places;

				// mettre a jour l'evenement
				sql_updateq('spip_evenements',
					array(
						"titre" => $update_titre,
						"descriptif" => $update_descriptif,
						"lieu" => $update_lieu,
						"adresse" => $update_adresse,
						"horaire" => $horaire,
						"date_debut" => $update_date_debut,
						"date_fin" => $update_date_fin,
						"inscription" => $update_inscription,
						"places" => $update_places,
						"id_article" => $id_article),"id_evenement=".intval($row['id_evenement']));

				agenda_action_revision_evenement_mots($row['id_evenement'], $liste_mots);
			}
			else {
				// il est supprime
				sql_delete("spip_mots_evenements","id_evenement=".$row['id_evenement']);
				sql_delete("spip_evenements","id_evenement=".$row['id_evenement']);
			}

		}
		// regarder les repetitions a ajouter
		foreach($repetitions as $date){
			if (!in_array($date,$repetitions_updated)){
				$update_date_debut = date('Y-m-d',$date)." ".date('H:i:s',$date_debut);
				$update_date_fin = date('Y-m-d H:i:s',strtotime($update_date_debut)+$duree);
				$update_titre = $titre;
				$update_descriptif = $descriptif;
				$update_lieu = $lieu;
				$update_adresse = $adresse;
				$update_inscription = $inscription;
				$update_places = $places;

				if ($id_evenement_new = agenda_action_insert_evenement($id_article,$id_evenement)) {
					// mettre a jour l'evenement
					sql_updateq('spip_evenements',
						array(
							"titre" => $update_titre,
							"descriptif" => $update_descriptif,
							"lieu" => $update_lieu,
							"adresse" => $update_adresse,
							"horaire" => $horaire,
							"date_debut" => $update_date_debut,
							"date_fin" => $update_date_fin,
							"inscription" => $update_inscription,
							"places" => $update_places,
							"id_article" => $id_article),"id_evenement=".intval($id_evenement_new));
					agenda_action_revision_evenement_mots($id_evenement_new, $liste_mots);
				}
			}
		}
	}
}


function agenda_action_revision_evenement_mots($id_evenement,$liste_mots){
	// suppression des mots obsoletes
	$cond_in  = $cond_not_in = "";
	if (count($liste_mots)){
		$cond_not_in = sql_in('id_mot', join(',',$liste_mots), 'NOT');
		$cond_in = sql_in('id_mot', join(',',$liste_mots));
	}
	sql_delete("spip_mots_evenements", "id_evenement=".intval($id_evenement) . ($cond_in?" AND ".$cond_not_in:""));

	$liste_deja = array();
	if ($cond_in)
		$liste_deja = sql_allfetsel("id_mot", "spip_mots_evenements", "id_evenement=".intval($id_evenement) . " AND $cond_in");
	if (count($liste_deja)) {
		$cond_not_in = sql_in('id_mot', join(',',array_map('reset',$liste_deja)), 'NOT');
		$liste_mots = sql_allfetsel('id_mot','spip_mots',"$cond_in AND $cond_not_in");
		$liste_mots = array_map('reset',$liste_mots);
	}
	if (count($liste_mots)) {
		$ins = array();
		foreach($liste_mots as $k=>$id_mot)
			$ins[] = array('id_evenement'=>$id_evenement,'id_mot'=>$id_mot);
		sql_insertq_multi("spip_mots_evenements", $ins);
	}
}

// creer un nouvel evenement
function agenda_action_insert_evenement($id_article,$id_evenement_source = 0){
	include_spip('inc/autoriser');
	if (!autoriser('creerevenementdans','article',$id_article)){
		spip_log("agenda action formulaire article : auteur ".$GLOBALS['visiteur_session']['id_auteur']." n'a pas le droit de creer un evenement dans article $id_article",'agenda');
		return false;
	}

	// nouvel evenement
	$id_evenement = sql_insertq("spip_evenements", array("id_evenement_source"=>intval($id_evenement_source), "maj"=>date("Y-m-d H:i:s"), 'id_article'=>intval($id_article)));

	if (!$id_evenement){
		spip_log("agenda action formulaire article : impossible d'ajouter un evenement",'agenda');
		return false;
	}
	return $id_evenement;
}

// Enregistre une revision d'evenement
function agenda_action_revision_evenement ($id_evenement, $c=false) {

	if ($c['id_parent']) {
		// Si l'article est publie, invalider les caches et demander sa reindexation
		$t = sql_getfetsel("statut", "spip_articles", "id_article=".intval($c['id_parent']));
		if ($t == 'publie') {
			$invalideur = "id='id_article/$id_article'";
			$indexation = true;
		}
	}

	modifier_contenu('evenement', $id_evenement,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation
		),
		$c);

	return ''; // pas d'erreur
}


// $c est un array ('statut', 'id_parent' = changement de rubrique)
function agenda_action_instituer_evenement($id_evenement, $c) {

	include_spip('inc/autoriser');
	include_spip('inc/modifier');

	$row = sql_fetsel("id_article", "spip_evenements", "id_evenement=".intval($id_evenement));
	$id_parent = $row['id_article'];
	$champs = array();

	if (!autoriser('modifier', 'article', $id_parent)
	  OR (!autoriser('modifier', 'article', $c['id_parent']))){
		spip_log("editer_evenement $id_evenement refus " . join(' ', $c));
		return false;
	}

	// Verifier que la rubrique demandee existe et est differente
	// de la rubrique actuelle
	if ($c['id_parent']
	AND $c['id_parent'] != $id_parent
	AND (sql_fetsel('1', "spip_articles", "id_article=".intval($c['id_parent'])))) {
		$champs['id_article'] = $c['id_parent'];
	}

	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_evenements',
				'id_objet' => $id_evenement
			),
			'data' => $champs
		)
	);

	if (!count($champs)) return;

	// Envoyer les modifs sur l'evenement et toutes ses repetitons
	$ids = array_map('reset',sql_allfetsel('id_evenement','spip_evenements','id_evenement_source='.intval($id_evenement)));
	$ids[] = intval($id_evenement);
	sql_updateq('spip_evenements',$champs,sql_in('id_evenement',$ids));

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_article/$id_parent'");
	suivre_invalideur("id='id_article/".$champs['id_article']."'");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_evenements',
				'id_objet' => $id_evenement
			),
			'data' => $champs
		)
	);

	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituerevenement', $id_evenement,
			array('id_parent' => $champs['id_article'], 'id_parent_ancien' => $id_parent)
		);
	}

	return ''; // pas d'erreur
}


function agenda_action_supprime_repetitions($supp_evenement){
	$res = sql_select("id_evenement", "spip_evenements", "id_evenement_source=".intval($supp_evenement));
	while ($row = sql_fetch($res)){
		$id_evenement = $row['id_evenement'];
		sql_delete("spip_mots_evenements", "id_evenement=".intval($id_evenement));
		sql_delete("spip_evenements", "id_evenement=".intval($id_evenement));
	}
}

function agenda_action_supprime_evenement($id_article,$supp_evenement){
	$id_evenement = sql_getfetsel("id_evenement", "spip_evenements", array(
		"id_article=" . intval($id_article),
		"id_evenement=" . intval($supp_evenement)));
	if (intval($id_evenement) AND $id_evenement == $supp_evenement){
		sql_delete("spip_mots_evenements", "id_evenement=".intval($id_evenement));
		sql_delete("spip_evenements", "id_evenement=".intval($id_evenement));
		agenda_action_supprime_repetitions($id_evenement);
	}
	include_spip('inc/invalideur');
	suivre_invalideur("article/$id_article");
	$id_evenement = 0;
	return $id_evenement;
}

?>