<?php

/**
 * Déclaration systématiquement chargées
 *
 * @package SPIP\Saisies
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


if (!function_exists('_T_ou_typo')) {
	/**
	 * une fonction qui regarde si $texte est une chaine de langue
	 * de la forme <:qqch:>
	 * si oui applique _T()
	 * si non applique typo() suivant le mode choisi
	 *
	 * @param mixed $valeur
	 *     Une valeur à tester. Si c'est un tableau, la fonction s'appliquera récursivement dessus.
	 * @param string $mode_typo
	 *     Le mode d'application de la fonction typo(), avec trois valeurs possibles "toujours", "jamais" ou "multi".
	 * @return mixed
	 *     Retourne la valeur éventuellement modifiée.
	 */
	function _T_ou_typo($valeur, $mode_typo = 'toujours') {
		if (!in_array($mode_typo, array('toujours', 'multi', 'jamais'))) {
			$mode_typo = 'toujours';
		}

		// Si la valeur est bien une chaine (et pas non plus un entier déguisé)
		if (is_string($valeur) and !is_numeric($valeur)) {
			// Si on est en >=3.2, on peut extraire les <:chaine:>
			$version = explode('.',$GLOBALS['spip_version_branche']);
			$extraction_chaines = (($version[0] > 3 or $version[1] >= 2) ? true : false);
			// Si la chaine est du type <:truc:> on passe à _T()
			if (strpos($valeur, '<:') !== false
			  and preg_match('/^\<:([^>]*?):\>$/', $valeur, $match)) {
				$valeur = _T($match[1]);
			} else {
				// Sinon on la passe a typo() si c'est pertinent
				if (
					$mode_typo === 'toujours'
					or ($mode_typo === 'multi' and strpos($valeur, '<multi>') !== false)
					or ($extraction_chaines
					  and $mode_typo === 'multi'
					  and strpos($valeur, '<:') !== false
					  and include_spip('inc/filtres')
					  and preg_match(_EXTRAIRE_IDIOME, $valeur))
				) {
					include_spip('inc/texte');
					$valeur = typo($valeur);
				}
			}
		}
		// Si c'est un tableau, on réapplique la fonction récursivement
		elseif (is_array($valeur)) {
			foreach ($valeur as $cle => $valeur2) {
				$valeur[$cle] = _T_ou_typo($valeur2, $mode_typo);
			}
		}

		return $valeur;
	}
}
