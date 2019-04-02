<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2017                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_safehtml($t, $allowed = null) {
	static $purifier;

	include_spip('inc/memoization');
	if (function_exists('cache_get')
	AND $a = cache_get($cle = 'safehtml:'.md5($t)))
		return $a;

	include_spip('lib/HTMLPurifier.standalone');
	
	
	// http://htmlpurifier.org/live/configdoc/plain.html
	$config = HTMLPurifier_Config::createDefault();
	$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
	$config->set('CSS.AllowTricky', true);
	$config->set('HTML.Allowed', $allowed);
	//https://stackoverflow.com/questions/11747918/htmlpurifier-allow-class-attribute
	//$config->set('HTML.AllowedAttributes', 'img.src,*.class');
	$config->set('Attr.AllowedFrameTargets', array('_blank'=>true));
	
	//$config->set('HTML.TargetBlank', true); 
	//$config->set('HTML.TargetNoopener', true);
	$config->set('Cache.SerializerPath', preg_replace(',/$,', '', realpath(_DIR_TMP.'cache/')));
	
	  // Set some HTML5 properties
	//$config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
	//$config->set('HTML.DefinitionRev', 1);
  
	$def = $config->getHTMLDefinition(true);
	 
	// HTML5
	// https://github.com/lukusw/php-htmlpurfier-html5/blob/master/htmlpurifier_html5.php
	// http://developers.whatwg.org/grouping-content.html
	// http://developers.whatwg.org/sections.html
    $def->addElement('section', 'Block', 'Flow', 'Common');
    $def->addElement('nav',     'Block', 'Flow', 'Common');
    $def->addElement('article', 'Block', 'Flow', 'Common');
    $def->addElement('aside',   'Block', 'Flow', 'Common');
    $def->addElement('header',  'Block', 'Flow', 'Common');
    $def->addElement('footer',  'Block', 'Flow', 'Common');
	
	$def->addElement('picture', 'Block', 'Flow', 'Common');
	
    $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
    $def->addElement('figcaption', 'Inline', 'Flow', 'Common');
	/*
	 * element_name,
	 * element_type,
	 * element_childs_attributes,
	 * attributes
	 **/
	// http://developers.whatwg.org/the-video-element.html#the-video-element
    $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
      'src' => 'URI',
      'type' => 'Text',
      'width' => 'Length',
      'height' => 'Length',
      'poster' => 'URI',
      'preload' => 'Enum#auto,metadata,none',
      'controls' => 'Bool',
    ));
	
	
	
    $def->addElement('source', 'Block', 'Flow', 'Common', array(
      'src' => 'URI',
	  'data-srcset' => 'URI',
	  'srcset' => 'URI',
	  'media' => 'Text',
      'type' => 'Text',
    ));
	
	// http://developers.whatwg.org/text-level-semantics.html
    $def->addElement('s',    'Inline', 'Inline', 'Common');
	//$def->addElement('b',    'Inline', 'Inline', 'Common');
    $def->addElement('var',  'Inline', 'Inline', 'Common');
    $def->addElement('sub',  'Inline', 'Inline', 'Common');
    $def->addElement('sup',  'Inline', 'Inline', 'Common');
    $def->addElement('mark', 'Inline', 'Inline', 'Common');
    $def->addElement('wbr',  'Inline', 'Empty', 'Core');
	
	
	$def->addAttribute('img', 'data-src', 'URI');
	$def->addAttribute('img', 'data-srcset', 'URI');
	$def->addAttribute('img', 'data-sizes', 'CDATA');
	
	$def->addAttribute('a', 'class', 'Text');
	$def->addAttribute('a', 'href', 'URI');
	//var_dump($def);
	if (!isset($purifier))
		$purifier = new HTMLPurifier($config);
	
	// HTML Purifier prefere l'utf-8
	if ($GLOBALS['meta']['charset'] == 'utf-8')
		$t = $purifier->purify($t);
	else
		$t = unicode_to_charset($purifier->purify(charset2unicode($t)));

	if (function_exists('cache_set'))
		cache_set($cle, $t);

	return $t;

}