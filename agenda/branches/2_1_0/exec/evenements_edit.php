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
include_spip('inc/article_select');

function exec_evenements_edit_dist(){
	exec_evenements_edit_args(_request('id_evenement'), // intval plus tard
		_request('id_article'),
		((_request('new') == 'oui') ? 'new' : ''));
}


function exec_evenements_edit_args($id_evenement, $id_article, $new){
	
	if ($row = sql_fetsel('*','spip_evenements','id_evenement='.intval($id_evenement)))
		$id_article = $row['id_article'];

	if ((!$new AND !$row)
	  OR ($new AND $id_article AND !autoriser('creerevenementdans','article',$id_article))
	  OR (!$new AND (!autoriser('voir', 'evenement', $id_evenement,null,array('id_article'=>$id_article))	OR !autoriser('modifier','evenement', $id_evenement,null,array('id_article'=>$id_article)))) 
	  ) {
		include_spip('inc/minipres');
		echo minipres(_T('agenda:aucun_evenement'));
	} 
	else evenements_edit($id_evenement, $id_article, $new, 'evenements_edit_config', $row);
}

function evenements_edit($id_evenement, $id_article, $new, $config_fonc, $row)
{
	$id_evenement = $row['id_evenement'];
	$commencer_page = charger_fonction('commencer_page', 'inc');
	pipeline('exec_init',array('args'=>array('exec'=>'evenements_edit','id_evenement'=>$id_evenement,'id_article'=>$id_article),'data'=>''));

	$id_rubrique = sql_getfetsel('id_rubrique','spip_articles','id_article='.intval($id_article));
	echo $commencer_page(intval($id_evenement)?_T('agenda:titre_cadre_modifier_evenement'):_T('agenda:titre_cadre_ajouter_evenement'), "naviguer", "evenements", $id_rubrique);

	echo debut_grand_cadre(true);
	echo afficher_hierarchie($id_rubrique);
	echo fin_grand_cadre(true);

	echo debut_gauche("",true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'evenements_edit','id_evenement'=>$id_evenement,'id_article'=>$id_article),'data'=>''));
	echo creer_colonne_droite("",true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'evenements_edit','id_evenement'=>$id_evenement,'id_article'=>$id_article),'data'=>''));
	echo debut_droite("",true);
	
	$oups = _request('retour') ? _request('retour') : 
		($id_article ?
	     generer_url_ecrire("articles","id_article=$id_article")
	     : generer_url_ecrire()     
		);

	$contexte = array(
	'icone_retour'=>icone_inline(_T('icone_retour'), $oups, _DIR_PLUGIN_AGENDA."img_pack/agenda-24.png", "rien.gif",$GLOBALS['spip_lang_left']),
	'redirect'=>_request('retour') ? _request('retour') : generer_url_ecrire('articles'),
	'titre'=>$titre,
	'new'=>$new?$new:$row['id_evenement'],
	'id_article'=>$id_article,
	'config_fonc'=>$config_fonc,
	);

	$milieu = recuperer_fond("prive/editer/evenement", $contexte);
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'evenements_edit','id_evenement'=>$id_evenement,'id_article'=>$id_article),'data'=>$milieu));

	echo fin_gauche(), fin_page();
}

?>