<?php
/**
 * Définit les autorisations du plugin Prix Objets
 *
 * @plugin     Prix Objets
 * @copyright  2012 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Prix_objets\Autorisations
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function prix_objets_autoriser(){}

// declarations d'autorisations

// Édition
// modifier
function autoriser_prix_modifier_dist($faire, $type, $id, $qui, $opt) {
    return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

?>
