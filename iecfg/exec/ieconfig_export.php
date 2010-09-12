<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_ieconfig_export(){
	if (!autoriser('configurer','ieconfig',0)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('ieconfig:titre_ieconfig'));

	echo gros_titre(_T('ieconfig:titre_ieconfig'),'', false);
	echo barre_onglets('ieconfig', 'ieconfig_export');
	
	// Colonne de gauche
	echo debut_gauche('',true);
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'ieconfig_export'),'data'=>''));
	
	// Colonne de droite
	echo debut_droite('',true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'ieconfig_export'),'data'=>''));

	// Contenu
	echo recuperer_fond('prive/ieconfig/ieconfig_export');
	
	// Fin de page
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'ieconfig_export'),'data'=>''));
	echo fin_gauche(),fin_page();
}

?>