<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!function_exists('autoriser')) {
	include_spip('inc/autoriser');
}     // si on utilise le formulaire dans le public

function formulaires_editer_noisette_charger_dist($id_noisette, $redirect = '') {

	$valeurs = array(
		'editable' => autoriser('configurer', 'noizetier'),
		'id_noisette' => intval($id_noisette),
		'_champs'  => array(),
	);

	if ($valeurs['editable']) {
		$select = array('noisette', 'parametres', 'balise', 'css');
		$from = array('spip_noisettes');
		$where = array('id_noisette=' . $valeurs['id_noisette']);
		$noisette = sql_fetsel($select, $from, $where);
		if ($noisette) {
			// Acquisition des informations spécifiques de la noisette en base de données
			$valeurs['noisette'] = $noisette['noisette'];

			// Acquisition de la structure de configuration standard de la noisette définie dans son fichier YAML.
			// Cette configuration peut comporter des paramètres de saisie spécifiques dont les valeurs sont ensuite
			// stockées dans le champ 'parametres' de la table 'spip_noisettes'.
			// Cette structure de formulaire est générée automatiquement par le plugin Saisies.
			include_spip('noizetier_fonctions');
			$champs = noisette_informer($valeurs['noisette'], 'parametres');
			if ($champs) {
				$valeurs['_champs'] = $champs;

				// Insérer dans le contexte les valeurs des paramètres spécifiques stockées en BD.
				// On doit passer par saisies_charger_champs() au cas ou la definition de la noisette a change
				// et qu'il y a de nouveau champs a prendre en compte
				include_spip('inc/saisies');
				$parametres = unserialize($noisette['parametres']);
				if (is_array($parametres)) {
					$valeurs = array_merge($valeurs, saisies_charger_champs($champs), $parametres);
				}
			}

			// Insérer dans le contexte les valeurs des paramètres généraux stockées en BD.
			// Ces paramètres généraux sont inclus manuellement dans le formulaire.
			$valeurs['balise'] = $noisette['balise'];
			$valeurs['css'] = $noisette['css'];
			// Construction de la liste des valeurs possibles pour le choix de la balise
			include_spip('inc/config');
			$config_balise = lire_config('noizetier/balise_noisette')
				? _T('noizetier:option_noizetier_balise_oui')
				: _T('noizetier:option_noizetier_balise_non');
			$valeurs['_balise_options'] = array(
				'defaut' => _T('noizetier:option_noisette_balise_defaut', array('defaut' => $config_balise)),
				'on'     => _T('noizetier:option_noisette_balise_oui'),
				''       => _T('noizetier:option_noisette_balise_non')
			);
		} else {
			$valeurs['editable'] = false;
		}
	}

	return $valeurs;
}

function formulaires_editer_noisette_verifier_dist($id_noisette, $redirect = '') {

	// TODO : rajouter la vérification des css
	$noisette = _request('noisette');
	$champs = noisette_informer($noisette, 'parametres');

	return saisies_verifier($champs, false);
}

function formulaires_editer_noisette_traiter_dist($id_noisette, $redirect = '') {

	$retour = array();

	// TODO : a quoi sert l'autorisation sur les formulaires ???
	if (autoriser('configurer', 'noizetier')) {
		// Paramètres propres de la noisette
		$noisette = _request('noisette');
		$champs = noisette_informer($noisette, 'parametres');
		$parametres = array();
		foreach (saisies_lister_champs($champs, false) as $_champ) {
			$parametres[$_champ] = _request($_champ);
		}

		// Paramètres généraux d'inclusion de la noisette
		include_spip('inc:config');
		$balise = _request('balise');
		$css = _request('css');
		if (!$balise or (($balise == 'defaut') and !lire_config('noizetier/balise_noisette'))) {
			// on remet à zéro les css si la balise englobante n'est pas active
			$css = '';
		}

		// Mise à jour de la noisette en base de données
		$valeurs = array('parametres' => serialize($parametres), 'balise' => $balise, 'css' => $css);
		$where = array('id_noisette=' . intval($id_noisette));
		if (sql_updateq('spip_noisettes', $valeurs, $where)) {
			// On invalide le cache
			include_spip('inc/invalideur');
			suivre_invalideur("id='noisette/$id_noisette'");
			$retour['message_ok'] = _T('info_modification_enregistree');
			if ($redirect) {
				if (strncmp($redirect, 'javascript:', 11) == 0) {
					$retour['message_ok'] .= '<script type="text/javascript">/*<![CDATA[*/'.substr($redirect, 11).'/*]]>*/</script>';
					$retour['editable'] = true;
				} else {
					$retour['redirect'] = $redirect;
				}
			}
		} else {
			$retour['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
		}
	} else {
		$retour['message_erreur'] = _T('noizetier:probleme_droits');
	}

	return $retour;
}
