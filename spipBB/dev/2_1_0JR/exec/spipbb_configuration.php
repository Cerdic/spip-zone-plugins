<?php


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

# initialiser spipbb
include_spip('inc/spipbb_init'); // + spipbb_util + spipbb_presentation + spipbb_menus_gauche

# requis de cet exec
include_spip('inc/spipbb_inc_config');
include_spip('inc/spipbb_inc_metas');

// ------------------------------------------------------------------------------
// [fr] Affichage de la page de configruation generale du plugin
// ------------------------------------------------------------------------------
function exec_spipbb_configuration() {
	# requis spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;

	# reserve au Admins
	if ($connect_statut!='0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		echo fin_page();
		exit;
	}
	$cmd=_request('cmd');


	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "configuration", "spipbb_configuration");
	echo barre_onglets("configuration", 'spipbb_configuration');

	echo "<a name='haut_page'></a>";

	echo debut_gauche('',true);
	spipbb_menus_gauche(_request('exec'));

	echo creer_colonne_droite('',true);
	// Explication + aide + lien téléchargement
	echo signature_spipbb_admin(); // dans inc/spipbb_presentation

	echo debut_droite('',true);

	//$spipbb_param_tech = charger_fonction('spipbb_param_tech', 'configuration');
	//echo $spipbb_param_tech();

	# install ou maj
	echo spipbb_admin_configuration();

	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();
} // exec_spipbb_config


// ------------------------------------------------------------------------------
// [fr] Affiche la partie configuration des forums avec le fond situe dans prive/
// ------------------------------------------------------------------------------
function spipbb_admin_configuration() {

	spipbb_log('DEBUT',3,"spipbb_configuration()");

	# h : cet appel vers "assembler" et donc l_usage de skel backoffice
	# vont bloquer certaines redef de fonctions spip ...
	# très genant !!!
	# c: 11/1/8 je ne vois pas ce qui est bloque ? precise ?

	// chryjs :  7/9/8 recuperer_fond est maintenant dans inc/utils
	if (!function_exists('recuperer_fond')) include_spip('inc/utils');


	$contexte = array(
			'lien_action' => generer_action_auteur('spipbb_admin_reconfig', 'save',generer_url_ecrire('spipbb_configuration')), // generer_url_action ?
			'exec_script' => 'spipbb_admin_reconfig',
			'etat_general' => $etat_general ,
			'prerequis' => $prerequis ? 'oui':'non',
			'config_spipbb' => $GLOBALS['spipbb']['configure'],
			'spipbb_id_secteur' => $GLOBALS['spipbb']['id_secteur'] ,
			'id_groupe_mot' => $GLOBALS['spipbb']['id_groupe_mot'] ,
			'id_mot_ferme' => $GLOBALS['spipbb']['id_mot_ferme'],
			'id_mot_annonce' => $GLOBALS['spipbb']['id_mot_annonce'],
			'id_mot_postit' => $GLOBALS['spipbb']['id_mot_postit'],
			'squelette_groupeforum' => $GLOBALS['spipbb']['squelette_groupeforum'],
			'squelette_filforum' => $GLOBALS['spipbb']['squelette_filforum'],
			'fixlimit' => $GLOBALS['spipbb']['fixlimit'],
			'lockmaint' => $GLOBALS['spipbb']['lockmaint'],
			'affiche_bouton_abus' => $GLOBALS['spipbb']['affiche_bouton_abus'],
			'affiche_bouton_rss' => $GLOBALS['spipbb']['affiche_bouton_rss'],
			'affiche_membre_defaut' => $GLOBALS['spipbb']['affiche_membre_defaut'],
			'log_level' => $GLOBALS['spipbb']['log_level'],
			);
	$res = recuperer_fond("prive/spipbb_admin_configuration",$contexte) ;
	spipbb_log('END',3,"spipbb_configuration()");

	//il faudra forcer le rechargement de cette partie (ou utiliser de quoi cacher dynamiquement
	$configure_spipbb = charger_fonction('spipbb', 'configuration');
	$res = $configure_spipbb();
		
	$etat_spipbb = $GLOBALS['spipbb']['configure'];
	if ($etat_spipbb == "oui") 
	{
		$res .= "<div id='etat-spipbb' style='display:block;'>"; // id defini dans configuration/spipbb
	} else
	{
		$res .= "<div id='etat-spipbb' style='display:none;'>"; // id defini dans configuration/spipbb
	}

		$configure_id_secteur_spipbb = charger_fonction('spipbb_rubriques', 'configuration');
		$res .= $configure_id_secteur_spipbb();
		$configure_mots_spipbb = charger_fonction('spipbb_mots_cles','configuration');
		$res .= $configure_mots_spipbb();		
		$configure_squelettes = charger_fonction('spipbb_squelettes','configuration');
		$res .= $configure_squelettes();		
		$configure_affichage = charger_fonction('spipbb_affichage','configuration');
		$res .= $configure_affichage();		
		$configure_support_auteurs = charger_fonction('spipbb_support_auteurs','configuration');
		$res .= $configure_support_auteurs();		
		$configure_champs_supp = charger_fonction('spipbb_champs_supp','configuration');
		$res .= $configure_champs_supp();		
		
		$res .= "</div>";

	return $res;
} // spipbb_admin_configuration





?>