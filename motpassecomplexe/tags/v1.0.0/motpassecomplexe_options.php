<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// les constantes definissant les contraintes sur le mot de passe
// define('_PASS_LONGUEUR_MINI', '6');                                          // longueur minimale - defaut: 6
if (!defined('_MOTCOMPLEXE_MINUSCULE')) define('_MOTCOMPLEXE_MINUSCULE', 1);    // nb de minuscules  - defaut: 1
if (!defined('_MOTCOMPLEXE_MAJUSCULE')) define('_MOTCOMPLEXE_MAJUSCULE', 1);    // nb de majuscules  - defaut: 1
if (!defined('_MOTCOMPLEXE_CHIFFRE')) define('_MOTCOMPLEXE_CHIFFRE', 1);        // nb de chiffres  - defaut: 1
if (!defined('_MOTCOMPLEXE_SPECIAL')) define('_MOTCOMPLEXE_SPECIAL', 1);        // nb de caractères spéciaux  - defaut: 1 
