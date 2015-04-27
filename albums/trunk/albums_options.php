<?php
/**
 * options du plugin Albums
 *
 * @plugin     Albums
 * @copyright  2015
 * @author     Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Options
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Paramètres par défaut des modèles
// Les suffixes `_VIGNETTES` et `_LISTE` pour restreindre certains à une variante

// [tous] Pas de pagination (nombre de documents affichés en même temps) : nombre
if (!defined('_ALBUMS_PAGINATION'))                    define('_ALBUMS_PAGINATION',20);
if (!defined('_ALBUMS_PAGINATION_VIGNETTES'))          define('_ALBUMS_PAGINATION_VIGNETTES','');
if (!defined('_ALBUMS_PAGINATION_LISTE'))              define('_ALBUMS_PAGINATION_LISTE','');

// [tous] Balise dans laquelle est placé le titre : balise HTML *sans les chevrons*, ex.: `span`, `h3`, `strong`...
if (!defined('_ALBUMS_BALISE_TITRE'))                  define('_ALBUMS_BALISE_TITRE','strong');
if (!defined('_ALBUMS_BALISE_TITRE_VIGNETTES'))        define('_ALBUMS_BALISE_TITRE_VIGNETTES','');
if (!defined('_ALBUMS_BALISE_TITRE_LISTE'))            define('_ALBUMS_BALISE_TITRE_LISTE','');

// [tous] Position de la légende (titre et descriptif) : `top` ou `bottom`
if (!defined('_ALBUMS_POSITION_LEGENDE'))              define('_ALBUMS_POSITION_LEGENDE','top');
if (!defined('_ALBUMS_POSITION_LEGENDE_VIGNETTES'))    define('_ALBUMS_POSITION_LEGENDE_VIGNETTES','bottom');
if (!defined('_ALBUMS_POSITION_LEGENDE_LISTE'))        define('_ALBUMS_POSITION_LEGENDE_LISTE','');

// [vignettes] Dimension maximale des vignettes : nombre
if (!defined('_ALBUMS_TAILLE_PREVIEW'))                define('_ALBUMS_TAILLE_PREVIEW',150);

// [vignettes] Recadrer ou non les vignettes
if (!defined('_ALBUMS_RECADRER'))                      define('_ALBUMS_RECADRER',false);

// [vignettes] Titre long ou court
if (!defined('_ALBUMS_TITRE_COURT'))                   define('_ALBUMS_TITRE_COURT',false);

// [liste] Informations affichées pour chaque fichier, séparées par un tiret `-` : 'extension-taille-dimensions'
if (!defined('_ALBUMS_LISTE_METAS'))                   define('_ALBUMS_LISTE_METAS','');

?>
