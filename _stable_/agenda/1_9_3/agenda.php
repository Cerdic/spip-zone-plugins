<?php

function Agenda_ajouter_onglets($flux) {
	if($flux['args']=='calendrier'){
		$flux['data']['evenements']= new Bouton(
														 '../'._DIR_PLUGIN_AGENDA.'/img_pack/agenda-24.png', _T('agenda:evenements'),
														generer_url_ecrire("calendrier","type=semaine"));
		$flux['data']['editorial']= new Bouton(
													 'cal-rv.png', _T('agenda:activite_editoriale'),
													 generer_url_ecrire("calendrier","mode=editorial&type=semaine"));
	}
	return $flux;
}
function Agenda_header_prive($flux) {
/*	$exec = _request('exec');
	// les CSS
	if ($exec == 'calendrier'){
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA . '/img_pack/calendrier.css" type="text/css" />'. "\n";
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA . '/img_pack/agenda.css" type="text/css" />'. "\n";
	}
	if ($exec == 'articles'){
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA . '/img_pack/agenda_articles.css" type="text/css" />'. "\n";
	}*/
	return $flux;
}

function Agenda_affiche_milieu($flux) {
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
			$flux .= $page['texte'];
		}
	}
	return $flux;
}

function Agenda_rendu_boite($titre,$descriptif,$lieu,$type='ics'){
	$texte = "<span class='calendrier-verdana10'><span  style='font-weight: bold;'>";
	$texte .= wordwrap($sum=typo($titre),15)."</span>";
	$texte .= "<span class='survol'>";
	$texte .= "<strong>$sum</strong><br />";
	$texte .= $lieu ? propre($lieu).'<br />':'';
	$texte .= propre($descriptif);
	$texte .= "</span>";
	if ($type=='ics'){	
		$texte .= (strlen($lieu.$descriptif)?"<hr/>":"").$lieu.(strlen($lieu)?"<br/>":"");
		$texte .= $descriptif;
	}
	$texte .= "</span>";

	return $texte;
}

function Agenda_rendu_evenement($flux) {
	global $couleur_claire;
	$evenement = $flux['args']['evenement'];
	$url = $evenement['URL']; 
	$texte = Agenda_rendu_boite($evenement['SUMMARY'],$evenement['DESCRIPTION'],$evenement['LOCATION'],$flux['args']['type']);
	if (is_string($url))
		$texte = http_href(quote_amp($url), $texte, '', '', '', '');
	else if (is_array($url))
		$texte = ajax_action_auteur(
			$url['action'], $url['id'], $url['script'], 
			isset($url['args'])?$url['args']:'', 
			array($texte,""),
			isset($url['args_ajax'])?$url['args_ajax']:'', 
			isset($url['fct_ajax'])?$url['fct_ajax']:'');
	
	$flux['data'] = $texte;
	return $flux;
}

?>