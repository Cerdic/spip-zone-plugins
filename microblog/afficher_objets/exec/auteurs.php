<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

// Constante pour le nombre d'auteurs par page.
@define('MAX_AUTEURS_PAR_PAGE', 30);
@define('AUTEURS_MIN_REDAC', "0minirezo,1comite,5poubelle");
@define('AUTEURS_DEFAUT', '');
// decommenter cette ligne et commenter la precedente 
// pour que l'affichage par defaut soit les visiteurs
#@define('AUTEURS_DEFAUT', '!');

// http://doc.spip.org/@exec_auteurs_dist
function exec_auteurs_dist(){

	$statut =  _request('statut');
	if (!$statut)  $statut = AUTEURS_DEFAUT . AUTEURS_MIN_REDAC;
	
	pipeline('exec_init',array('args'=>array('exec'=>'auteurs'),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('info_auteurs'),"auteurs","redacteurs");

	$ret = debut_gauche("auteurs",true) . debut_boite_info(true);

	$ret .= "\n<p class='arial1'>"._T('info_gauche_auteurs'). '</p>';

	if ($GLOBALS['visiteur_session']['statut'] == '0minirezo')
		$ret .= "\n<p class='arial1'>". _T('info_gauche_auteurs_exterieurs') . '</p>';

	$ret .= fin_boite_info(true);

	$ret .= pipeline('affiche_gauche',array('args'=>array('exec'=>'auteurs'),'data'=>''));

	$res = '';
	if (autoriser('creer','auteur'))
		$res = icone_horizontale(_T('icone_creer_nouvel_auteur'), generer_url_ecrire("auteur_infos", 'new=oui'), "auteur-24.gif", "creer.gif", false);

	$res .= icone_horizontale(_T('icone_informations_personnelles'), generer_url_ecrire("auteur_infos","id_auteur=$connect_id_auteur"), "fiche-perso-24.gif","rien.gif", false);

	if (avoir_visiteurs(true))
		$res .= icone_horizontale (_T('icone_afficher_visiteurs'), generer_url_ecrire("visiteurs"), "auteur-24.gif", "", false);

	$ret .= bloc_des_raccourcis($res);
	$ret .= creer_colonne_droite('auteurs',true);
	$ret .= pipeline('affiche_droite',array('args'=>array('exec'=>'auteurs'),'data'=>''));
	$ret .= debut_droite('',true);

	$ret .= "\n<br />";
	$ret .= gros_titre($visiteurs ? _T('info_visiteurs') :  _T('info_auteurs'),'',false);
	$ret .= "\n<br />";

	echo $ret;
	echo formulaire_recherche("auteurs",(($s=_request('statut'))?"<input type='hidden' name='statut' value='$s' />":""));
		
	echo "<div class='nettoyeur'></div>";

	$contexte = $_GET;
	$contexte['nb'] = MAX_AUTEURS_PAR_PAGE;
	if (substr($statut,0,1)!=='!')
		$contexte['statut'] = explode(',',$statut);
	else {
		$statut = substr($statut,1);
		$statut = explode(',',$statut);
		$statut = sql_allfetsel('DISTINCT statut','spip_auteurs',sql_in('statut',$statut,'NOT'));
		$contexte['statut'] = array_map('reset',$statut);
	}

	if ($GLOBALS['visiteur_session']['statut']=='0minirezo'){
		// n'exclure que les articles a la poubelle des compteurs
		$contexte['filtre_statut_articles'] = array('poubelle');
	}
	else {
		// exclure les articles a la poubelle, en redac ou refuse des compteurs
		$contexte['filtre_statut_articles'] = array('prepa','poubelle','refuse');
	}

	$lister_objets = charger_fonction('lister_objets','inc');
	echo $lister_objets('auteurs',$contexte);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'auteurs'),'data'=>''));
	echo fin_gauche(), fin_page();
}

?>