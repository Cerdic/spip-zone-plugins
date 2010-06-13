<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_saveauto_dist(){
	if (!autoriser('sauvegarder', 'saveauto')) {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}
	exec_saveauto_args($_GET);
}

function exec_saveauto_args($contexte=array()){
	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'saveauto'),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('saveauto:titre_page_saveauto'), "administration", "administration");

	echo "<br /><br />\n"; // outch que c'est vilain !
	echo gros_titre(_T('titre_admin_tech'),'', false);
	echo barre_onglets("administration", "saveauto");

	echo debut_gauche('', true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'saveauto', 'type'=>$type),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'saveauto', 'type'=>$type),'data'=>''));

	echo debut_droite('', true);
	if(defined('_DIR_SITE')){
		$rep_bases = _DIR_SITE.lire_config('saveauto/rep_bases','');
	}else{
		$rep_bases = _DIR_RACINE.lire_config('saveauto/rep_bases','');
	}
	$prefixe = lire_config('saveauto/prefixe_save','');

	$contexte['sauvegardes'] = preg_files($rep_bases,"$prefixe.+[.](zip|sql)$");
	echo recuperer_fond('prive/contenu/saveauto',$contexte);
	echo recuperer_fond('prive/contenu/saveauto_historique',$contexte);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'saveauto', 'type'=>$type),'data'=>''));

	echo fin_gauche(), fin_page();
}
?>