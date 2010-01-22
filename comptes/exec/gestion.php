<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_gestion(){

	if (defined('_AJAX') AND _AJAX){
		$contexte = array_merge(array('editable'=>0),$_GET);
		$res = /*formulaire_recherche('portfolio').*/recuperer_fond('prive/gestion',$contexte);

		include_spip('inc/actions');
		ajax_retour($res);
		return;
	}
	
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('gestion:titre'));
	
	echo gros_titre("<a href='".generer_url_ecrire('gestion')."'>"._T('gestion:titre')."</a>",'',false);
	echo debut_grand_cadre(true);
	
	echo formulaire_recherche('gestion');
	echo recuperer_fond('prive/gestion',$_GET);

	echo fin_grand_cadre(true),fin_page();
}
