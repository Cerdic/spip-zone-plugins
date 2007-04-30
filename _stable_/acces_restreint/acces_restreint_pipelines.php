<?php

// * Acces restreint, plugin pour SPIP * //

if (!defined("_ECRIRE_INC_VERSION")) return;


if (!defined('_DIR_PLUGIN_ACCESRESTREINT')){ // definie automatiquement en 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ACCESRESTREINT',(_DIR_PLUGINS.end($p)));
}

	/* public static */
	function AccesRestreint_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['configuration']->sousmenu['acces_restreint']= new Bouton(
			"../"._DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif",  // icone
			_T('accesrestreint:icone_menu_config')	// titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function AccesRestreint_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}

	function AccesRestreint_affiche_milieu($flux){
		switch($flux['args']['exec']) {
			case 'auteurs_edit':
			case 'auteur_infos':
				include_spip('inc/acces_restreint_gestion');
				$id_auteur = $flux['args']['id_auteur'];
				$nouv_zone = _request('nouv_zone');
				$supp_zone = _request('supp_zone');
				// le formulaire qu'on ajoute
				global $connect_statut;
				$flux['data'] .= AccesRestreint_formulaire_zones('auteurs', $id_auteur, $nouv_zone, $supp_zone, $connect_statut == '0minirezo', generer_url_ecrire('auteurs_edit',"id_auteur=$id_auteur"));
				break;
			case 'config_fonctions':
				AccesRestreint_htpasswd_config();
				AccesRestreint_htaccess_config();
				break;
			default:
				break;
		}

		return $flux;
	}


//
// Le core de SPIP sait gerer ces options de configuration
//
function AccesRestreint_htaccess_config() {

	global $spip_lang_right;

	debut_cadre_trait_couleur("cadenas-24.gif", false, "", 
			  _L("Acc&egrave;s aux document joints par leur URL"));
#	include_spip('inc/acces'); vient d'etre fait
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