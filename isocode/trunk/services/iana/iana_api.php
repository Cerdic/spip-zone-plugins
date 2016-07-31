<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant les services IANA.
 *
 * @package SPIP\ISOCODE\SERVICES\IANA
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


$GLOBALS['isocode']['iana']['tables'] = array(
	'iana5646subtags' => array(
		'basic_fields' => array(
			'Type'            => 'type',
			'Subtag'          => 'subtag',
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
				'regexp'    => '%(\w+):\s+(.*)%i'
			)
		)
	)
);

// ----------------------------------------------------------------------------
// ----------------- API du service ISO - Actions principales -----------------
// ----------------------------------------------------------------------------

