<?php

/**
 * Plugin Gabarits pour Spip 2.0
 * Licence GPL
 * 
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_gabarits_edit_dist(){
	exec_gabarits_edit_args(_request('id_gabarit'), // intval plus tard
		((_request('new') == 'oui') ? 'new' : ''));
}


function exec_gabarits_edit_args($id_gabarit, $new){
	
	$row = sql_fetsel('*','spip_gabarits','id_gabarit='.intval($id_gabarit));

	if ((!$new AND !$row)
	  OR (!$new AND !autoriser('modifier','gabarit', $id_gabarit, null, null))
	  ) {
		include_spip('inc/minipres');
		echo minipres(_T('gabarits:aucun_gabarit'));
	} 
	else gabarits_edit($id_gabarit, $new, $row);
}

function gabarits_edit($id_gabarit, $new, $row){

	$id_gabarit = $row['id_gabarit'];
	$commencer_page = charger_fonction('commencer_page', 'inc');
	pipeline('exec_init',array('args'=>array('exec'=>'gabarits_edit','id_gabarit'=>$id_gabarit),'data'=>''));

	echo $commencer_page(intval($id_gabarit)?_T('gabarits:titre_cadre_modifier_gabarit'):_T('gabarits:titre_cadre_ajouter_gabarit'), "gabarits", "gabarits");

	echo debut_gauche("",true);
	echo recuperer_fond("prive/navigation/gabarits_edit", array('id_gabarit'=>$id_gabarit));
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'gabarits_edit','id_gabarit'=>$id_gabarit),'data'=>''));
	echo creer_colonne_droite("",true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'gabarits_edit','id_gabarit'=>$id_gabarit),'data'=>''));
	echo debut_droite("",true);
	
	$oups = _request('retour') ? _request('retour') : generer_url_ecrire("gabarits_tous");

	$contexte = array(
	'icone_retour'=>icone_inline(_T('icone_retour'), $oups, _DIR_PLUGIN_GABARITS."prive/themes/spip/images/gabarits-24.png", "rien.gif",$GLOBALS['spip_lang_left']),
	'redirect'=>$oups,
	'new'=>$new?$new:$row['id_gabarit'],
	'titre'=>$row['titre'],
	);

	$milieu = recuperer_fond("prive/editer/gabarit", $contexte);
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'gabarits_edit','id_gabarit'=>$id_gabarit),'data'=>$milieu));

	echo fin_gauche(), fin_page();
}

?>