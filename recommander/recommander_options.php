<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// brancher le plugin sur nospam
if (isset($GLOBALS['formulaires_no_spam']) and is_array($GLOBALS['formulaires_no_spam']) and !in_array('recommander', $GLOBALS['formulaires_no_spam'])) {
	$GLOBALS['formulaires_no_spam'][] = 'recommander';
}
