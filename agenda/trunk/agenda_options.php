<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_AGENDA_AUTORISER_ORPHELINS')) define('_AGENDA_AUTORISER_ORPHELINS', FALSE);

// brancher le plugin sur nospam
$GLOBALS['formulaires_no_spam'][] = 'participer_evenement';
