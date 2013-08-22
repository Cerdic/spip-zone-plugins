<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

// Langue par defaut si non supportee par le site serveur
if (!defined('_SPIPERIPSUM_LANGUE_DEFAUT'))
	define('_SPIPERIPSUM_LANGUE_DEFAUT','en');

// Jour par defaut
if (!defined('_SPIPERIPSUM_JOUR_DEFAUT'))
	define('_SPIPERIPSUM_JOUR_DEFAUT','aujourdhui');

// Valeurs de l'argument lecture dans le modele spiperipum
if (!defined('_SPIPERIPSUM_LECTURE_EVANGILE'))
	define('_SPIPERIPSUM_LECTURE_EVANGILE','evangile');
if (!defined('_SPIPERIPSUM_LECTURE_PREMIERE'))
	define('_SPIPERIPSUM_LECTURE_PREMIERE','premiere');
if (!defined('_SPIPERIPSUM_LECTURE_SECONDE'))
	define('_SPIPERIPSUM_LECTURE_SECONDE','seconde');
if (!defined('_SPIPERIPSUM_LECTURE_PSAUME'))
	define('_SPIPERIPSUM_LECTURE_PSAUME','psaume');
if (!defined('_SPIPERIPSUM_LECTURE_COMMENTAIRE'))
	define('_SPIPERIPSUM_LECTURE_COMMENTAIRE','commentaire');
if (!defined('_SPIPERIPSUM_LECTURE_SAINT'))
	define('_SPIPERIPSUM_LECTURE_SAINT','saint');
if (!defined('_SPIPERIPSUM_LECTURE_FETE'))
	define('_SPIPERIPSUM_LECTURE_FETE','fete');
if (!defined('_SPIPERIPSUM_LECTURE_DATE'))
	define('_SPIPERIPSUM_LECTURE_DATE_TITRE','date_titre');
if (!defined('_SPIPERIPSUM_LECTURE_DATE_ISO'))
	define('_SPIPERIPSUM_LECTURE_DATE_ISO','date_iso');
if (!defined('_SPIPERIPSUM_LECTURE_DATE_LITURGIQUE'))
	define('_SPIPERIPSUM_LECTURE_DATE_LITURGIQUE','date_liturgique');
// -- Lecture par defaut
if (!defined('_SPIPERIPSUM_LECTURE_DEFAUT'))
	define('_SPIPERIPSUM_LECTURE_DEFAUT','evangile');

// Valeurs de l'argument mode d'appel du modele (depuis article ou page zpip)
if (!defined('_SPIPERIPSUM_MODE_ARTICLE'))
	define('_SPIPERIPSUM_MODE_ARTICLE','article');
if (!defined('_SPIPERIPSUM_MODE_PAGE'))
	define('_SPIPERIPSUM_MODE_PAGE','page');
// -- Mode par defaut
if (!defined('_SPIPERIPSUM_MODE_DEFAUT'))
	define('_SPIPERIPSUM_MODE_DEFAUT','article');

// Info par defaut
if (!defined('_SPIPERIPSUM_INFO_DEFAUT'))
	define('_SPIPERIPSUM_INFO_DEFAUT','titre');

// Séparateur entre la date iso et la date liturgique quand on demande une lecture=date
if (!defined('_SPIPERIPSUM_SEPARATEUR_DATE'))
	define('_SPIPERIPSUM_SEPARATEUR_DATE', ',&nbsp;');

?>
