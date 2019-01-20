<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!function_exists('autoriser')) {
	include_spip('inc/autoriser');
}     // si on utilise le formulaire dans le public

function formulaires_editer_noisette_charger_dist($id_noisette, $redirect = '') {

	$valeurs = array('editable' => false);

	if (autoriser( 'editernoisette', 'noizetier', $id_noisette)) {
		// Récupération des informations sur la noisette en cours d'édition et sur le type de noisette
		$valeurs['id_noisette'] = intval($id_noisette);
		$select = array(
			't1.type_noisette as type_noisette',
			't1.est_conteneur as est_conteneur',
			't1.parametres as parametres',
			't1.encapsulation as encapsulation',
			't1.css as css',
			't2.parametres as champs');
		$from = array('spip_noisettes as t1', 'spip_types_noisettes as t2');
		$where = array(
			't1.plugin=' . sql_quote('noizetier'),
			't1.id_noisette=' . $valeurs['id_noisette'],
			't1.type_noisette=t2.type_noisette');
		$noisette = sql_fetsel($select, $from, $where);
		if ($noisette) {
			// Type de la noisette
			$valeurs['type_noisette'] = $noisette['type_noisette'];
			$valeurs['est_conteneur'] = $noisette['est_conteneur'];

			// Configuration standard de la noisette définie dans son fichier YAML.
			// Cette configuration peut comporter des paramètres de saisie spécifiques dont les valeurs sont ensuite
			// stockées dans le champ 'parametres' de la table 'spip_noisettes'.
			// Cette structure de formulaire est générée automatiquement par le plugin Saisies.
			$valeurs['_champs'] = unserialize($noisette['champs']);

			// Insérer dans le contexte les valeurs des paramètres spécifiques stockées en BD.
			// On doit passer par saisies_charger_champs() au cas ou la définition de la noisette a changé
			// et qu'il y a de nouveaux champs à prendre en compte
			include_spip('inc/saisies');
			$parametres = unserialize($noisette['parametres']);
			$valeurs = array_merge($valeurs, saisies_charger_champs($valeurs['_champs']), $parametres);

			// Insérer dans le contexte les valeurs des paramètres généraux stockées en BD.
			// Ces paramètres généraux sont inclus manuellement dans le formulaire.
			$valeurs['encapsulation'] = $noisette['encapsulation'];
			$valeurs['css'] = $noisette['css'];
			// Construction de la liste des valeurs possibles pour le choix de la encapsulation
			include_spip('ncore/noizetier');
			$config_encapsulation = noizetier_noisette_initialiser_encapsulation('noizetier')
				? _T('noizetier:option_noizetier_encapsulation_oui')
				: _T('noizetier:option_noizetier_encapsulation_non');
			$valeurs['_encapsulation_options'] = array(
				'defaut' => _T('noizetier:option_noisette_encapsulation_defaut', array('defaut' => lcfirst($config_encapsulation))),
				'oui'    => _T('noizetier:option_noisette_encapsulation_oui'),
				'non'    => _T('noizetier:option_noisette_encapsulation_non')
			);
			$valeurs['editable'] = true;
		}
	}

	return $valeurs;
}


function formulaires_editer_noisette_verifier_dist($id_noisette, $redirect = '') {

	$erreurs = array();

	// Vérifier les champs correspondant aux paramètres spécifiques de ce type de noisette
	include_spip('inc/ncore_type_noisette');
	$champs = type_noisette_lire(
		'noizetier',
		 _request('type_noisette'),
		 'parametres',
		 false);
	if ($champs) {
		include_spip('inc/saisies');
		$erreurs = saisies_verifier($champs, false);
	}

	// On vérifie la syntaxe des sélecteurs de classe
	if (!preg_match('#([\w-]+)(\s+([\w-]+))*#', _request('css'))) {
		$erreurs['css'] = _T('noizetier:erreur_saisie_css_invalide');
	}

	return $erreurs;
}


function formulaires_editer_noisette_traiter_dist($id_noisette, $redirect = '') {

	$retour = array();

	if (autoriser( 'editernoisette', 'noizetier', $id_noisette)) {
		// On constitue le tableau des valeurs des paramètres spécifiques de la noisette
		include_spip('inc/ncore_type_noisette');
		$champs = type_noisette_lire(
			'noizetier',
			 _request('type_noisette'),
			 'parametres',
			 false);
		$parametres = array();
		if ($champs) {
			include_spip('inc/saisies_lister');
			foreach (saisies_lister_champs($champs, false) as $_champ) {
				$parametres[$_champ] = _request($_champ);
			}
		}

		// Paramètres généraux d'inclusion de la noisette : on distingue les noisettes conteneur et les autres.
		// Pour les noisettes conteneur, l'encapsulation et les css ne sont pas éditables.
		$valeurs = array('parametres' => serialize($parametres));
		if (_request('est_conteneur') != 'oui') {
			include_spip('inc/config');
			$encapsulation = _request('encapsulation');
			$css = _request('css');
			if (($encapsulation == 'non') or (($encapsulation == 'defaut') and !lire_config('noizetier/encapsulation_noisette'))) {
				// on remet à zéro les css si la capsule englobante n'est pas active
				$css = '';
			}
			$valeurs = array_merge($valeurs, array('encapsulation' => $encapsulation, 'css' => $css));
		}

		// Fermeture de la modale
		$autoclose = "<script type='text/javascript'>if (window.jQuery) jQuery.modalboxclose();</script>";

		// Mise à jour de la noisette en base de données
		include_spip('inc/ncore_noisette');
		if (noisette_parametrer('noizetier', intval($id_noisette), $valeurs)) {
			// On invalide le cache
			include_spip('inc/invalideur');
			suivre_invalideur("id='noisette/$id_noisette'");
			$retour['message_ok'] = _T('info_modification_enregistree') . $autoclose;
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
