<?php

if (!defined('_DIR_PLUGIN_AGENDA')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_AGENDA',(_DIR_PLUGINS.end($p)));
}
include_spip('base/agenda_evenements');

//Pour 1.9.3: permet d'utiliser les criteres racine, meme_parent, id_parent
$exceptions_des_tables['evenements']['id_parent']='id_evenement_source';

function cron_agenda_nettoyer_base($t){
	# les evenements lies a un article inexistant
	$res = spip_query("SELECT evenements.id_evenement,evenements.id_article
		      FROM spip_evenements AS evenements
		        LEFT JOIN spip_articles AS articles
		          ON evenements.id_article=articles.id_article
		       WHERE articles.id_article IS NULL");
	while ($row = spip_fetch_array($res,SPIP_ASSOC))
		spip_query("DELETE FROM spip_evenements
		WHERE id_evenement=".$row['id_evenement']
		." AND id_article=".$row['id_article']);

	# les liens de mots affectes a des evenements effaces
	$res = spip_query("SELECT mots_evenements.id_mot,mots_evenements.id_evenement 
		        FROM spip_mots_evenements AS mots_evenements
		        LEFT JOIN spip_evenements AS evenements
		          ON mots_evenements.id_evenement=evenements.id_evenement
		       WHERE evenements.id_evenement IS NULL");

	while ($row = spip_fetch_array($res,SPIP_ASSOC))
		spip_query("DELETE FROM spip_mots_evenements
		WHERE id_mot=".$row['id_mot']
		." AND id_evenement=".$row['id_evenement']);

	return 1;
}

function Agenda_taches_generales_cron($taches_generales){
	$taches_generales['agenda_nettoyer_base'] = 3600*48;
	return $taches_generales;
}

function exec_calendrier()
{
	$mode = _request('mode');
	$type = _request('type');
	if ($mode=='editorial'){
		include_spip('exec/calendrier');
	  global $css;
	// icones standards, fonction de la direction de la langue
	
	  global $bleu, $vert, $jaune, $spip_lang_rtl;
	  $bleu = http_img_pack("m_envoi_bleu$spip_lang_rtl.gif", 'B', "class='calendrier-icone'");
	  $vert = http_img_pack("m_envoi$spip_lang_rtl.gif", 'V', "class='calendrier-icone'");
	  $jaune= http_img_pack("m_envoi_jaune$spip_lang_rtl.gif", 'J', "class='calendrier-icone'");
	
	  $date = date("Y-m-d", time()); 
		if ($type == 'semaine') {
		
			$GLOBALS['afficher_bandeau_calendrier_semaine'] = true;
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
		
	  $res .= debut_page($titre, "redacteurs", "calendrier","",$css);
		$res .= barre_onglets("calendrier", "editorial");
		$res .= "<div>&nbsp;</div>" ;
	  $res .= http_calendrier_init('', $type, '','',generer_url_ecrire('calendrier', 'mode=editorial'.($type ? "&type=$type" : '')));
		
	  $res .= fin_page();
	  echo $res;
	}
	else{
		$var_f = charger_fonction('agenda_evenements');
		$var_f();
	}
}


?>