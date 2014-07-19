<?php
/***************************************************************************\
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
    return;

/**
 * Ajout d'un lien vers la page de membre sur la page d'auteur
**/
function requeteursql_affiche_gauche($flux) {
    if ($flux['args']['exec']=='sql_requete') {
        //if (autoriser('voir_membres', 'amengees', $id_auteur)) {
            $flux['data'] .= recuperer_fond('prive/boites/affiche_gauche_sql_requete', $flux['args']);
        //}
    }
    return $flux;
}

?>
