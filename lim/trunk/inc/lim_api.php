<?php
/**
 * Fonctions utiles au plugin Lim
 *
 * @plugin     Lim
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Lim\Inc
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Vérifier si il existe déjà des logos de téléchargés pour un type d'objet
 * Exception : le logo du site (dans 'Identité du site') n'est pas pris en compte
 * 
 * @type string
 * @return bool
 */
function verifier_presence_logo($type) {
	include_spip('inc/chercher_logo');
	include_spip('base/objets');
	$id_objet = id_table_objet($type);
	$prefixe_logo = _DIR_LOGOS.type_du_logo($id_objet).'*.*';
	$liste_logos = glob($prefixe_logo);

	// ne pas prenfdre en compte le logo du site (id = 0)
	if ($type == 'spip_syndic') {
		$chercher_logo = charger_fonction('chercher_logo','inc');
		$logo_du_site = $chercher_logo(0,'id_syndic');
		$prefixe_logo_du_site[] = $logo_du_site[0];
		$liste_logos = array_diff($liste_logos, $prefixe_logo_du_site);
	}
	
	if (count($liste_logos) > 0) return true;
	return false;
}

/**
 * Vérifier si il existe déjà des pétitions
 * @return bool
 */
function verifier_presence_petitions() {
	/* recherche de pétitions */
	if (sql_countsel('spip_petitions', "statut='publie'") > 0) {
		return true;
	}
	return false;
}

/**
 * Vérifier si il existe déjà des objets dans la rubrique
 * on renvoi un tableau avec le type et la table_objet
 * @param int $id_rubrique
 * @param string $objet
 * @return bool
 */
function verifier_presence_objets($id_rubrique, $objet) {
	$table = table_objet_sql($objet);
	if (sql_countsel($table, "id_rubrique=$id_rubrique") > 0) return true;
	return false;
}

/**
 * Traitement des cas d'installation/ désinstalation de plugin(s) intégrant un objet éditable
 * Il faut alors mettre à jour la méta 'lim_logos' (action réalisée dans le formulaire 'configurer_lim_logos.php')
 * 
 * @return void
 */
function traitement_plugins() {
	$tables_principales = lister_tables_principales_editables();
	
	// traitement du cas d'un nouveau plugin intégrant un objet éditable
	foreach($tables_principales as $key => $type) {
		if (is_null(lire_config('lim_logos/'.$type))) {
			ecrire_config('lim_logos/'.$type, 'oui');
		}
	}

	// traitement du cas d'un plugin désinstallé qui intégrait un objet éditable
	$config_logo = lire_config('lim_logos');
	$test_table = array_flip($tables_principales);
	$diff = array_diff_key($config_logo, $test_table);

	if(count($diff) > 0) {
		foreach ($diff as $key => $value) {
			effacer_config('lim_logos/'.$key);
		}
	}
}

?>