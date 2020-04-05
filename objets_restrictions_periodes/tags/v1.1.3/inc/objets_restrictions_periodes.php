<?php
/**
 * Donctions du plugin Objets restrictions périodes.
 *
 * @plugin     Objets restrictions périodes
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Objets_restrictions_periodes\Verifier
 */


/**
 * Teste si la date est conforme aux restrictions imposés par la période.
 *
 * @param string $champ
 *   Le champ date à tester.
 * @param string $valeur
 *   La valeur à vérifier.
 * @param array $options
 *   valeurs_restriction -> les valeurs de la restriction
 * @return string
 *   Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function periodes_verifier_date($champ, $valeur, $options) {
	include_spip('filtres/dates_outils');
	include_spip('public/assembler');
	// Si une période a été définie inc charge ses données
	$valeurs_restriction = $options['valeurs_restriction'];
	$id_periode = isset($valeurs_restriction['periode']) ?
		$valeurs_restriction['periode'] :
		'';
	$contexte = calculer_contexte();
	$erreur_periode = '';
	$ok = FALSE;
	if ($id_periode) {
		$verifier_periode = charger_fonction('periode_verifier', 'inc');

		// Si la période ne correspond pas, on s'arrête là.
		if (!$verifier_periode($id_periode, $contexte)) {
			return $ok;
		}
		else {
			$periode = sql_fetsel('*', 'spip_periodes', "id_periode=$id_periode");
			$titre_periode = extraire_multi($periode['titre']);
			$erreur_periode = _T('periode:champ_periode_label') . " $titre_periode <br>\n";
		}
	}

	$type = $valeurs_restriction['type'];

	switch ($type) {
		case 'duree':

			$difference = dates_difference($contexte['date_debut'], $contexte['date_fin'], 'jour');
			$duree = $valeurs_restriction['duree'];

			if ($difference < $duree) {
				if ($id_objets_location = _request('id_objets_location') > 0) {
					$entite_duree = sql_getfetsel(
						'entite_duree',
						'spip_objets_locations',
						'id_objets_location=' . $id_objets_location);
				}
				else {
						$entite_duree = _request('entite_duree');
				}

				$erreur = $erreur_periode . _T('objets_restrictions_periodes:erreur_duree', [
					'duree' => $duree,
					'entite_duree' => $entite_duree,
				]);

				return $erreur;
			}
			break;

		case 'jours':
			$jour_restriction = $valeurs_restriction['jour_debut'];
			if ($champ == 'date_fin') {
				$jour_restriction = $valeurs_restriction['jour_fin'];
			}

			$jour_contexte = date('N', strtotime($contexte[$champ]));

			if (!empty($jour_restriction) AND ($jour_restriction != $jour_contexte)) {
				if ($jour_restriction < 7) {
					$jour_restriction = $jour_restriction + 1;
				}
				else {
					$jour_restriction = 1 ;
				}

				$jour = _T('spip:date_jour_' . $jour_restriction);
				$erreur = $erreur_periode . _T(
					'objets_restrictions_periodes:erreur_jours',
					[
						'date' => _T('dates_outils:info_' . $champ),
						'jour' => $jour,
					]);

				return $erreur;
			}
			break;

	}

	return $ok;
}