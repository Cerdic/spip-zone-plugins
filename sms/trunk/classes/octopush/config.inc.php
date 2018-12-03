<?php

/**
 * Librairie d'envoi de SMS via POST HTTP
 *
 * Auteur Yoni Guimberteau yoni@octopush.com
 *
 * copyright (c) 2014 Yoni Guimberteau
 * licence : utilisation, modification, commercialisation.
 * L'auteur ainsi se decharge de toute responsabilite
 * concernant une quelconque utilisation de ce code, livre sans aucune garantie.
 * Il n'est distribue qu'a titre d'exemple de fonctionnement du module POST HTTP de OCTOPUSH,
 * Vous pourrez toutefois telecharger une version actualisee sur www.octopush.com
 */

define('DOMAIN', 'http://www.octopush-dm.com');
define('PORT', '80');
define('PATH', '');
define('PATH_BIS', '');
$path = PATH;

define('PATH_SMS', $path . '/api/sms');
define('PATH_BALANCE', $path . '/api/balance');

// Options ouvertes sur demande. Demandez la documention annexe pour utiliser ces webservices.
define('PATH_SUB_ACCOUNT', $path . '/api_sub/add_sub_account');
define('PATH_CREDIT_SUB_ACCOUNT_TOKEN', $path . '/api_sub/credit_sub_account_get_session');
define('PATH_CREDIT_SUB_ACCOUNT', $path . '/api_sub/credit_sub_account');
define('PATH_OSTP', $path . '/api/open_single_temp_session');
define('PATH_EDIT_OPTIONS', $path . '/api/edit_options');
define('PATH_GET_USER_INFO', $path . '/api_sub/get_user_info');

define('_CUT_', 7);

define('SMS_STANDARD', 'XXX');
define('SMS_WORLD', 'WWW');
define('SMS_PREMIUM', 'FR');

define('INSTANTANE', 1);
define('DIFFERE', 2);

define('SIMULATION', 'simu');
define('REEL', 'real');
