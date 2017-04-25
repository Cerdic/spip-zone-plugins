<?php
/**
 * Utilisations de pipelines par Réservations multiples
 *
 * @plugin     Réservations multiples
 * @copyright  2014
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Reservations_multiples\Pipelines
 */
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Charge les valeurs d'un formulaire
 *
 * @pipeline formulaire_charger
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 *
 */
function reservations_multiples_formulaire_charger($flux) {
	$form = $flux['args']['form'];

	// Le formulaire reservation
	if ($form == 'reservation') {
		$config = reservations_multiples_config();

		// Si inscription de plusieurs personnes
		if ($config['multiple_personnes'] == 'on') {
			$champs_extras_auteurs_add = array();
			$ajouter = array();
			$nombre_auteurs = intval(_request('nr_auteurs')) ? _request('nr_auteurs') : (_request('nombre_auteurs') ? _request('nombre_auteurs') : '');
			if (_request('nr_auteurs') == 'nada')
				$nombre_auteurs = 0;
			$i = 1;
			while ($i <= $nombre_auteurs) {
				$nr = $i++;
				$ajouter[$nr] = $nr;
				$flux['data']['nom_' . $nr] = '';
				$flux['data']['email_' . $nr] = '';
				if ($flux['data']['champs_extras_auteurs']) {
					// Adapter les champs extras
					foreach ($flux['data']['champs_extras_auteurs'] as $key => $value) {
						$flux['data'][$value['options']['nom'] . '_' . $nr] = '';
						$champs_extras_auteurs_add[$nr][$key] = $value;
						$champs_extras_auteurs_add[$nr][$key]['options']['nom'] = $value['options']['nom'] . '_' . $nr;
					}
				}
			}
			$flux['data']['id_reservation_source'] = '';
			$flux['data']['type_lien'] = '';
			$flux['data']['origine_lien'] = '';
			$flux['data']['nombre_auteurs'] = $nombre_auteurs;
			$flux['data']['nr_auteurs'] = '';
			$flux['data']['champs_extras_auteurs_add'] = $champs_extras_auteurs_add;
			$flux['data']['ajouter'] = $ajouter;
			$flux['data']['_hidden'] .= '<input type="hidden" name="nombre_auteurs" value="' . $flux['data']['nombre_auteurs'] . '">';
		}
	}

	return $flux;
}

/**
 * Vérifie les données d'un formulaire
 *
 * @pipeline formulaire_verifier
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 *
 */
function reservations_multiples_formulaire_verifier($flux) {
	$form = $flux['args']['form'];
	if ($form == 'reservation') {
		$config = reservations_multiples_config();

		// Si inscription de plusieurs personnes
		if ($config['multiple_personnes'] == 'on') {
			// enlever le message d'erreur en attendand de comnprendre d'ou vient ce message qui se met d'office
			unset($flux['data']['message_erreur']);

			// Une erreur bidon pour éviter ne pas traiter le formulaire lors de modification de nombre de inscrits
			if (_request('nr_auteurs')) {
				$flux['data'] = array(
					'ajouter' => 'ajouter auteurs'
				);
			}
			elseif ($nombre = _request('nombre_auteurs')) {
				include_spip('inc/saisies');
				include_spip('cextras_pipelines');
				$erreurs = array();

				if (function_exists('champs_extras_objet')) {
					$champs_extras_auteurs = champs_extras_objet(table_objet_sql('auteur'));

					// Stocker les valeurs intitiales des champs extras
					foreach ($champs_extras_auteurs as $key => $value) {
						$$value['options']['nom'] = _request($value['options']['nom']);
					}
				}
				else
					$champs_extras_auteurs = array();

					// Vérification des champs additionnels
				$i = 1;
				while ($i <= $nombre) {
					$nr = $i++;

					// les champs de bases obligatoires
					$obligatoires = array(
						'nom_' . $nr,
						'email_' . $nr
					);

					// Tester les champs de bases obligatoires
					foreach ($obligatoires as $champ) {
						if (!_request($champ))
							$erreurs[$champ] = _T("info_obligatoire");
					}

					if ($email = _request('email_' . $nr)) {
						include_spip('inc/filtres');
						// la validité du mail
						if (!email_valide($email)) {
							$erreurs['email_' . $nr] = _T('form_prop_indiquer_email');
						}
					}

					// Vérifier les champs extras
					foreach ($champs_extras_auteurs as $key => $value) {

						// Adapter les request pour pouvoir faire la vérification des champs extras
						set_request($value['options']['nom'], _request($value['options']['nom'] . '_' . $nr));
						$e = saisies_verifier($champs_extras_auteurs);

						// Adapter le nom du champ
						if (is_array($e)) {
							foreach ($e as $champ => $erreur) {
								$erreurs[$champ . '_' . $nr] = $erreur;
							}
						}
					}
				}

				// Remettre les valeurs initiales
				foreach ($champs_extras_auteurs as $key => $value) {
					set_request($value['options']['nom'], $$value['options']['nom']);
				}
				$flux['data'] = array_merge($flux['data'], $erreurs);

				// remettre le message d'erreur
				if (count($flux['data']) > 0)
					$flux['data']['message_erreur'] = _T('reservation:message_erreur');
			}
		}
	}
	return $flux;
}

/**
 * Traite les formulaires d'un formulaire
 *
 * @pipeline formulaire_traiter
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 *
 */
function reservations_multiples_formulaire_traiter($flux) {
	$form = $flux['args']['form'];
	if ($form == 'reservation' and $nombre = _request('nombre_auteurs')) {
		$config = reservations_multiples_config();

		// Si inscription de plusieurs personnes
		if ($config['multiple_personnes'] == 'on') {
			$noms = array(
				_request('nom')
			);
			// Enregistrement des champs additionnels
			$enregistrer = charger_fonction('reservation_enregistrer', 'inc');

			// Lister les messages de retour
			preg_match('/<h3>(.*?)<\/h3>/s', $flux['data']['message_ok'], $match);
			$titre = $match[0];

			preg_match('/<p(.*?)<\/p>/s', $flux['data']['message_ok'], $match);
			$intro = $match[0];
			preg_match('/<table(.*?)<\/table>/s', $flux['data']['message_ok'], $match);

			$message_ok = array(
				$match[0]
			);

			if (function_exists('champs_extras_objet')) {
				$champs_extras_auteurs = champs_extras_objet(table_objet_sql('auteur'));
			}
			else
				$champs_extras_auteurs = array();
				// ne pas créer de compte spip
			set_request('enregistrer', '');
			$i = 1;

			// inscription aux mailinglistes
			if (test_plugin_actif('reservations_mailsubscribers')) {
				$inscription = charger_fonction('inscription_mailinglinglistes', 'inc');
			}

			// Ajouter les références à la réservation d'origine
			set_request('type_lien', 'multiple_personnes');
			set_request('origine_lien', 'reservations_multiples');

			// Enregistrer les réservations
			while ($i <= $nombre) {
				// recupérer les champs par défaut
				$nr = $i++;
				$email = _request('email_' . $nr);
				set_request('nom', _request('nom_' . $nr));
				set_request('email', $email);
				set_request('id_auteur', '');
				$noms[] = _request('nom');

				// Vérifier les champs extras
				foreach ($champs_extras_auteurs as $key => $value) {

					// récupérer les champs extras
					set_request($value['options']['nom'], _request($value['options']['nom'] . '_' . $nr));
				}

				set_request('nr_auteur', $nr);

				// Enregistrer
				$flux['data'] = $enregistrer('', '', '', $champs_extras_auteurs);
				preg_match('/<table(.*?)<\/table>/s', $flux['data']['message_ok'], $match);
				$message_ok[] = $match['0'];
				$nr = 0;

				// inscription aux mailinglistes
				if (test_plugin_actif('reservations_mailsubscribers')) {
					$inscription($email);
				}
			}
			// Recopiler le messages de retour
			$m = $intro;
			$m .= $titre;
			foreach ($message_ok as $message) {
				$m .= "<h4>$noms[$nr]</h4>";
				$m .= $message;
				$nr++;
			}
			$flux['data']['message_ok'] = $m;
		}
	}
	return $flux;
}

/**
 * Intervient sur un squelette
 *
 * @pipeline recuperer_fond
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 *
 */
function reservations_multiples_recuperer_fond($flux) {
	$fond = $flux['args']['fond'];
	$contexte = $flux['data']['contexte'];

	if ($fond == 'formulaires/inc-reservation_connection') {
		$config = reservations_multiples_config();

		if ($config['multiple_personnes'] == 'on') {
			$auteurs_multiples = recuperer_fond('inclure/auteurs_multiples', $contexte, array(
				'ajax' => 'oui'
			));
			$flux['data']['texte'] .= $auteurs_multiples;
		}
	}
	if ($fond == 'formulaires/inc-reservation_evenements_champ' or $fond == 'formulaires/inc-reservation_evenements_declinaisons_prix') {
		$config = reservations_multiples_config();
		if ($config['multiple_inscriptions'] == 'on') {
			$flux['data']['texte'] .= recuperer_fond('inclure/nombre_multiples', $contexte);
			;
		}
	}

	return $flux;
}

/**
 * Insère des donnés dans le head public
 *
 * @pipeline insert_head
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 *
 */
function reservations_multiples_insert_head($flux) {
	$script = find_in_path('scripts/reservations_multiples.js');
	$css = find_in_path('css/reservations_multiples.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n" . "<script type='text/javascript' src='$script'> </script>";

	return $flux;
}

/**
 * Intervient après l'enregistrement
 *
 * @pipeline post_insertion
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 *
 */
function reservations_multiples_post_insertion($flux) {
	if ($flux['args']['table'] == 'spip_reservations' and _request('nombre_auteurs')) {
		$id_reservation = $flux['args']['id_objet'];
		// premier enregisté, on met l'id_reservation_source
		if (!_request('nr_auteur') > 0) {
			set_request('id_reservation_base', $id_reservation);
		}
		// Puis on recorrige l'id_reservation dans la session
		else {
			$id_reservation_source = _request('id_reservation_base');
			set_request('id_reservation_source', $id_reservation_source);
		}
	}

	return $flux;
}

/**
 * Ajouter les configurations dans celle de réservation événements.
 *
 * @pipeline reservation_evenement_objets_configuration
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 */
function reservations_multiples_reservation_evenement_objets_configuration($flux) {

	$objets = array(
		'reservations_multiples' => array(
			'label' => _T('reservations_multiples:reservations_multiples_titre'),
		),
	);

	$flux['data'] = array_merge($flux['data'], $objets);

	return $flux;
}