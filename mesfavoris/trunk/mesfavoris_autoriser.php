<?php
/**
 * Plugin mesfavoris
 * (c) 2009-2013 Olivier Sallou, Cedric Morin, Gilles Vincent
 * Distribue sous licence GPL
 *
 */

/**
 * Définit les autorisations du plugin mesfavoris
 *
 * @package SPIP\Mesfavoris\Autorisations
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function mesfavoris_autoriser(){}

/**
 * Contrôle l'accès sur la modification d'un favori via le plugin
 * Ici les admins (non restreint) ont tous les droits
 * Et les auteurs peuvent modifier leurs favoris
 *
 * @pipeline autoriser
 * @param  string $faire Action
 * @param  string $type  Type d'objet
 * @param  integer $id   id de l'objet
 * @param  array $qui    celui qui veut réaliser l'action $faire sur l'objet
 * @param  array $opt    
 * @return boolean       true si la modification est possible
 */
function autoriser_favori_modifier_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut'] == '0minirezo' AND !$qui['restreint'])
		return true;
	else{
		$auteur_favori = sql_getfetsel('id_auteur','spip_favoris','id_favori='.intval($id));
		return ($qui['id_auteur'] == $auteur_favori);
	}
}

?>