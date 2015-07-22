<?php

/**
 * Déclaration systématiquement chargées.
 **/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
// ********
// Permettre de surcharger la taille pour le petit écran et le grand écran
// ********
if (!defined('_PETIT_ECRAN')) {
    define('_PETIT_ECRAN', '960px');
}
if (!defined('_GRAND_ECRAN')) {
    define('_GRAND_ECRAN', '1280px');
}
// ********

