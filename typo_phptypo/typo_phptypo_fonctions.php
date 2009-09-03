<?php

/*
 * Integration de PHP-Typography dans SPIP
 *
 * (c) Fil 2009
 * sous licence GNU/GPL
 *
 * PHP-Typographie est (c) Kingdesk.com, sous licence GNU/GPL
 *
 */

function typo_phptypo($t) {

	include_spip('lib/php-typography/php-typography');
	if (!class_exists("phpTypography")) {
		spip_log('erreur phpTypography non trouve');
		return $t;
	}
	$typo = new phpTypography();
	include_spip('inc/lang');
	$l = lang_typo();
	$lh = @file_exists(_DIR_LIB.'php-typography/lang/'.$GLOBALS['spip_lang'].'.php')
		? $GLOBALS['spip_lang']
		: @file_exists(_DIR_LIB.'php-typography/lang/'.$l.'.php')
			? $l
			: 'en-US';
	$typo->set_smart_quotes_language($l);
	$typo->set_hyphenation_language($lh);


	$t = preg_replace(',@@.*@@,UimsS', '<kbd>\0</kbd>', $t);
	$t = $typo->process($t);
	return preg_replace(',<kbd>(@@.*@@)</kbd>,UimsS', '\1', $t);

}

?>
