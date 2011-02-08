<?php

function agenda_ajouter_onglets($flux) {
	if($flux['args']=='calendrier' AND !defined('_DIR_PLUGIN_BANDO')){
		$flux['data']['agenda']= new Bouton(
														 _DIR_PLUGIN_AGENDA.'/img_pack/agenda-24.png', _T('agenda:agenda'),
														generer_url_ecrire("calendrier","type=semaine"));
		$flux['data']['calendrier']= new Bouton(
													 'cal-rv.png', _T('agenda:activite_editoriale'),
													 generer_url_ecrire("calendrier","mode=editorial&type=semaine"));
	}
	return $flux;
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
			include_spip('inc/agenda_gestion');
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
			$flux['data'] .= "<div class='verdana2'><img src='".find_in_path("img_pack/agenda$statut.png")."' class='agenda-statut' alt='' />$res<div class='nettoyeur'></div></div>";
	}
	elseif ($exec=='articles'){
		$id_article = $flux['args']['id_article'];
		$afficher = autoriser('creerevenementdans','article',$id_article);
		if ($afficher) {
			$contexte = array();
			foreach($_GET as $key=>$val)
				$contexte[$key] = $val;
			 $evenements = recuperer_fond('prive/contenu/evenements_article',$contexte);
			 $flux['data'] .= $evenements;
		}
	}
	elseif ($exec=='mots_edit'){
		$id_mot = $flux['args']['id_mot'];
		foreach($_GET as $key=>$val)
			$contexte[$key] = $val;
	 $evenements = recuperer_fond('prive/contenu/agenda_evenements',$contexte);
	 $flux['data'] .= $evenements;
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

function agenda_afficher_nombre_objets_associes_a($flux){
	if ($flux['args']['objet']=='mot'
	  AND $id_mot=$flux['args']['id_objet']){
		$aff_articles = sql_in('A.statut',  ($GLOBALS['connect_statut'] =="0minirezo")  ? array('prepa','prop','publie') : array('prop','publie'));
		$nb = sql_countsel("spip_mots_evenements AS L LEFT JOIN spip_evenements AS E ON E.id_evenement=L.id_evenement LEFT JOIN spip_articles AS A ON E.id_article=A.id_article", "L.id_mot=".intval($id_mot)." AND $aff_articles");
		if ($nb)
			$flux['data'][] = singulier_ou_pluriel($nb, "agenda:info_un_evenement", "agenda:info_nombre_evenements");
	}
	return $flux;
}
function agenda_rechercher_liste_des_champs($tables){
	$tables['evenement'] = array(
				'titre' => 8, 
				'descriptif' => 5,
				'lieu' => 5,
				'adresse' => 3			
			);
	return $tables;
}
/**
 * Declarer evenement comme un objet interpretable dans les url
 * ?evenement12
 * 
 * @param array $objets
 * @return array
 */
function agenda_declarer_url_objets($objets){
	$objets[] = 'evenement';
	return $objets;
}
?>
