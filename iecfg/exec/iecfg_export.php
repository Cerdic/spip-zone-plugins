<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_iecfg_export(){
	if (!autoriser('configurer','iecfg',0)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('iecfg:titre_iecfg'));

	echo gros_titre(_T('iecfg:titre_iecfg'),'', false);
	echo barre_onglets('iecfg', 'iecfg_export');
	
	// Colonne de gauche
	echo debut_gauche('',true);
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'iecfg_export'),'data'=>''));
	
	// Colonne de droite
	echo debut_droite('',true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'iecfg_export'),'data'=>''));

	// Contenu
	echo recuperer_fond('prive/iecfg/iecfg_export');
	
	// Fin de page
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'iecfg_export'),'data'=>''));
	echo fin_gauche(),fin_page();
}

?>