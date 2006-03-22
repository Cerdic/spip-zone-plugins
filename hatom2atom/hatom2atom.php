<?php

// Syndication : ce plugin permet de lire les pages au format hAtom
//
// s'integre sur le point d'entree 'pre_syndication' et filtre la page
// avec XSLT @ http://rbach.priv.at/hAtom2Atom/hAtom2Atom-HEAD.xsl
//


function hAtom_to_Atom($rss) {

	if (preg_match(',<div\s[^>]*class=[^>]*\bhfeed\b,Uims', $rss)) {
		ecrire_fichier(_DIR_CACHE.'atom.xml', charset2unicode($rss));
		exec("tidy -asxhtml -numeric -o "
			._DIR_CACHE."atom-tidy.xml "
			._DIR_CACHE."atom.xml");
		$xml = new DOMDocument;
		$xml->load(_DIR_CACHE.'atom-tidy.xml');

		## source XSL transform 
		$xsl = new DOMDocument;
		$xsl->load(dirname(__FILE__).'/'.'hatom2atom.xsl');  
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl); // attach xsl transform
		$atom = $proc->transformToXml($xml);

		if ($atom)
			$rss = $atom;
	}

	return $rss;
}


?>