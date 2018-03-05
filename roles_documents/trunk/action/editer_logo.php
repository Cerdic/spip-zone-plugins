<?php
/**
 * Rôles de documents : gestion de l'API de modification/suppression des logos
 *
 * Surcharge du core.
 *
 * @plugin     Rôles de documents
 * @copyright  2015-2018
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Core\Logo\Edition
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Supprimer le logo d'un objet
 *
 * @uses dissocier_document_role()
 * @uses chercher_logo()
 * 
 * @param string $objet
 * @param int $id_objet
 * @param string $role
 *     - Rôle pour un document : `logo`, `logo_survol`, etc.
 *     - `on` ou `off` pour un vieux logo
 * @return bool
 */
function logo_supprimer($objet, $id_objet, $role) {

	$res = false;
	$objet = objet_type($objet);
	$id_table_objet = id_table_objet($objet);
	$etats = array('on', 'off'); // États des vieux logos

	// Vieux logos
	if (in_array($role, $etats)) {
		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		$logo = $chercher_logo($id_objet, $id_table_objet, $role, true);
		if ($logo) {
			$res = spip_unlink($logo[0]);
		}

	// Documents
	// On n'appelle pas directement supprimer_document()
	// Il faut faire diverses choses avant, ce dont dissocier_document_role() se charge.
	} else {
		include_spip('action/editer_liens');
		if ($lien_role = objet_trouver_liens(
				array('document' => '*'),
				array($objet => $id_objet),
				array('role' => $role)
			)
			and $id_document = intval(array_shift(array_column($lien_role, 'id_document')))
		) {
			$dissocier_document_role = charger_fonction('dissocier_document_role', 'action');
			$arg = "$id_objet-$objet-$id_document-$role-suppr-";
			$res = $dissocier_document_role($arg);
		}
	}

	return $res;
}

/**
 * Modifier ou créer le logo d'un objet
 *
 * [TODO] : compatibilité avec les documents (création : action/ajouter_documents, modification : action/editer_document)
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $etat
 *     `on` ou `off`
 * @param string|array $source
 *     - array : sous tableau de `$_FILE` issu de l'upload
 *     - string : fichier source (chemin complet ou chemin relatif a `tmp/upload`)
 * @return string
 *     Erreur, sinon ''
 */
function logo_modifier($objet, $id_objet, $etat, $source) {
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$objet = objet_type($objet);
	$primary = id_table_objet($objet);
	include_spip('inc/chercher_logo');
	$type = type_du_logo($primary);

	// nom du logo
	$nom = $type . $etat . $id_objet;

	// supprimer le logo eventueel existant
	logo_supprimer($objet, $id_objet, $etat);

	include_spip('inc/documents');
	$erreur = '';

	if (!$source) {
		spip_log('spip_image_ajouter : source inconnue');
		$erreur = 'source inconnue';

		return $erreur;
	}

	$file_tmp = _DIR_LOGOS . $nom . '.tmp';

	$ok = false;
	// fichier dans upload/
	if (is_string($source)) {
		if (file_exists($source)) {
			$ok = @copy($source, $file_tmp);
		} elseif (file_exists($f = determine_upload() . $source)) {
			$ok = @copy($f, $file_tmp);
		}
	} elseif (!$erreur = check_upload_error($source['error'], '', true)) {
		// Intercepter une erreur a l'envoi
		// analyse le type de l'image (on ne fait pas confiance au nom de
		// fichier envoye par le browser : pour les Macs c'est plus sur)
		$ok = deplacer_fichier_upload($source['tmp_name'], $file_tmp);
	}

	if ($erreur) {
		return $erreur;
	}
	if (!$ok or !file_exists($file_tmp)) {
		spip_log($erreur = "probleme de copie pour $file_tmp ");

		return $erreur;
	}

	$size = @getimagesize($file_tmp);
	$extension = !$size ? '' : ($size[2] > 3 ? '' : $GLOBALS['formats_logos'][$size[2] - 1]);
	if ($extension) {
		@rename($file_tmp, $file_tmp . ".$extension");
		$file_tmp = $file_tmp . ".$extension";
		$poids = filesize($file_tmp);

		if (defined('_LOGO_MAX_WIDTH') or defined('_LOGO_MAX_HEIGHT')) {
			if ((defined('_LOGO_MAX_WIDTH') and _LOGO_MAX_WIDTH and $size[0] > _LOGO_MAX_WIDTH)
				or (defined('_LOGO_MAX_HEIGHT') and _LOGO_MAX_HEIGHT and $size[1] > _LOGO_MAX_HEIGHT)
			) {
				$max_width = (defined('_LOGO_MAX_WIDTH') and _LOGO_MAX_WIDTH) ? _LOGO_MAX_WIDTH : '*';
				$max_height = (defined('_LOGO_MAX_HEIGHT') and _LOGO_MAX_HEIGHT) ? _LOGO_MAX_HEIGHT : '*';

				// pas la peine d'embeter le redacteur avec ca si on a active le calcul des miniatures
				// on met directement a la taille maxi a la volee
				if (isset($GLOBALS['meta']['creer_preview']) and $GLOBALS['meta']['creer_preview'] == 'oui') {
					include_spip('inc/filtres');
					$img = filtrer('image_reduire', $file_tmp, $max_width, $max_height);
					$img = extraire_attribut($img, 'src');
					$img = supprimer_timestamp($img);
					if (@file_exists($img) and $img !== $file_tmp) {
						spip_unlink($file_tmp);
						@rename($img, $file_tmp);
						$size = @getimagesize($file_tmp);
					}
				}
				// verifier au cas ou image_reduire a echoue
				if ((defined('_LOGO_MAX_WIDTH') and _LOGO_MAX_WIDTH and $size[0] > _LOGO_MAX_WIDTH)
					or (defined('_LOGO_MAX_HEIGHT') and _LOGO_MAX_HEIGHT and $size[1] > _LOGO_MAX_HEIGHT)
				) {
					spip_unlink($file_tmp);
					$erreur = _T(
						'info_logo_max_poids',
						array(
							'maxi' =>
								_T(
									'info_largeur_vignette',
									array(
										'largeur_vignette' => $max_width,
										'hauteur_vignette' => $max_height
									)
								),
							'actuel' =>
								_T(
									'info_largeur_vignette',
									array(
										'largeur_vignette' => $size[0],
										'hauteur_vignette' => $size[1]
									)
								)
						)
					);
				}
			}
		}

		if (!$erreur and defined('_LOGO_MAX_SIZE') and _LOGO_MAX_SIZE and $poids > _LOGO_MAX_SIZE * 1024) {
			spip_unlink($file_tmp);
			$erreur = _T(
				'info_logo_max_poids',
				array(
					'maxi' => taille_en_octets(_LOGO_MAX_SIZE * 1024),
					'actuel' => taille_en_octets($poids)
				)
			);
		}

		if (!$erreur) {
			@rename($file_tmp, _DIR_LOGOS . $nom . ".$extension");
		}
	} else {
		spip_unlink($file_tmp);
		$erreur = _T(
			'info_logo_format_interdit',
			array('formats' => join(', ', $GLOBALS['formats_logos']))
		);
	}

	return $erreur;
}
