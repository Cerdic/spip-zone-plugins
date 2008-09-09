<?php

function agenda_ajouter_onglets($flux) {
	if($flux['args']=='calendrier'){
		$flux['data']['evenements']= new Bouton(
														 _DIR_PLUGIN_AGENDA.'/img_pack/agenda-24.png', _T('agenda:evenements'),
														generer_url_ecrire("calendrier","type=semaine"));
		$flux['data']['editorial']= new Bouton(
													 'cal-rv.png', _T('agenda:activite_editoriale'),
													 generer_url_ecrire("calendrier","mode=editorial&type=semaine"));
	}
	return $flux;
}

/**
 * Ajouter une css dans l'espace prive
 *
 * @param unknown_type $flux
 * @return unknown
 */
function agenda_header_prive($flux) {
	if (isset($flux['args']['exec'])
	  AND in_array($flux['args']['exec'],array('calendrier'))){
		$flux['data'] .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('img_pack/agenda.css').'" media="screen" />'."\n";
  }
	return $flux;
}

function agenda_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	$id_article = $flux['args']['id_article'];
	
	if ($exec=='articles'){
		//on teste si cfg est actif
		$afficher = true;
		if (defined('_DIR_PLUGIN_CFG') && (count(lire_config("agenda/rubriques_agenda",' '))>1)) {
			$arracfgrubriques=lire_config("agenda/rubriques_agenda",' ');
			if ($id_article!=''){
				//on cherche la rubrique de l'article
				$id_rubrique = sql_getfetsel("id_rubrique", "spip_articles", "id_article=$id_article");
				//et si la rubrique est dans l'arrayrub
				if ($id_rubrique  AND !in_array($id_rubrique, $arracfgrubriques))
					$afficher = false;
			}
		}
		if ($afficher) {
			$contexte = array('id_article'=>$id_article,
			'id_evenement'=>_request('id_evenement'),
			'id_evenement_edit'=>_request('id_evenement_edit'));
			$page = evaluer_fond('prive/contenu/evenements_article',$contexte);
			$flux['data'] .= $page['texte'];
		}
	}
	return $flux;
}

function agenda_taches_generales_cron($taches_generales){
	$taches_generales['agenda_nettoyer_base'] = 3600*48;
	return $taches_generales;
}
?>