<?php
/**
 * Utilisations de pipelines par Déclinaisons Prix
 *
 * @plugin     Déclinaisons Prix
 * @copyright  2012 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Promotions_commandes\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Declare l'object pour le Plugin shop https://github.com/abelass/shop.
 *
 * @pipeline shop_objets
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array
 */
function declinaisons_shop_objets($flux) {
	$flux['data']['declinaisons'] = array(
		'action' => 'declinaisons',
		'nom_action' => _T('declinaison:titre_declinaisons'),
		'icone' => 'declinaisons-16.png'
	);

	return $flux;
}

/**
 * Déclare les champs extras pour le formulaire prix.
 *
 * @pipeline prix_objets_extensions
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array
 */
function declinaisons_prix_objets_extensions($flux) {

	$flux['data'] = array (
		array(
			'saisie' => 'declinaisons',
			'options' => array(
				'nom' => 'id_prix_extension_declinaison',
				'label' => _T('declinaison:choisir_declinaison'),
				'option_intro' => _T('declinaison:info_aucun_declinaison'),
				'defaut' => $flux['id_prix_extension_objet'],
			)
		),
		array(
			'saisie' => 'ajouter_action',
			'options' => array(
				'nom' => 'ajouter_declinaison',
				'label_action' => _T('declinaison:icone_creer_declinaison'),
				'action' => 'declinaison_edit',
			)
		),
	);

	return $flux;
}
