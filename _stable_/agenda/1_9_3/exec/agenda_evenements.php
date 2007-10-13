<?php

include_spip('inc/presentation');
include_spip('inc/voir_agenda');

function exec_agenda_evenements_dist(){
	include_spip('inc/calendar');
	// Reserver les widgets agenda
	WCalendar_ajoute_lies(_T('agenda:evenement_date_debut'),'_evenement_debut',_T('agenda:evenement_date_fin'),'_evenement_fin');
	WCalendar_ajoute_statique(_T('agenda:evenement_repetitions'),'_repetitions');

	$ajouter_id_article = intval(_request('ajouter_id_article'));
	$flag_editable = article_editable($ajouter_id_article);

	global $visu_evenements;
	$type = _request('type');
	if (!$type) $type='semaine';
	$id_evenement = intval(_request('id_evenement'));
	$edit = _request('edit');
	$neweven = _request('neweven');

	$annee = intval(_request('annee'));
	$mois = intval(_request('mois'));
	$jour = intval(_request('jour'));
	$date = date("Y-m-d", time());
	if ($annee&&$mois&&$jour)
		$date = date("Y-m-d", strtotime("$annee-$mois-$jour"));


		
	if ($type == 'semaine') {
	
		//$GLOBALS['afficher_bandeau_calendrier_semaine'] = true;
		$titre = _T('titre_page_calendrier',
			array('nom_mois' => nom_mois($date), 'annee' => annee($date)));
	}
  elseif ($type == 'jour') {
		$titre = nom_jour($date)." ". affdate_jourcourt($date);
  }
	else {
		$titre = _T('titre_page_calendrier',
		    array('nom_mois' => nom_mois($date), 'annee' => annee($date)));
	}

  $out = debut_page($titre, "redacteurs", "calendrier","",$css);
	$out .= barre_onglets("calendrier", "evenements");

	$out .= "<div>";
	if ($ajouter_id_article){
		$res2 = spip_query("SELECT * FROM spip_articles AS articles WHERE id_article="._q($ajouter_id_article));
		if ($row2 = spip_fetch_array($res2)){
			$out .= "<div style=' width:750px; font-size: 18px; color: #9DBA00; font-weight: bold;text-align:left;'>";
			$out .= "<a href='".generer_url_ecrire('articles',"id_article=".$row2['id_article'])."'>";
			$out .= http_img_pack("article-24.gif", "", "width='24' height='24' style='border:none;'");
			$out .= entites_html(typo($row2['titre']))."</a></div>";
		}
	}
	$out .= "&nbsp;</div>" ;
	
	$voir_agenda = charger_fonction("voir_agenda","inc");
	$out .= "<div id='voir_agenda'>".$voir_agenda($flag_editable)."</div>";

	$out .= "<div id='voir_evenement-0'>";
	if (($edit||$neweven)&&($flag_editable))	{ //---------------Edition RDV ------------------------------
		$ndate = _request('ndate');
		$form .= Agenda_formulaire_edition_evenement($id_evenement,$neweven,$ndate);
		$args = explode('?',self());
		$out .= ajax_action_auteur('voir_evenement',"0-modifier-$id_article-$id_evenement", 'calendrier', end($args), $form,'','reload_agenda');
	}
	elseif ((isset($id_evenement))&&(isset($visu_evenements[$id_evenement]))){ //---------------Visualisation RDV ------------------------------
		$voir_evenement = charger_fonction('voir_evenement','inc');
		$out .= $voir_evenement($id_evenement,$flag_editable);
	}
	$out .= "</div>";
	
	$out .= "<script type='text/javascript'>
	function reload_agenda(){
	var url=document.location.href;
	url = url.replace(/exec=[^&]*/,'exec=voir_agenda')
	\$('#voir_agenda').load(url);
	}</script>";
	$out .= fin_page();
	echo $out;
}

?>