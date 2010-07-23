<?php

/**
 * Plugin Canevas pour Spip 2.0
 * Licence GPL
 * 
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_canevas_edit_dist(){
	exec_canevas_edit_args(_request('id_canevas'), // intval plus tard
		((_request('new') == 'oui') ? 'new' : ''));
}


function exec_canevas_edit_args($id_canevas, $new){
	
	$row = sql_fetsel('*','spip_canevas','id_canevas='.intval($id_canevas));

	if ((!$new AND !$row)
	  OR (!$new AND !autoriser('modifier','canevas', $id_canevas, null, null))
	  ) {
		include_spip('inc/minipres');
		echo minipres(_T('canevas:aucun_canevas'));
	} 
	else canevas_edit($id_canevas, $new, $row);
}

function canevas_edit($id_canevas, $new, $row){

	$id_canevas = $row['id_canevas'];
	$commencer_page = charger_fonction('commencer_page', 'inc');
	pipeline('exec_init',array('args'=>array('exec'=>'canevas_edit','id_canevas'=>$id_canevas),'data'=>''));

	echo $commencer_page(intval($id_canevas)?_T('canevas:titre_cadre_modifier_canevas'):_T('canevas:titre_cadre_ajouter_canevas'), "canevas", "canevas");

	echo debut_gauche("",true);
	echo recuperer_fond("prive/navigation/canevas_edit", array('id_canevas'=>$id_canevas));
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'canevas_edit','id_canevas'=>$id_canevas),'data'=>''));
	echo creer_colonne_droite("",true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'canevas_edit','id_canevas'=>$id_canevas),'data'=>''));
	echo debut_droite("",true);
	
	$oups = _request('retour') ? _request('retour') : generer_url_ecrire("canevas_tous");

	$contexte = array(
	'icone_retour'=>icone_inline(_T('icone_retour'), $oups, _DIR_PLUGIN_CANEVAS."prive/themes/spip/images/canevas-24.png", "rien.gif",$GLOBALS['spip_lang_left']),
	'redirect'=>$oups,
	'new'=>$new?$new:$row['id_canevas'],
	'titre'=>$row['titre'],
	);

	$milieu = recuperer_fond("prive/editer/canevas", $contexte);
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'canevas_edit','id_canevas'=>$id_canevas),'data'=>$milieu));

	echo fin_gauche(), fin_page();
}

?>