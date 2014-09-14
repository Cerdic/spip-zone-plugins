<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_priveperso_dist(){
	global $spip_lang_right;
	// si pas autorise : message d'erreur
	if (!autoriser('configurer', 'priveperso')) {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'priveperso'),'data'=>''));

	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('priveperso:personnaliser_espace_prive'), "configuration", "configuration");

	// barre d'onglets
	// echo barre_onglets("configuration", "priveperso");

	// colonne gauche
	echo debut_gauche('', true);
	echo cadre_priveperso_infos();
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'priveperso'),'data'=>''));

	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'priveperso'),'data'=>''));

	// centre
	echo debut_droite('', true);

	// contenu
	include_spip('inc/inscrire_priveperso');

	echo gros_titre(_T('priveperso:personnaliser_espace_prive'),'', false);
	echo recuperer_fond('prive/contenu/priveperso_rubriques');


	echo icone_inline(_T('priveperso:info_modifier_priveperso'), generer_url_ecrire("priveperso_edit"), find_in_path("prive/themes/spip/images/priveperso-24.png"), "creer.gif", $spip_lang_right);
	// fin contenu

	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'priveperso'),'data'=>''));

	echo fin_gauche(), fin_page();
}

// afficher les informations de la page
function cadre_priveperso_infos() {
	$boite = pipeline ('boite_infos', array('data' => '',
		'args' => array(
			'type'=>'priveperso',
		)
	));

	if ($boite)
		return debut_boite_info(true) . $boite . fin_boite_info(true);
}

?>