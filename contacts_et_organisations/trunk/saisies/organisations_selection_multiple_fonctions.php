<?php
/**
 * Fonctions utiles au squelette organisations_selection_multiple
 *
 * @plugin	   Contacts et Organisations
 * @copyright  2016
 * @author	   Michel @ Vertige ASBL
 * @licence	   GNU/GPL
 * @package SPIP\Contacts\Saisies\Fonctions
 */

/**
 * Assigner une valeur à la clé donnée du tableau donné
 *
 * @param array $tableau : le tableau en question
 * @param mixed $cle : la clé
 * @param mixed $valeur : la valeur
 *
 * @return array : le tableau avec la bonne valeur associée à la bonne clé
 */
function filtre_table_cle_valeur_dist($tableau, $cle, $valeur) {

	$tableau[$cle] = $valeur;

	return $tableau;
}
