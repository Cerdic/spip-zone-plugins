<?php

function Agenda_ajouter_onglets($flux) {
  if($flux['args']=='calendrier')
  {
		$flux['data']['evenements']= new Bouton(
																 '../'._DIR_PLUGIN_AGENDA_EVENEMENTS.'/img_pack/agenda-24.png', 'Evenements',
																generer_url_ecrire("calendrier","type=semaine"));
	
		$flux['data']['editorial']= new Bouton(
															 'cal-rv.png', 'Activité Editoriale',
																 generer_url_ecrire("calendrier","mode=editorial&type=semaine"));
  }
	return $flux;
}
function Agenda_header_prive($flux) {
	$exec = _request('exec');
	if ($exec == 'calendrier'){
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA_EVENEMENTS . '/calendrier.css" type="text/css" />'. "\n";
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA_EVENEMENTS . '/agenda.css" type="text/css" />'. "\n";
	}
	if ($exec == 'articles'){
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_AGENDA_EVENEMENTS . '/agenda_articles.css" type="text/css" />'. "\n";
	}
	return $flux;
}

function Agenda_rendu_boite($titre,$descriptif,$lieu,$type='ics'){
	$texte = "<span class='calendrier-verdana10'><span  style='font-weight: bold;'>";
	$texte .= wordwrap($sum=typo($titre),15)."</span>";
	$texte .= "<span class='survol'>";
	$texte .= "<strong>$sum</strong><br />";
	$texte .= $lieu ? "$lieu<br />":"";
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

	
	/*$url = $evenement['URL']; 
	$desc = propre($evenement['DESCRIPTION']);
	$lieu = $evenement['LOCATION'];
	$sum = typo($evenement['SUMMARY']);

	$texte = "<span class='calendrier-verdana10'><span  style='font-weight: bold;'>";
	$texte .= wordwrap($sum,15)."</span>";
	$texte .= "<span class='survol'>";
	$texte .= "<strong>$sum</strong><br />";
	$texte .= $lieu ? "$lieu<br />":"";
	$texte .= $desc;
	$texte .= "</span>";
	$texte .= "</span>";*/
	$texte = Agenda_rendu_boite($evenement['SUMMARY'],$evenement['DESCRIPTION'],$evenement['LOCATION'],$flux['args']['type']);
	$texte = http_href(quote_amp($url), $texte, '', '', '', '');
	
	$flux['data'] = $texte;
	return $flux;
}

/* public static */
/*function Agenda_ajouterBoutons($boutons_admin) {
	// si on est admin
	//if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['accueil']->sousmenu["agenda"]= new Bouton(
		"../"._DIR_PLUGIN_PUBLIMAP."/habillage/publimap-24.png",  // icone
		_L("Publimap") //titre
		);
	}
	return $boutons_admin;
}*/


?>