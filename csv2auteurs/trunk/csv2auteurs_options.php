<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

// losqu'on utilise la moulinette d'import des comptes, débrider la longueur mini du passe SPIP
if (_request('exec') == 'csv2auteurs' 
	AND $GLOBALS['auteur_session']['webmestre'] == 'oui'
	AND _request('formulaire_action') == 'csv2auteurs_importation')
	define('_PASS_LONGUEUR_MINI', '1');