<?php
// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

define('_TRAITEMENT_TYPO_SANS_NUMERO', 'trim(PtoBR(propre(supprimer_numero(%s), $connect, $Pile[0])))');

?>
