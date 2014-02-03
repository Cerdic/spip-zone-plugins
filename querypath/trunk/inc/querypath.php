<?php

/**
 * Ce fichier Gère le chargement de la librairie QueryPath
 *
 * Une fois ce fichier chargé, vous avez accès à la fonction
 * spip_query_path(), mais également si vous préférez aux fonctions
 * de la librairie directement : qp(), htmlqp() ou encore directement
 * la classe QueryPath.
 * 
 * @link http://querypath.org/
 * @version 2.1.2
**/

/**
 * Plugin Query Path
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Charger la librairie QueryPath
 *
 * Permet de chercher et modifier du HTML
 * http://http://querypath.org/
**/
include_spip('lib/querypath-3.0.0/src/qp');


/**
 * Retourne un objet QueryPath
 *
 * QueryPath est lancé, mais avec les modifications suivantes :
 * - l'option replace_entities est passée par défaut à TRUE
 * - lorsque $document est un texte, il est transformé en unicode
 *   et les CDATA sont enlevés. Cela est plus pratique pour manipuler du HTML.
 *
 * @api
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
		// domDocument a du mal avec l'UTF, c'est pourquoi
		// QueryPath a des options pour transcoder la source
		// en utilisant la librairie mb.
		// Comme SPIP possède sa propre fonction, autant l'utiliser.
		$document = charset2unicode($document);
		// le chargement d'un HTML ayant déjà des CDATA est problématique
		// car ils seront automatiquement doublés (cf. https://bugs.php.net/bug.php?id=54429)
		// On les échappe ici automatiquement.
		// Il ne faudrait peut être pas le faire si <?xml est là...
		$document = querypath_echappe_CDATA($document);
	}
	// indiquer que les ajouts tel que ->after()
	// doivent transformer les entités HTML présentes,
	// sinon le XML est rarement correct et domDocument râle.
	$options += array('replace_entities' => true);

	// lancer
	return qp($document, $string, $options);
}


/**
 * Enlève les cdata présents dans un texte...
 *
 * domDocument les ajoute automatiquement
 * sans possibilité de déconnecter la fonctionnalité.
 * Du coup, lorsque les CDATA sont déjà présents, ce qui est le cas
 * en général chez SPIP, ils se retrouvent doublés.
 * Cf. https://bugs.php.net/bug.php?id=54429
 *
 * @param string $html
 * 		Contenu du document html
 * @return string
 * 		Contenu sans les CDATA
**/
function querypath_echappe_CDATA($html) {
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
