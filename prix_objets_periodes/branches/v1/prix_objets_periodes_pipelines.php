<?php
/**
 * Utilisations de pipelines par Prix objets par périodes
 *
 * @plugin     Prix objets par périodes
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Prix_objets_periodes\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclare les champs extras pour le formulaire prix.
 *
 * @pipeline prix_objets_extensions
 *
 * @param array $flux
 *          Données du pipeline
 * @return array
 */
function prix_objets_periodes_prix_objets_extensions($flux) {

	$flux['data']['periode'] = array (
		array(
			'saisie' => 'periodes',
			'options' => array(
				'nom' => 'id_prix_extension_periode',
				'label' => _T('po_periode:champ_id_prix_extension_po_periode'),
				'option_intro' => _T('po_periode:info_aucun_po_periode'),
				'defaut' => $flux['id_prix_extension_objet'],
				'class' => 'chosen',
				'multiple' => 'oui',
			)
		),
		array(
			'saisie' => 'ajouter_action',
			'options' => array(
				'nom' => 'ajouter_periode',
				'label_action' => _T('periode:icone_creer_periode'),
				'objet' => 'periode',
			)
		),
	);

	return $flux;
}
