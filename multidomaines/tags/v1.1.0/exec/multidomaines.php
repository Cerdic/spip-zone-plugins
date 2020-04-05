<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_multidomaines_dist(){
	
	if (!autoriser('configurer','multidomaine')){
		include_spip('inc/minipres');
		echo minipres();
	} else {
		// pipeline d'initialisation
		pipeline('exec_init', array('args'=>array('exec'=>'multidomaines'),'data'=>''));
		
		// entetes
		$commencer_page = charger_fonction('commencer_page', 'inc');
		
		// titre, partie, sous_partie (pour le menu)
		echo $commencer_page(_T('multidomaines:titre_multidomaines'), "configuration", "multidomaines");
		
		// titre
		echo "<br /><br /><br />\n"; // outch ! aie aie aie ! au secours !
		echo gros_titre(_T('multidomaines:titre_multidomaines'),'', false);
		
		// colonne gauche
		echo debut_gauche('', true);
		echo pipeline('affiche_gauche', array('args'=>array('exec'=>'multidomaines'),'data'=>''));
		echo debut_boite_info(true);
		echo  _T('info_gauche_admin_tech');
		echo fin_boite_info(true);
		
		
		// colonne droite
		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite', array('args'=>array('exec'=>'multidomaines'),'data'=>''));
		
		// centre
		echo debut_droite('', true);
		// contenu
		// ...
		
		echo debut_cadre_trait_couleur(_DIR_PLUGIN_MULTIDOMAINES."images/logo-24.png", true, "", _T('multidomaines:configurations'));
		
		echo recuperer_fond("html/configurer_multidomaines",array());
		
		echo fin_cadre_trait_couleur(true);

		// ...
		// fin contenu
		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'multidomaines'),'data'=>''));
		echo fin_gauche(), fin_page();
	}
}
?>
