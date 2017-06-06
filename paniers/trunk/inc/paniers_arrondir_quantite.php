<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Arrondir la quantite d'une ligne de panier, en fonction de ce que permet l'objet
 * par defaut c'est en entier pour tout le monde, mais un plugin peut etendre cela
 * et permettre d'utiliser des quantites decimales pour certains type d'objet
 * ou certains objets precis qui auraient une option 'fractionnable' cochee
 *
 * @return Retourne l'identifiant du panier en cours
 */
function inc_paniers_arrondir_quantite($quantite, $objet='', $id_objet=0){

	return intval($quantite);

}
