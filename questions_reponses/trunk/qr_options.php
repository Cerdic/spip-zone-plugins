<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Ajout des raccourcis dans la liste des wheels
$GLOBALS['spip_wheels']['raccourcis'][] = 'qr.yaml';

// Définition surchargeable de l'indicateur de tag.
// -- les valeurs possibles sont # (défaut) et @
$GLOBALS['qr_indicateur_tag'] = '#';

?>
