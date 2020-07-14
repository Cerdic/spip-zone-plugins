<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service IANA.
 *
 * @package SPIP\ISOCODE\SERVICES\IANA
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


$GLOBALS['isocode']['iana'] = array(
	'iana5646subtags' => array(
		'groupe'       => 'langue',
		'basic_fields' => array(
			'Type'            => 'type',
			'Subtag'          => 'subtag',
			'Tag'             => 'subtag',
			'Description'     => 'description',
			'Added' 	      => 'date_ref',
			'Suppress-Script' => 'no_script',
			'Scope'           => 'scope',
			'Macrolanguage'   => 'macro_language',
			'Deprecated'      => 'deprecated',
			'Preferred-Value' => 'preferred_tag',
			'Prefix'          => 'prefix',
			'Comments'        => 'comments',
		),
		'populating'   => 'page_text',
		'url'          => 'http://www.iana.org/assignments/language-subtag-registry/language-subtag-registry',
		'parsing'      => array(
			'element'       => array(
				'method'    => 'explode',
				'delimiter' => '%%'
			),
			'field'        => array(
				'method'    => 'regexp',
				'regexp'    => '%^([a-z]+-*[a-z]*):\s+(.*)%im'
			)
		)
	)
);

// ----------------------------------------------------------------------------
// ----------------- API du service ISO - Actions principales -----------------
// ----------------------------------------------------------------------------

