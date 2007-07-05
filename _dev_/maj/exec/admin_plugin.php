<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include('exec/admin_plugin.php');

function exec_admin_plugin() {
	global $spip_lang_right;

	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	verif_plugin();

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('icone_admin_plugin'), "configuration", "plugin");
	echo "<br/><br/><br/>";
	echo "<hr />test surcharge<hr />";
	
	echo gros_titre(_T('icone_admin_plugin'),'',false);
	// barre_onglets("configuration", "plugin"); // a creer dynamiquement en fonction des plugin charges qui utilisent une page admin ?
	
	echo debut_gauche('plugin',true);
	echo debut_boite_info(true);
	$s = "";
	$s .= _T('info_gauche_admin_tech');
	$s .= "<p><img src='"._DIR_IMG_PACK . "puce-poubelle.gif' width='9' height='9' alt='' /> "._T('plugin_etat_developpement')."</p>";
	$s .= "<p><img src='"._DIR_IMG_PACK . "puce-orange.gif' width='9' height='9' alt='' /> "._T('plugin_etat_test')."</p>";
	$s .= "<p><img src='"._DIR_IMG_PACK . "puce-verte.gif' width='9' height='9' alt='' /> "._T('plugin_etat_stable')."</p>";
	$s .= "<p><img src='"._DIR_IMG_PACK . "puce-rouge.gif' width='9' height='9' alt='' /> "._T('plugin_etat_experimental')."</p>";
	echo $s;
	echo fin_boite_info(true);

	// on fait l'installation ici, cela permet aux scripts d'install de faire des affichages ...
	installe_plugins();

	echo debut_droite('plugin',true);
	if (isset($GLOBALS['meta']['plugin_erreur_activation'])){
		echo $GLOBALS['meta']['plugin_erreur_activation'];
		effacer_meta('plugin_erreur_activation');
	}

	echo debut_cadre_trait_couleur('',true,'',_T('plugins_liste'),'liste_plugins');
	echo _T('texte_presente_plugin');

	$lpf = liste_plugin_files();
	$lcpa = liste_chemin_plugin_actifs();

	$sub = "\n<div style='text-align:$spip_lang_right'>"
	.  "<input type='submit' value='"._T('bouton_valider')."' class='fondo' />"
	. "</div>";

	$corps = $sub
	. affiche_arbre_plugins($lpf, $lcpa)
	. "\n<br />"
	. $sub;

	echo redirige_action_auteur('activer_plugins','activer','admin_plugin','', $corps, " method='post'");

	echo fin_cadre_trait_couleur(true);
	echo fin_gauche(), fin_page();

}

?>