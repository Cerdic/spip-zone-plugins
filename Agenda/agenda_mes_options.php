<?php
define('_DIR_PLUGIN_AGENDA_EVENEMENTS',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));
include_spip('base/agenda_evenements');

global $INDEX_elements_objet;
$INDEX_elements_objet['spip_evenements'] = array('titre'=>8,'descriptif'=>4,'lieu'=>3);

global $INDEX_objet_associes;
$INDEX_objet_associes['spip_articles']['spip_evenements'] = 1;

global $INDEX_elements_associes;
$INDEX_elements_associes['spip_evenements'] = array('titre'=>2,'descriptif'=>1);


function exec_calendrier()
{
	$mode = _request('mode');
	if ($mode=='editorial'){
		include_spip('exec/calendrier');
	  global $type, $css;
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
		
	  debut_page($titre, "redacteurs", "calendrier","",$css);
		barre_onglets("calendrier", "editorial");
		echo "<div>&nbsp;</div>" ;
	  echo http_calendrier_init('', $type, '','',generer_url_ecrire('calendrier', 'mode=editorial'.($type ? "&type=$type" : '')));
		
	  fin_page();
	}
	else{
		$var_f = charger_fonction('agenda_evenements');
		$var_f();
	}
}


?>