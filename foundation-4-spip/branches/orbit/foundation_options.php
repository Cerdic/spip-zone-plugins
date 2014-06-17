<?php
/**
 * Options du plugin foundation-4-spipau chargement
 *
 * @plugin     foundation-4-spip
 * @copyright  2013
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Foundation\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
*   Plugin "En travaux"
*   
*   Autorise les administrateurs et les rédacteurs à voir le site lorsqu'il est en maintenance.
*/
function autoriser_travaux($faire,$quoi,$id,$qui,$opts){
    if ($qui['statut']=='0minirezo' or $qui['statut']=='1comite')
        return true;
    return false;
}
?>