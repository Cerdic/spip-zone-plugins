<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// On déclare le formulaire pour éditer un formulaire
$GLOBALS['formulaires']['editer_formulaire'] = array(
	array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'titre',
			'label' => '<:formidable:editer_titre:>',
			'obligatoire' => 'oui'
		)
	),
	array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'identifiant',
			'label' => '<:formidable:editer_identifiant:>',
			'explication' => '<:formidable:editer_identifiant_explication:>',
			'obligatoire' => 'oui'
		),
		'verifier' => array(
			'type' => 'regex',
			'options' => array(
				'modele' => '/^[\w]+$/'
			)
		)
	),
	array(
		'saisie' => 'radio',
		'options' => array(
			'nom' => 'multiple',
			'label' => '<:formidable:editer_multiple:>',
			'explication' => '<:formidable:editer_multiple_explication:>',
			'datas' => array(
				'oui' => '<:item_oui:>',
				'non' => '<:item_non:>'
			),
			'defaut' => 'non'
		)
	),
	array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'message_retour',
			'label' => '<:formidable:editer_message_ok:>',
			'explication' => '<:formidable:editer_message_ok_explication:>',
			'rows' => 5,
			'li_class' => 'editer_texte'
		)
	),
	array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'descriptif',
			'label' => '<:formidable:editer_descriptif:>',
			'explication' => '<:formidable:editer_descriptif_explication:>',
			'rows' => 5
		)
	)
);

?>
