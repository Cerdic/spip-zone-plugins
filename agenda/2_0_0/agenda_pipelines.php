<?php

function agenda_ajouter_onglets($flux) {
	if($flux['args']=='calendrier'){
		$flux['data']['agenda']= new Bouton(
														 _DIR_PLUGIN_AGENDA.'/img_pack/agenda-24.png', _T('agenda:agenda'),
														generer_url_ecrire("calendrier","type=semaine"));
		$flux['data']['calendrier']= new Bouton(
													 'cal-rv.png', _T('agenda:activite_editoriale'),
													 generer_url_ecrire("calendrier","mode=editorial&type=semaine"));
	}
	return $flux;
}

// Calcul d'une hierarchie
// (liste des id_rubrique contenants une rubrique donnee)
function calcul_hierarchie_in($id) {

	// normaliser $id qui a pu arriver comme un array
	$id = is_array($id)
		? join(',', array_map('sql_quote', $id))
		: $id;

	// Notre branche commence par la rubrique de depart
	$hier = $id;

	// On ajoute une generation (les filles de la generation precedente)
	// jusqu'a epuisement
	while ($parents = sql_allfetsel('id_parent', 'spip_rubriques',
	sql_in('id_rubrique', $id))) {
		$id = join(',', array_map('reset', $parents));
		$hier .= ',' . $id;
	}

	return $hier;
}

function agenda_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	
	if ($exec=='naviguer'
	  AND $id_rubrique = intval($flux['args']['id_rubrique'])){
		$activer = true;
		$res = "";
		$actif = sql_getfetsel('agenda','spip_rubriques','id_rubrique='.intval($id_rubrique));
		$statut="-48";
		$voir = "";
		if (!sql_countsel('spip_rubriques','agenda=1'))
			$res .= _T('agenda:aucune_rubrique_mode_agenda').'<br />';
		else {
			if (sql_countsel('spip_rubriques',sql_in('id_rubrique',calcul_hierarchie_in($id_rubrique))." AND agenda=1 AND id_rubrique<>".intval($id_rubrique))){
				$res .= _T('agenda:rubrique_dans_une_rubrique_mode_agenda').'<br />';
				$activer = false;
				$statut="-ok-48";
				$voir = _T('agenda:voir_evenements_rubrique');
			}
			elseif(!$actif) {
				$res .= _T('agenda:rubrique_sans_gestion_evenement').'<br />';
				$statut="-non-24";
			}
			if ($actif){
				$res .= _T('agenda:rubrique_mode_agenda').'<br />';
				$statut="-ok-48";
				$voir = _T('agenda:voir_evenements_rubrique');
			}
		}

		if (!$actif){
			if($activer){
				$res .= "<a href='".generer_action_auteur('rubrique_activer_agenda',$id_rubrique,self())."'>"._T('agenda:rubrique_activer_agenda').'</a>';
			}
		}
		else
			$res .= "<a href='".generer_action_auteur('rubrique_activer_agenda',"-$id_rubrique",self())."'>"._T('agenda:rubrique_desactiver_agenda').'</a>';
		if ($voir)
			$res .= "<p><a href='".generer_url_ecrire('calendrier',"id_rubrique=$id_rubrique")."'>$voir</a></p>";
		if ($res)
			$flux['data'] .= "<div class='verdana2'><img src='".find_in_path("img_pack/agenda$statut.png")."' class='agenda-statut' />$res<div class='nettoyeur'></div></div>";
	}
	if ($exec=='articles'){
		$id_article = $flux['args']['id_article'];
		$afficher = true;
		// un article avec des evenements a toujours le bloc
		if (!sql_countsel('spip_evenements','id_article='.intval($id_article))){
			// si au moins une rubrique a le flag agenda
			if (sql_countsel('spip_rubriques','agenda=1')){
				// alors il faut le flag agenda dans cette branche !
				$afficher = false;
				include_spip('inc/rubriques');
				$in = calcul_hierarchie_in(sql_getfetsel('id_rubrique','spip_articles','id_article='.intval($id_article)));
				$afficher = sql_countsel('spip_rubriques',sql_in('id_rubrique',$in)." AND agenda=1");
			}
		}
		if ($afficher) {
			$contexte = array();
			foreach($_GET as $key=>$val)
				$contexte[$key] = $val;
			 $evenements = recuperer_fond('prive/contenu/evenements_article',$contexte);
			 $flux['data'] .= $evenements;
		}
	}
	return $flux;
}

function agenda_taches_generales_cron($taches_generales){
	$taches_generales['agenda_nettoyer_base'] = 3600*48;
	return $taches_generales;
}

function agenda_editer_contenu_objet($flux){
	if ($flux['args']['type']=='groupe_mot'){
		// ajouter l'input sur les evenements
		$checked = in_array('evenements',$flux['args']['contexte']['tables_liees']);
		$checked = $checked?" checked='checked'":'';
		$input = "<div class='choix'><input type='checkbox' class='checkbox' name='tables_liees&#91;&#93;' value='evenements'$checked id='evenements' /><label for='evenements'>"._T('agenda:item_mots_cles_association_evenements')."</label></div>";
		$flux['data'] = str_replace('<!--choix_tables-->',"$input\n<!--choix_tables-->",$flux['data']);
	}
	return $flux;
}

function agenda_libelle_association_mots($libelles){
	$libelles['evenements'] = 'agenda:info_evenements';
	return $libelles;
}


function agenda_objets_extensibles($objets){
		return array_merge($objets, array('evenement' => _T('agenda:evenements')));
}

?>
