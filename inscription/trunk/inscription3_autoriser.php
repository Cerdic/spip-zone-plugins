<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2012 - cmtmt, BoOz, kent1
 * Licence GPL v3
 * 
 * Fichiers de fonctions d'autorisations spécifiques
 */

 if (!defined("_ECRIRE_INC_VERSION")) return;

function inscription3_autoriser(){}

/**
 * Autoriser les utilisateurs à modifier leur profil
 * 
 * On garde les autorisations par défaut pour les administrateurs et les rédacteurs
 * Par contre on autorise les visiteurs (6forum) à modifier un profil:
 * -* s'il sont eux même l'utilisateur à modifier
 * -* s'ils ont le bon statut
 * -* si on ne souhaite pas modifier le statut
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 */
if (!function_exists('autoriser_auteur_modifier')) {
function autoriser_auteur_modifier($faire, $type, $id, $qui, $opt) {
	// Admin ou redacteur => On utilise la fonction par défaut
	if (in_array($qui['statut'], array('0minirezo', '1comite')))
		return autoriser_auteur_modifier_dist($faire, $type, $id, $qui, $opt);
	// Un utilisateur normal n'a jamais le droit de modifier son statut
	// Ni les champs qui ne sont pas dans _fiche_mod
	else if(isset($opt['champ'])){
		return
			!$opt['statut']
			AND (lire_config('inscription3/'.$opt['champ'].'_fiche_mod','off') == 'on')
			AND $qui['statut'] == '6forum'
			AND $id == $qui['id_auteur'];	
	}else
		return
			!$opt['statut']
			AND $qui['statut'] == '6forum'
			AND $id == $qui['id_auteur'];
	}
}

/**
 * Autoriser les utilisateurs à modifier leur logo
 * 
 * On garde les autorisations par défaut pour les administrateurs
 * Par contre on autorise les visiteurs (6forum) et rédacteurs (1comite) à modifier leur logo:
 * -* s'il sont eux même l'utilisateur à modifier
 * -* s'ils ont le bon statut
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 */
if(!function_exists('autoriser_auteur_iconifier')){
	function autoriser_auteur_iconifier($faire, $type, $id, $qui, $opt) {
		if (in_array($qui['statut'], array('0minirezo')))
			return autoriser_auteur_iconifier_dist($faire, $type, $id, $qui, $opt);
		else
			return
			in_array($qui['statut'],array('6forum','1comite'))
			AND $id == $qui['id_auteur'];
	}
}
?>