<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

define("_DIR_SIMPLECAL_IMG_PACK", _DIR_PLUGIN_SIMPLECAL."img_pack/");
define("_DIR_SIMPLECAL_PRIVE", _DIR_PLUGIN_SIMPLECAL."prive/");


// Necessaire au bouton d'administration 'Evènement 123' de l'espace public
// Mais pour le voir, il faut rajouter un 'evenement' dans le foreach de admin_objet() dans /ecrire/balise/formulaire_admin.php
function generer_url_ecrire_evenement($id, $args='', $ancre='', $statut='', $connect='') {
	$a = "id_evenement=" . intval($id);
	if (!$statut) {
		$statut = sql_getfetsel('statut', 'spip_evenements', $a,'','','','',$connect);
	}
	$h = ($statut == 'publie' OR $connect)
	? generer_url_entite_absolue($id, 'evenement', $args, $ancre, $connect)
	: (generer_url_ecrire('evenement_voir', $a . ($args ? "&$args" : ''))
		. ($ancre ? "#$ancre" : ''));
	return $h;
}


// ------------------------------------
//  Plugin Corbeille - compatibilite
// ------------------------------------
global $corbeille_params;
$corbeille_params["evenements"] = array (
    "statut" => "poubelle",
    "table" => "spip_evenements",
    "tableliee"  => array("spip_mots_evenements"),
);


?>
