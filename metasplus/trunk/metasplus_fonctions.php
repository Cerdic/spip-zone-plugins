<?php
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/* rétro-compatibilité pour SPIP 3.0 */
include_spip('inc/filtres');
if (!function_exists('supprimer_timestamp')) {
	function supprimer_timestamp($url) {
			if (strpos($url, "?") === false) {
					return $url;
			}
			return preg_replace(",\?[[:digit:]]+$,", "", $url);
	}
}
include_spip('public/quete');
if (!function_exists('quete_logo_objet')) {
	function quete_logo_objet($id_objet, $objet, $mode) {
			static $chercher_logo;
			if (is_null($chercher_logo)) {
					$chercher_logo = charger_fonction('chercher_logo', 'inc');
			}
			$cle_objet = id_table_objet($objet);

			// On cherche pas la méthode classique
			$infos_logo = $chercher_logo($id_objet, $cle_objet, $mode);

			// Si la méthode classique a trouvé quelque chose, on utilise le nouveau format
			if (!empty($infos_logo)) {
					$infos_logo = array(
							'chemin' => $infos_logo[0],
							'timestamp' => $infos_logo[4],
					);
			}

			// On passe cette recherche de logo dans un pipeline
			$infos_logo = pipeline(
					'quete_logo_objet',
					array(
							'args' => array(
									'id_objet' => $id_objet,
									'objet' => $objet,
									'cle_objet' => $cle_objet,
									'mode' => $mode,
							),
							'data' => $infos_logo,
					)
			);
			return $infos_logo;
	}
}
