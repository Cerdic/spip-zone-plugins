<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

function exec_boussoles_gerer_dist(){

	// si pas autorise : message d'erreur
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'boussoles_gerer'),'data'=>''));

	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('boussole:titre_page_boussole'), "configuration", "boussole");
	echo "<br />\n";
	echo "<br />\n";
	
	// titre
	echo gros_titre(_T('boussole:titre_page_boussole'),'', false);
	
	// barre d'onglets
	echo barre_onglets("boussoles", "gerer");
	
	// colonne gauche
	echo debut_gauche('', true);
	// -- Boite d'infos
	$boite = _T('boussole:info_boite_boussoles_gerer');
    if ($boite)
        echo debut_boite_info(true) . $boite . fin_boite_info(true); 
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'boussoles_gerer'),'data'=>''));
	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'boussoles_gerer'),'data'=>''));
	
	// centre
	echo debut_droite('', true);

	// contenu
	echo recuperer_fond('prive/contenu/boussoles_gerer',  array());

	// fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'boussoles_gerer'),'data'=>''));

	echo fin_gauche(), fin_page();
}

?>
