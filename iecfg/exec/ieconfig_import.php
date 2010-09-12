<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_ieconfig_import(){
	if (!autoriser('configurer','ieconfig',0)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('ieconfig:titre_ieconfig'));

	echo gros_titre(_T('ieconfig:titre_ieconfig'),'', false);
	echo barre_onglets('ieconfig', 'ieconfig_import');
	
	// Colonne de gauche
	echo debut_gauche('',true);
	include_spip('inc/config');
	echo avertissement_config();
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'ieconfig_import'),'data'=>''));
	
	// Colonne de droite
	echo debut_droite('',true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'ieconfig_import'),'data'=>''));

	// Contenu
	echo recuperer_fond('prive/ieconfig/ieconfig_import');
	
	// Fin de page
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'ieconfig_import'),'data'=>''));
	echo fin_gauche(),fin_page();
}

?>