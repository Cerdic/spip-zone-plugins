<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Inscrit un suscriber par son email
 * si le suscriber existe deja, on met a jour les informations (nom, listes, lang)
 * l'ajout d'une inscription a une liste est cumulatif : si on appelle plusieurs fois la fonction avec le meme email
 * et plusieurs listes differentes, l'inscrit sera sur chaque liste
 * Pour retirer une liste il faut desinscrire
 *
 * Quand aucune liste n'est indiquee :
 *   si l'email n'est inscrit a rien, on l'inscrit a la liste generale 'newsletter'
 *   si l'email est deja inscrit, on ne change pas ses inscriptions, mais on modifie ses informations (nom, lang)
 *
 * @param $email
 *   champ obligatoire
 * @param array $options
 *   nom : string
 *   listes : array (si non fourni, inscrit a la liste generale 'newsletter')
 *   lang : string
 * @return bool
 *   true si desinscrit comme demande, false sinon
 */
function newsletter_suscribe_dist($email,$options = array()){

	return true;
}