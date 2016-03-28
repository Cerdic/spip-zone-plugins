<?php
/**
 * Fonctions utiles au plugin Connection
 *
 * @plugin     Connection
 * @copyright  2016
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Connecteur\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Balise des connecteurs
 *
 * Active le lien de connection spécifique à un connecteur
 *
 * ```
 * #CONNECTEUR_FACEBOOK
 * ```
 * Cette balise appel une fonction du dossier connecteur: `connecteur_facebook_lien`
 *
 * @param mixed $p
 * @access public
 */
function balise_CONNECTEUR__dist($p) {

	// Récupérer le type de connecteur
	// Le substr supprime la partie "CONNECTEUR_" pour ne garder que la source
	$connecteur_type = strtolower(substr($p->nom_champ, 11));

	$p->code = "connecteur_lien('$connecteur_type')";
	$p->interdire_scripts = false;

	return $p;
}

function connecteur_lien($source) {
	// On appel la fonction du service
	$f = charger_fonction($source.'_lien', 'connecteur');
	return $f();
}


/**
 * Cette fonction va créer un auteur SPIP en fonction d'une source
 * La source est définie par un plugin externe sous la forme
 *
 * ```
 * function connecteur_SOURCE_dist()
 * ```
 * Cette fonction connecteur ce charge de fournir un tableau d'information
 * qui servira créer l'auteur SPIP
 *
 * ```
 * array('nom' => 'truc', 'email' => 'truc@machin.be')
 * ```
 *
 * @param mixed $source
 * @access public
 */
function connecteur_creer_auteur($source, $statut = '6forum') {

	// On commence par charger la fonction appeler
	$connecteur = charger_fonction('connecteur_'.$source);

	// Connecteur valide ?
	if (is_array($connecteur)) {

		// Inscrire l'auteur sur base des informations du connecteur
		$inscrire_auteur = charger_fonction('inscrire_auteur', 'action');
		$desc = $inscrire_auteur(
			$statut,
			$connecteur['email'],
			$connecteur['nom']
		);

		// Envoyer aux pipelines
		$desc = pipeline('post_connecteur', $desc);
	}
}
