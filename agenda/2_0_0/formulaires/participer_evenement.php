<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_participer_evenement_charger_dist($id_evenement){
	$valeurs = array();
	// si pas d'evenement ou d'inscription, on echoue silencieusement
	if (!$row = sql_fetsel('inscription,places','spip_evenements','id_evenement='.intval($id_evenement))
	  OR !$row['inscription'])
		return false;
	$valeurs['id'] = $id_evenement;
	
	// si les places sont comptees, regarder si il en reste
	if ($places = $row['places']){
		$ok = sql_countsel('spip_evenements_participants','id_evenement='.intval($id_evenement)." AND reponse='oui'");
		$peutetre = sql_countsel('spip_evenements_participants','id_evenement='.intval($id_evenement)." AND reponse='?'");
		// Les reponses PEUT-ETRE sont ponderees a 0,5 donc
		// on multiplie tout par 2 pour eviter les troncatures ($total ne sert de toute facon que dans les tests)
		$total = 2*$ok+$peutetre;
		if ($total>=2*$places){
		  // dans ce cas, le formulaire est editable seulement si l'auteur a deja repondu oui ou peut-etre, et peut changer d'avis ! 
		  // on recupere la reponse eventuelle de l'utilisateur que si on en a besoin pour le test (petite optim)
		  $valeurs['reponse'] = sql_getfetsel('reponse','spip_evenements_participants','id_evenement='.intval($id_evenement).' AND id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']));
		  if (!($valeurs['reponse']=='oui' OR $valeurs['reponse']=='?')){
				$valeurs['editable'] = false;
				$valeurs['message_ok'] = _T('agenda:evenement_complet');
			}
		}
	}
	
	return $valeurs;
}

function formulaires_participer_evenement_verifier_dist($id_evenement){
	$erreurs = array();
	$reponse = _request('reponse');
	// Le test de la ligne suivante sert a savoir si la reponse est vide, non?
	// On vient juste de la recuperer ci-dessus, pas la peine de la reaffecter...
	if (!($reponse) OR !in_array($reponse,array('oui','non','?')))
		$erreurs['reponse'] = _T('agenda:indiquez_votre_choix');
	elseif ($reponse!=='non') {
		$row = sql_fetsel('places','spip_evenements','id_evenement='.intval($id_evenement));
		$valeurs['reponse'] = sql_getfetsel('reponse','spip_evenements_participants','id_evenement='.intval($id_evenement).' AND id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']));
		if ($places = $row['places'] AND $valeurs['reponse']!==$reponse){
			$ok = sql_countsel('spip_evenements_participants','id_evenement='.intval($id_evenement)." AND reponse='oui'");
			$peutetre = sql_countsel('spip_evenements_participants','id_evenement='.intval($id_evenement)." AND reponse='?'");
			// Les reponses PEUT-ETRE sont ponderees a 0,5 donc
			// on multiplie tout par 2 pour eviter les troncatures ($total ne sert de toute facon que dans les tests)
			$total = 2*$ok+$peutetre;
			if (
			    // Si on est au taquet, le seul cas autorise restant (la reponse NON et la reponse identique sont prises
			    // en compte dans les tests ci-dessus) est: transformation d'un OUI en PEUT-ETRE (-0,5)
			    ($total>=2*$places AND !($valeurs['reponse']=='oui' AND $reponse=='?'))
			    OR
			    // Si il reste un siege PEUT-ETRE, le seul cas interdit restant est: transformation d'un NON en OUI (+1)
			    ($total==2*$places-1 AND ($valeurs['reponse']=='non' AND $reponse=='oui'))
			    ){
				$erreurs['reponse'] = _T('agenda:plus_de_place');
			}
		}
	}
	return $erreurs;
}

function formulaires_participer_evenement_traiter_dist($id_evenement){
	
	$reponse = _request('reponse');
	if (sql_fetsel('reponse','spip_evenements_participants','id_evenement='.intval($id_evenement).' AND id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']))){
		sql_updateq('spip_evenements_participants',array('reponse'=>$reponse),'id_evenement='.intval($id_evenement).' AND id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']));
	}
	else
		sql_insertq('spip_evenements_participants',array('id_evenement'=>$id_evenement,'id_auteur'=>$GLOBALS['visiteur_session']['id_auteur'],'reponse'=>$reponse,'date'=>'NOW()'));

	$retour = array('editable'=>true);
	if (!$reponse = sql_getfetsel('reponse','spip_evenements_participants','id_evenement='.intval($id_evenement).' AND id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']))
	OR $reponse!=_request('reponse')){
		$retour['message_erreur'] = _T('agenda:probleme_technique');
	}
	else {
		if ($reponse=='oui')
			$message = _T('agenda:participation_prise_en_compte');
		elseif ($reponse=='?')
			$message = _T('agenda:participation_incertaine_prise_en_compte');
		else
			$message = _T('agenda:absence_prise_en_compte');
	}
	return array('message_ok'=>$message,'editable'=>true);
}

?>