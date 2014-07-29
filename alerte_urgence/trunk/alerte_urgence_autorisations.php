<?php
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function alerte_urgence_autoriser($flux) {return $flux;}

/**
 * Autorisation de configurer l'alerte d'urgence
 * - celleux qui peuvent configurer le site complet
 * - plus celleux qui sont dans une liste supplémentaire
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_alerteurgence_configurer($faire, $quoi, $id, $qui, $options) {
	if (
		autoriser('configurer')
		or (
			include_spip('inc/config')
			and $utilisateurs = lire_config('alerte_urgence/utilisateurs')
			and is_array($utilisateurs)
			and in_array(intval($id), $utilisateurs)
		)
	) {
		return true;
	}
	
	return false;
}
