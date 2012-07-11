<?php

/**
 * Ce fichier Gère la librairie QueryPath
 *
 * Ce pourrait faire l'objet d'un plugin spécifique.
 * À voir.
 * 
 * http://http://querypath.org/
**/

/**
 * Plugin Groupes arborescents de mots clés
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Adresse de la librairie QueryPath
 *
 * Permet de chercher et modifier du HTML
 * http://http://querypath.org/
**/
define('SOURCE_QUERYPATH', _DIR_PLUGIN_GMA . 'lib/QueryPath-2.1.2.phar');

// charger l'archive
include_once(SOURCE_QUERYPATH);


/**
 * Retourne un objet QueryPath en utilisant la classe adaptée pour SPIP
 *
 * @param string|null $document
 * 		URL ou texte du document XML/HTML
 * @param string|null $string
 * 		Position sur laquelle se placer, exemple 'body'
 * @param array $options
 * 		Option de la classe QueryPath
 * @return SpipQueryPath
 * 		Objet QueryPath adapté pour SPIP
 * 
**/
function spip_query_path($document = NULL, $string = NULL, $options = array()) {

	// convertir automatiquement si le document est une chaine
	// en tenant compte du charset du site et des CDATA
	if (is_string($document)) {
		$document = charset2unicode($document);
		$document = gma_echappe_CDATA($document);
		$options += array('replace_entities' => true);
	}

	// lancer
	return qp($document, $string, $options);
}


/**
 * Échappe les cdata présents dans un document...
 *
 * domDocument les ajoute automatiquement
 * sans possibilité de déconnecter la fonctionnalité.
 * Du coup, lorsque les CDATA sont déjà présents, ce qui est le cas
 * en général chez SPIP, ils se retrouvent doublés.
 *
 * Ici, on les enlève donc du document Texte d'origine.
 *
 * @param string $html
 * 		Contenu du document html
 * @return string
 * 		Contenu sans les CDATA
**/
function gma_echappe_CDATA($html) {
	static $cdata_on  = '<!\[CDATA\[';
	static $cdata_off = '\]\]>';

	if (false !== strpos($html, '<![')) {
		// echapper \\<![CDATA[    \\]]>
		$html = preg_replace('/'
			. '\/\/' . $cdata_on  #ouverture
			. '(.*?)'             #contenu
			. '\/\/' . $cdata_off #fermuture
			. '/is', '$1', $html);
		// echapper /* <![CDATA[ */    /* ]]> */
		$html = preg_replace('/'
			. '\/\*\s*' . $cdata_on . '\s*\*\/'  #ouverture
			. '(.*?)'                            #contenu
			. '\/\*\s*' . $cdata_off . '\s*\*\/' #fermuture
			. '/is', '$1', $html);
		// echapper <![CDATA[    ]]>
		$html = preg_replace('/'
			. $cdata_on  #ouverture
			. '(.*?)'    #contenu
			. $cdata_off #fermuture
			. '/is', '$1', $html);
	}
	return $html;
}
