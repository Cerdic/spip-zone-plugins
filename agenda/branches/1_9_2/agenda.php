<?php

function Agenda_ajouter_onglets($flux) {
  if($flux['args']=='calendrier')
  {
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
	$exec = _request('exec');
	// les CSS
	if ($exec == 'calendrier'){
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA . '/img_pack/calendrier.css" type="text/css" />'. "\n";
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA . '/img_pack/agenda.css" type="text/css" />'. "\n";
	}
	if ($exec == 'articles'){
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA . '/img_pack/agenda_articles.css" type="text/css" />'. "\n";
	}


	return $flux;
}

function Agenda_exec_init($flux) {
	$exec =  $flux['args']['exec'];
	if (($exec == 'calendrier')||($exec=='articles')){
		include_spip('inc/calendar');
		if (!function_exists('WCalendar_ajoute_lies')){
			echo ('Erreur Wcalendar manquant');
			return $flux;
		}
		// Reserver les widgets agenda
		WCalendar_ajoute_lies(_T('agenda:evenement_date_debut'),'_evenement_debut',_T('agenda:evenement_date_fin'),'_evenement_fin');
		WCalendar_ajoute_statique(_T('agenda:evenement_repetitions'),'_repetitions');
	}
	return $flux;
}

function Agenda_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	if ($exec=='articles'){
		include_spip('inc/calendar');
		include_spip('inc/agenda_gestion');
		$id_article = $flux['args']['id_article'];
		$flux['data'] .= Agenda_formulaire_article($id_article, article_editable($id_article),'articles');
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