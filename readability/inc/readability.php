<?php

// nettoyer une page se trouvant derriere une URL
function readability($url) {
	// utiliser memoization si dispo
	include_spip('inc/memoization');
	if (function_exists('cache_me')
	AND !is_null($c=cache_me()))
		return $c;

	if ($html = recuperer_page($url)) {
		$html = abs_url($html, $url);
		return readability_html($html);
	}
}

// nettoyer un contenu html
// renvoit un HTML contenant :
//      <h1> le titre
//      <div.readability> le contenu
//
function readability_html($html) {
	require_once find_in_path('lib/readability/Readability.php');


	// Note: PHP Readability expects UTF-8 encoded content.
	// If your content is not UTF-8 encoded, convert it 
	// first before passing it to PHP Readability. 
	// Both iconv() and mb_convert_encoding() can do this.

	// If we've got Tidy, let's clean up input.
	// This step is highly recommended - PHP's default HTML parser
	// often does a terrible job and results in strange output.
	if (function_exists('tidy_parse_string')) {
		$tidy = tidy_parse_string($html, array('indent'=>true), 'UTF8');
		$tidy->cleanRepair();
		$html = $tidy->value;
	}

	// give it to Readability
	$readability = new Readability($html, $url);
	// print debug output? 
	// useful to compare against Arc90's original JS version - 
	// simply click the bookmarklet with FireBug's console window open
	$readability->debug = false;
	// convert links to footnotes?
	$readability->convertLinksToFootnotes = false;
	// process it
	$result = $readability->init();
	// does it look like we found what we wanted?
	if ($result) {
		$title = $readability->getTitle()->textContent;
		$content = $readability->getContent()->innerHTML;

		// if we've got Tidy, let's clean it up for output
		if (function_exists('tidy_parse_string')) {
			$tidy = tidy_parse_string($content, array('indent'=>true, 'show-body-only' => true), 'UTF8');
			$tidy->cleanRepair();
			$content = $tidy->value;
		}

		return (strlen($title) ? "<h1>$title</h1>\n\n" : '')
			. $content;
	}
}