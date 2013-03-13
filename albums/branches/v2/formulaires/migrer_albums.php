<?php
/**
 * Plugin albums 4 pour Spip 3.0
 * Licence GPL 3
 *
 * 2006-2011
 * Auteurs : cf paquet.xml
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_migrer_albums_charger_dist(){

	$valeurs = array(

		'id_parent'=>'',
		'toute_la_branche' => '',
		'groupes_mots' => array(),
		'refuser_articles' => '',
	);

	return $valeurs;
}

function formulaires_migrer_albums_verifier_dist(){

	$erreurs = array();
	$oblis = array('id_parent');

	foreach ($oblis as $obli){
		if (!_request($obli))
			$erreurs[$obli] = _T('info_obligatoire');
	}

	if (!isset($erreurs['groupes_mots'])
	  AND $groupes = _request('groupes_mots')){

		if (!is_array($groupes))
			$erreurs['groupes_mots'] = _T('migreralbums:erreur_choix_incorrect');
		else {
			$groupes = array_map('intval',$groupes);
			if (sql_countsel('spip_groupes_mots',sql_in('id_groupe',$groupes))!=count($groupes))
				$erreurs['groupes_mots'] = _T('migreralbums:erreur_choix_incorrect');
		}
	}

	// pas d'erreurs ? verifier ce qui va etre fait et l'annoncer
	if (!count($erreurs) AND !_request('confirm')){
		$where = migrer_albums_where_articles(_request('id_parent'),_request('toute_la_branche'));
		$nba = sql_countsel("spip_articles",$where);
		$erreurs['nb'] = $nba;

		$message = sinon(singulier_ou_pluriel($nba,'info_1_article','info_nb_articles'),_T('info_aucun_article'));
		$message .= " " ._T('migreralbums:info_migration_articles');

		$erreurs['confirmer'] = $message;
	}

	return $erreurs;
}


function formulaires_migrer_albums_traiter_dist(){
	$id_rubrique = _request('id_parent');
	$where_articles = migrer_albums_where_articles($id_rubrique,_request('toute_la_branche'));
	$refuser = (_request('refuser_articles')?true:false);
	$groupes = _request('groupes_mots');
	if (!$groupes)
		$groupes = array();
	$where_mots = migrer_albums_where_mots($groupes);

	// et migrer les articles
	$nb = albums_migrer_articles($where_articles, $where_mots, $refuser);

	$message = sinon(singulier_ou_pluriel($nb,'info_1_article','info_nb_articles'),_T('info_aucun_article'));
	$message .= " " ._T('migreralbums:info_migration_articles_reussi');

	return array('message_ok'=>$message);
}



function albums_migrer_articles($where_articles, $where_mots, $refuser){
	include_spip("action/editer_objet");
	include_spip("action/editer_liens");

	$where_mots = implode(" AND ",$where_mots);

	$nb = 0;
	$res = sql_select("*","spip_articles",$where_articles);
	while ($row = sql_fetch($res)){
		// y a-t-il deja un album associe ?
		$liens = objet_trouver_liens(array('album'=>'*'),array('article'=>$row['id_article']));
		if (!count($liens)
		  AND $id_album = objet_inserer('album')){

			// associer tout de suite
			objet_associer(array('album'=>$id_album),array('article'=>$row['id_article']));

			// titrer et decrire
			$descriptif = array();
			if (strlen($row['chapo']))
				$descriptif[] = $row['chapo'];
			if (strlen($row['texte']))
				$descriptif[] = $row['texte'];
			if (strlen($row['ps']))
				$descriptif[] = $row['ps'];
			$descriptif = implode("\n\n",$descriptif);

			$set = array(
				'titre' => $row['titre'],
				'descriptif' => $descriptif,
			);

			objet_modifier("album",$id_album,$set);

			// ajouter les documents : en sql pour ne pas exploser si plein de docs en base
			$docs = sql_allfetsel('D.id_document','spip_documents AS D JOIN spip_documents_liens AS L ON (D.id_document=L.id_document AND L.objet='.sql_quote('article').')',"id_objet=".intval($row['id_article']));

			if (count($docs)){
				$insert = array();
				foreach ($docs as $doc){
					$insert[] = array('id_document'=>$doc['id_document'],'objet'=>'album','id_objet'=>$id_album);
				}
				sql_insertq_multi("spip_documents_liens",$insert);
			}

			// associer les mots : en sql pour ne pas exploser si plein de mots en base
			$mots = sql_allfetsel('M.id_mot','spip_mots AS M JOIN spip_mots_liens AS L ON (M.id_mot=L.id_mot AND L.objet='.sql_quote('article').')',"id_objet=".intval($row['id_article'])." AND (".$where_mots.")");
			if (count($mots)){
				$insert = array();
				foreach ($mots as $mot){
					$insert[] = array('id_mot'=>$mot['id_mot'],'objet'=>'album','id_objet'=>$id_album);
				}
				sql_insertq_multi("spip_mots_liens",$insert);
			}

			// publier l'album
			objet_modifier('album',$id_album,array('date'=>$row['date'],'statut'=>'publie'));

			$nb++;
		}

		// refuser l'article si option demandee
		// meme si c'est un article migre un coup avant
		if (count($liens) OR $id_album){
			if ($refuser){
				objet_modifier('article',$row['id_article'],array('statut'=>'refuse'));
			}
		}

	}

	return $nb;
}



function migrer_albums_where_articles($id_rubrique,$branche = false){

	$where = array();
	$where[] = "statut=".sql_quote('publie');
	if ($branche){
		include_spip("inc/rubriques");
		$where[] = sql_in('id_rubrique',calcul_branche_in($id_rubrique));
	}
	else
		$where[] = "id_rubrique=".intval($id_rubrique);

	return $where;
}

function migrer_albums_where_mots($groupes){
	$id_groupe = array();

	$rows = sql_allfetsel('*','spip_groupes_mots',sql_in('id_groupe',$groupes));
	foreach($rows as $row){
		$id_groupe[] = $row['id_groupe'];
		$tables_liees = explode(',',$row['tables_liees']);
		$tables_liees = array_filter($tables_liees);
		// ajouter les evenements a ce groupe de mot
		if (!in_array('albums',$tables_liees)){
			include_spip("action/editer_groupe_mots");
			$tables_liees[] = 'albums';
			$tables_liees = implode(',',$tables_liees);
			groupemots_modifier($row['id_groupe'],array('tables_liees'=>$tables_liees));
		}
	}

	$where = array(sql_in('id_groupe',$id_groupe));

	return $where;
}

?>
