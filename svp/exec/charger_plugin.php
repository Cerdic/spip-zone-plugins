<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_charger_plugin_dist($retour=''){

	// si pas autorise : message d'erreur
	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
	}
	else {

		// pipeline d'initialisation
		pipeline('exec_init', array('args'=>array('exec'=>'charger_plugin'),'data'=>''));
	
		// entetes
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo "<br />\n";
		echo "<br />\n";
		
		// titre
		echo gros_titre(_T('icone_admin_plugin'),'',false);
		
		// barre d'onglets
		echo barre_onglets('plugins', 'charger_plugin');
		
		// colonne gauche
		echo debut_gauche('plugin',true);
		// -- Boite d'infos
		$boite = _T('svp:info_boite_charger_plugin');
		if ($boite)
			echo debut_boite_info(true) . $boite . fin_boite_info(true); 
		echo pipeline('affiche_gauche', array('args'=>array('exec'=>'charger_plugin'),'data'=>''));
		
		// colonne droite
		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite', array('args'=>array('exec'=>'charger_plugin'),'data'=>''));
		
		// centre
		echo debut_droite('plugin', true);
	
		// contenu
		// -- On essaye de creer le repertoire auto/ sans rien demander
		sous_repertoire(_DIR_PLUGINS_AUTO, '', true, true);
		echo recuperer_fond('prive/contenu/charger_plugin',  array());
	
		// fin contenu
		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'depots_gerer', 'retour'=>$retour),'data'=>''));
	
		echo fin_gauche(), fin_page();
	}
}

?>
