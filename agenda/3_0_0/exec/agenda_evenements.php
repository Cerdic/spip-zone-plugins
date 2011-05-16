<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

include_spip('inc/presentation');

function exec_agenda_evenements_dist(){
	/*$ajouter_id_article = intval(_request('ajouter_id_article'));
	$flag_editable = article_editable($ajouter_id_article);*/

	/*$annee = intval(_request('annee'));
	$mois = intval(_request('mois'));
	$jour = intval(_request('jour'));
	$date = date("Y-m-d", time());
	if ($annee&&$mois&&$jour)
		$date = date("Y-m-d", strtotime("$annee-$mois-$jour"));*/

	$commencer_page = charger_fonction('commencer_page', 'inc');
	$out = $commencer_page(_T('agenda:tous_les_evenements'), "agenda", "calendrier");
	$out .= barre_onglets("calendrier", "agenda");
	
	$contexte = array();
	foreach($_GET as $key=>$val)
		$contexte[$key] = $val;

	$out .= debut_gauche("agenda",true);

	$out .=  recuperer_fond("prive/navigation/agenda_evenements",$contexte);
	
	$out .= debut_droite('agenda',true);

	$out .=  recuperer_fond("prive/contenu/agenda_evenements",$contexte);
	
	$out .= fin_gauche('agenda',true);
	$out .= fin_page();

	echo $out;
}

?>
