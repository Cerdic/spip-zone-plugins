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

/**
 * Charger la fonction du service
 * Utiliser charger_fonction dans une fonction balise provoque des bugs
 *
 * @access public
 */
function connecteur_lien($source) {
	// On appel la fonction du service
	$f = charger_fonction($source.'_lien', 'connecteur');
	return $f();
}


/**
 * Cette fonction va créer un auteur SPIP en fonction d'un tableau
 * de donnée simple
 *
 * ```
 * array('nom' => 'truc', 'email' => 'truc@machin.be')
 * ```
 *
 * @param mixed $source
 * @access public
 */
function connecteur_creer_auteur($info, $statut = '6forum') {

	// Inscrire l'auteur sur base des informations du connecteur
	$inscrire_auteur = charger_fonction('inscrire_auteur', 'action');
	$desc = $inscrire_auteur(
		$statut,
		$info['email'],
		$info['nom']
	);

	// Envoyer aux pipelines
	$desc = pipeline('post_connecteur', $desc);

	return $desc;
}

}
