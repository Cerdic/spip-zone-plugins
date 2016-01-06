<?php

/**
 * Options du plugin Unsplashau chargement.
 *
 * @plugin     Unsplash
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

if (!defined('_UNSPLASH_JSON')) {
    define('_UNSPLASH_JSON', 'https://unsplash.it/list');
}

define('_UNSPLASH_URL', 'https://unsplash.it/');

define('_UNSPLASH_PAGINATION', 10);
define('_UNSPLASH_THUMB_WIDTH', 257); /* 240 -> 16 */
define('_UNSPLASH_THUMB_HEIGHT', 170); /* 200 -> 9 */
