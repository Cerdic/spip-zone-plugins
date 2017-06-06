<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Arrondir la quantite d'une ligne de commande, en fonction de ce que permet l'objet
 * par defaut c'est en entier pour tout le monde, mais un plugin peut etendre cela
 *
 * @return Retourne l'identifiant du panier en cours
 */
function inc_commandes_arrondir_quantite($quantite, $objet='', $id_objet=0){

	return round($quantite,3);

}
