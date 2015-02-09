<?php
/**
 * Plugin LinkCheck
 * (c) 2013 Benjamin Grapeloux, Guillaume Wauquier, Guillaume Wauquier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Un fichier de fonctions permet de definir des elements
 * systematiquement charges lors du calcul des squelettes.
 *
 * Il peut par exemple définir des filtres, critères, balises, …
 * 
 */
function linkcheck_en_url($url,$distant) {
	if ($distant==0) {
		$retour = ptobr(propre("[$url".' ('.supprimer_tags(propre("[->$url]")).')|'._T('linkcheck:ouvrenouvelonglet')."->$url]"));
	} else {
		$retour = ptobr(propre("[$url|"._T('linkcheck:ouvrenouvelonglet')."->$url]"));
	}
	return $retour; 
}


function balise_LINKCHECK_CHIFFRE($p) {
    $p->code = "linkcheck_chiffre()";
    return $p;
}

function linkcheck_chiffre()
{
	$tab_chiffre=array();
	$tab_chiffre['nb_lien']=sql_getfetsel('count(id_linkcheck)','spip_linkchecks');
	if($tab_chiffre['nb_lien']>0){
		$tab_chiffre['nb_lien_mort']=sql_getfetsel('count(id_linkcheck)','spip_linkchecks','etat='.sql_quote('mort'));
		$tab_chiffre['nb_lien_malade']=sql_getfetsel('count(id_linkcheck)','spip_linkchecks','etat='.sql_quote('malade'));
		$tab_chiffre['nb_lien_deplace']=sql_getfetsel('count(id_linkcheck)','spip_linkchecks','etat='.sql_quote('deplace'));
		$tab_chiffre['nb_lien_ok']=sql_getfetsel('count(id_linkcheck)','spip_linkchecks','etat='.sql_quote('ok'));
		$tab_chiffre['nb_lien_inconnu']=sql_getfetsel('count(id_linkcheck)','spip_linkchecks','etat=\'\'');
		$tab_chiffre['pct_lien_mort'] = $tab_chiffre['nb_lien_mort']*100/$tab_chiffre['nb_lien'];
		$tab_chiffre['pct_lien_malade'] = $tab_chiffre['nb_lien_malade']*100/$tab_chiffre['nb_lien'];
		$tab_chiffre['pct_lien_deplace'] = $tab_chiffre['nb_lien_deplace']*100/$tab_chiffre['nb_lien'];
		$tab_chiffre['pct_lien_ok'] = $tab_chiffre['nb_lien_ok']*100/$tab_chiffre['nb_lien'];
	}
	return $tab_chiffre;
}


?>
