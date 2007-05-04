<?php

// * Acces restreint, plugin pour SPIP * //

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acces_restreint_base');
include_spip('inc/acces_restreint');
include_spip('inc/acces_restreint_gestion');
include_spip('inc/presentation');
include_spip('inc/config');


// devrait etre une bonne "action"
function AccesRestreint_appliquer_modifs_config() {
	include_spip('inc/meta');

	// modifs de secu (necessitent une authentification ftp)
	$liste_meta = array(
		'creer_htpasswd',
		'creer_htaccess'
	);
	foreach($liste_meta as $i) {
		if (_request($i) !== NULL
		AND _request($i) != $GLOBALS['meta'][$i]) {

			$admin = _T('info_modification_parametres_securite');
			include_spip('inc/admin');
			debut_admin(_request('exec'), $admin);
			foreach($liste_meta as $i) {
				if (_request($i) !== NULL) {
					ecrire_meta($i, _request($i));
				}
			}
			ecrire_metas();
			fin_admin($admin);
			break;
		}
	}

}

// affiche la page de configuration
function exec_acces_restreint_config(){

	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		minipres();
		exit;
	}

	init_config();
	if (_request('changer_config') == 'oui')
		AccesRestreint_appliquer_modifs_config();

	pipeline('exec_init',array('args'=>array('exec'=>'acces_restreint_config'),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_page_config'), "configuration", "configuration");

	echo "<br /><br /><br />";
	gros_titre(_T('titre_config_fonctions'));

	debut_gauche();
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'acces_restreint_config'),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'acces_restreint_config'),'data'=>''));


	//Raccourcis
	$res = icone_horizontale(_T('accesrestreint:voir_toutes'), generer_url_ecrire("acces_restreint",''), "../"._DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif", 'rien.gif',false);
	echo bloc_des_raccourcis($res);


	debut_droite();
	lire_metas();

	$action = generer_url_ecrire('acces_restreint_config');

        echo "<form action='$action' method='post'><div>", form_hidden($action);
	echo "<input type='hidden' name='changer_config' value='oui' />";


	AccesRestreint_htaccess_config();
	AccesRestreint_htpasswd_config();


	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'acces_restreint_config'),'data'=>''));

	echo fin_gauche(), fin_page();
}


//
// Le core de SPIP sait gerer ces options de configuration
//
function AccesRestreint_htaccess_config() {

	global $spip_lang_right;

	debut_cadre_trait_couleur("cadenas-24.gif", false, "", 
			  _L("Acc&egrave;s aux document joints par leur URL"));
	include_spip('inc/acces');
	$creer_htaccess = gerer_htaccess();

	echo "<div class='verdana2'>";
	echo _L("Cette option interdit la lecture des documents joints si le texte auquel ils se rattachent n'est pas publi&eacute");
	echo "</div>";

	echo "<div class='verdana2'>";
	echo afficher_choix('creer_htaccess', $creer_htaccess,
		       array('oui' => _L("interdire la lecture"),
			     'non' => _L("autoriser la lecture")),
		       ' &nbsp; ');
	echo "</div>";
	echo "<div style='text-align:$spip_lang_right'><input type='submit'  value='"._T('bouton_valider')."' class='fondo' /></div>";
	
	fin_cadre_trait_couleur();

	echo "<br />";
}

function AccesRestreint_htpasswd_config() {
	global $spip_lang_right;

	include_spip('inc/acces');
	ecrire_acces();

	debut_cadre_trait_couleur("cadenas-24.gif", false, "",
		_T('info_fichiers_authent'));

	$creer_htpasswd = $GLOBALS['meta']["creer_htpasswd"];

	echo "<div class='verdana2'>", _T('texte_fichier_authent', array('dossier' => '<tt>'.joli_repertoire(_DIR_TMP).'</tt>')), "</div>";

	echo "<div class='verdana2'>";
	echo afficher_choix('creer_htpasswd', $creer_htpasswd,
		array('oui' => _T('item_creer_fichiers_authent'),
			'non' =>  _T('item_non_creer_fichiers_authent')),
		' &nbsp; ');
	echo "</div>";
	echo "<div style='text-align:$spip_lang_right'><input type='submit' value='"._T('bouton_valider')."' class='fondo' /></div>";
	
	fin_cadre_trait_couleur();
}

?>
