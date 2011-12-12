<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2009, distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


// charger la classe CFG d'origine pour l'etendre
include_spip('inc/cfg_formulaire_dist');

// etendre la classe CFG sans rien changer...
class cfg_formulaire extends cfg_formulaire_dist {}

?>
