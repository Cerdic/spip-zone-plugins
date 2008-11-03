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

/**
 * Ajouter une css dans l'espace prive
 *
 * @param unknown_type $flux
 * @return unknown
 */
function agenda_header_prive($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('img_pack/datePicker.css').'" media="screen" />'."\n";
	return $flux;
}

function agenda_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	$id_article = $flux['args']['id_article'];
	
	if ($exec=='articles'){
		//on teste si cfg est actif
		$afficher = true;
		//Cette strategie est erronnee : si un article possede des evenements, il *faut* les montrer
		// ou qu'on soit
		/* if (defined('_DIR_PLUGIN_CFG') && (count(lire_config("agenda/rubriques_agenda",' '))>1)) {
			$arracfgrubriques=lire_config("agenda/rubriques_agenda",' ');
			if ($id_article!=''){
				//on cherche la rubrique de l'article
				$id_rubrique = sql_getfetsel("id_rubrique", "spip_articles", "id_article=$id_article");
				//et si la rubrique est dans l'arrayrub
				if ($id_rubrique  AND !in_array($id_rubrique, $arracfgrubriques))
					$afficher = false;
			}
		}*/
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

?>