<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_langonet_verifier_dist(){
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
	echo "<br />\n";
	echo "<br />\n";
	
	// titre
	echo gros_titre(_T('langonet:titre_page'),'', false);
	
	// barre d'onglets
	echo barre_onglets("langonet", "langonet_verifier");
	
	// colonne gauche
	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'langonet_verifier'),'data'=>''));
	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'langonet_verifier'),'data'=>''));
	
	// centre
	echo debut_droite('', true);

	// contenu
 	echo recuperer_fond('prive/contenu/langonet_verifier',  array());

	// fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'langonet_verifier'),'data'=>''));

	echo fin_gauche(), fin_page();
}

?>
