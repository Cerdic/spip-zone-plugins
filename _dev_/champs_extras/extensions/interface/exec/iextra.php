<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_iextra_dist(){
	global $spip_lang_right;
	// si pas autorise : message d'erreur
	if (!autoriser('configurer', 'iextra')) {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'iextra'),'data'=>''));

	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('iextra:titre_page_iextra'), "configuration", "configuration");
	
	// titre
	echo "<br /><br /><br />\n"; // outch que c'est vilain !
	echo gros_titre(_T('iextra:titre_iextra'),'', false);
	
	// barre d'onglets
	echo barre_onglets("configuration", "interface_extra");
	
	// colonne gauche
	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'iextra'),'data'=>''));
	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'iextra'),'data'=>''));
	
	// centre
	echo debut_droite('', true);

	// contenu
	include_spip('inc/iextra');
	echo recuperer_fond('prive/contenu/champs_extras', array(
		'extras'=>iextra_get_extras_par_table(),
	));
		
	echo icone_inline(_T('iextra:icone_creer_champ_extra'), generer_url_ecrire("iextra_edit"), find_in_path("images/iextra-24.png"), "creer.gif", $spip_lang_right);
	// fin contenu

	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'iextra'),'data'=>''));

	echo fin_gauche(), fin_page();
}
?>
