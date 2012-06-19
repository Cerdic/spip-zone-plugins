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
	echo $commencer_page(_T('saveauto:titre_page_saveauto'), "configuration", "base");

	echo "<br /><br />\n"; // outch que c'est vilain !
	echo gros_titre(_T('titre_admin_tech'),'', false);
	echo barre_onglets("administration", "saveauto");

	echo debut_gauche('', true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'saveauto'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'saveauto'),'data'=>''));

	echo debut_droite('', true);
	echo debut_cadre_trait_couleur(find_in_path('img_pack/saveauto-24.png'),true, "", _T('saveauto:titre_boite_sauver'));
	echo recuperer_fond('prive/contenu/saveauto', array('err'=>_request('err')));
	echo fin_cadre_trait_couleur(true);

	echo debut_cadre_trait_couleur(find_in_path('img_pack/saveauto-24.png'),true, "", _T('saveauto:titre_boite_historique'));
	echo recuperer_fond('prive/contenu/saveauto_historique');
	echo fin_cadre_trait_couleur(true);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'saveauto'),'data'=>''));

	echo fin_gauche(), fin_page();
}
?>