<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('base/abstract_sql');

function exec_zones_edit_dist(){
	$new = _request('new');
	$id_zone = intval(_request('id_zone'));
	$row = sql_fetsel('*','spip_zones','id_zone='.intval($id_zone));

	if ((!$new AND !$row)
	  OR ($new AND !autoriser('creer','zone')) 
	  OR (!$new AND (!autoriser('modifier', 'zone', $id_zone))) 
	  ) {
		include_spip('inc/minipres');
		echo minipres(_T('accesrestreint:aucune_zone'));
	} 
	else zones_edit($id_zone, $new, '', $row);
}

function zones_edit($id_zone, $new, $config_fonc, $row)
{
	$id_zone = $row['id_zone'];
	$commencer_page = charger_fonction('commencer_page', 'inc');
	pipeline('exec_init',array('args'=>array('exec'=>'zones_edit','id_zone'=>$id_zone),'data'=>''));

	echo $commencer_page(intval($id_zone)?_T('accesrestreint:titre_cadre_modifier_zone'):_T('accesrestreint:creer_zone'), "naviguer", "zones", 0);

	echo debut_gauche("",true);
	echo recuperer_fond("prive/editer/zone_auteurs", $_GET);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'zones_edit','id_zone'=>$id_zone),'data'=>''));
	echo creer_colonne_droite("",true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'zones_edit','id_zone'=>$id_zone),'data'=>''));
	echo debut_droite("",true);
	
	$oups = _request('retour') ? _request('retour') : 
		($id_article ?
	     generer_url_ecrire("acces_restreint")
	     : generer_url_ecrire()     
		);

	$contexte = array(
	'icone_retour'=>icone_inline(_T('icone_retour'), $oups, _DIR_PLUGIN_ACCESRESTREINT."img_pack/zones-acces-24.png", "rien.gif",$GLOBALS['spip_lang_left']),
	'redirect'=>_request('retour') ? _request('retour') : generer_url_ecrire('acces_restreint'),
	'titre'=>$titre,
	'new'=>$new?$new:$row['id_zone'],
	'config_fonc'=>$config_fonc,
	);

	$milieu = recuperer_fond("prive/editer/zone", $contexte);
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'zones_edit','id_zone'=>$id_zone),'data'=>$milieu));

	echo fin_gauche(), fin_page();
}

?>