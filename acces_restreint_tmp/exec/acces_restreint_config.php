<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acces_restreint_base');
include_spip('inc/acces_restreint_gestion');
include_spip('inc/presentation');
include_spip('inc/config');


// devrait etre une bonne "action"
function accesrestreint_appliquer_modifs_config() {
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
			echo debut_admin(_request('exec'), $admin);
			foreach($liste_meta as $i) {
				if (_request($i) !== NULL) {
					ecrire_meta($i, _request($i));
				}
			}
			ecrire_metas();
			echo fin_admin($admin);
			break;
		}
	}

}

// affiche la page de configuration
function exec_acces_restreint_config(){

	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	include_spip('inc/config');
	inc_config_dist();
	if (_request('changer_config') == 'oui')
		accesrestreint_appliquer_modifs_config();

	pipeline('exec_init',array('args'=>array('exec'=>'acces_restreint_config'),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_page_config'), "configuration", "configuration");

	echo gros_titre(_T('titre_config_fonctions'),'',false);

	echo debut_gauche('',true);
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'acces_restreint_config'),'data'=>''));
	echo creer_colonne_droite('',true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'acces_restreint_config'),'data'=>''));


	//Raccourcis
	$res = icone_horizontale(_T('accesrestreint:voir_toutes'), generer_url_ecrire("acces_restreint",''), "../"._DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif", 'rien.gif',false);
	echo bloc_des_raccourcis($res);


	echo debut_droite('',true);
	lire_metas();

	$action = generer_url_ecrire('acces_restreint_config');

	echo "<form action='$action' method='post'><div>", form_hidden($action);
	echo "<input type='hidden' name='changer_config' value='oui' />";


	accesrestreint_htaccess_config();
	accesrestreint_htpasswd_config();
	echo '</div></form>';

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'acces_restreint_config'),'data'=>''));

	echo fin_gauche(), fin_page();
}


//
// Le core de SPIP sait gerer ces options de configuration
//
function accesrestreint_htaccess_config() {

	global $spip_lang_right;

	echo debut_cadre_trait_couleur("cadenas-24.gif", true, "", 
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
	
	echo fin_cadre_trait_couleur(true);

	echo "<br />";
}

function accesrestreint_htpasswd_config() {
	global $spip_lang_right;

	include_spip('inc/acces');
	ecrire_acces();

	echo debut_cadre_trait_couleur("cadenas-24.gif", true, "",
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
	
	echo fin_cadre_trait_couleur(true);
}

?>
