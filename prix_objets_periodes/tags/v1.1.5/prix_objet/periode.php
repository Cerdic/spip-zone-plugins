<?php
/**
 * Fonctions relatives au fonctionnement des extension à Prix Objets
 *
 * @plugin     Prix objets par périodes
 * @copyright  2012 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\prix_objets_periodes\Extensions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Détermine si une extension est applicable pour un objet
 *
 * @param integer $id_periode
 * @param array $contexte
 * @return boolean
 */
function prix_objet_periode_dist($id_periode, $contexte = array()) {
	$verifier_periode = charger_fonction('periode_verifier', 'inc');

	return $verifier_periode($id_periode, $contexte);
}

