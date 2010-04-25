<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_langonet_lister_dist(){
	global $spip_lang_right;
	// si pas autorise : message d'erreur
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'langonet'),'data'=>''));

	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('langonet:titre_page_navigateur'), "naviguer", "langonet");
	
	// colonne gauche
	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'langonet_lister'),'data'=>''));
	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'langonet_lister'),'data'=>''));
	
	// centre
	echo debut_droite('', true);
	
	// titre
	echo gros_titre(_T('langonet:titre_page'),'', false);
	
	// barre d'onglets
	echo barre_onglets("langonet", "langonet_lister");

	// contenu
 	echo recuperer_fond('prive/contenu/langonet_lister',  array());

	// fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'langonet_lister'),'data'=>''));

	echo fin_gauche(), fin_page();
}

?>
