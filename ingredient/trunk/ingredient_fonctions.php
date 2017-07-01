<?php
/**
 * Fonctions utiles au plugin ingrédients
 *
 * @plugin     ingrédients
 * @copyright  2015
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Ingredient\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Un filtre pour ajouter automatiquement l'uniter de l'ingredient à la balise #QUANTITE
 *
 * @param mixed $quantite
 * @param mixed $Pile
 * @access public
 * @return mixed
 */
function ingredient_ajoute_unite($quantite, $Pile) {
    $unite = sql_getfetsel('unite', 'spip_ingredients', 'id_ingredient='.intval($Pile=['id_ingredient']));
    return $quantite.$unite;
}