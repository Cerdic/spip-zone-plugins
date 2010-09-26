<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_inserer_modeles(){

	if (defined('_AJAX') AND _AJAX){
		$contexte = array_merge(array('modalbox'=>'oui'),$_GET);
		$res = '<p />'.recuperer_fond('prive/inserer_modeles',$contexte);

		include_spip('inc/actions');
		ajax_retour($res);
		return;
	}
	
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('inserer_modeles:titre_inserer_modeles'));
	echo gros_titre(_T('inserer_modeles:titre_inserer_modeles'),'',false);
	echo debut_grand_cadre(true);
	echo recuperer_fond('prive/inserer_modeles',$_GET);
	echo fin_grand_cadre(true),fin_page();

}

?>