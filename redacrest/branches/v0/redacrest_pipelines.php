<?php
/**
* Class and Function List:
* Function list:
* - redacrest_affiche_gauche()
* Classes list:
*/

/**
 * Plugin Rédacteurs restreints
 * Licence GPL (c) 2015 Teddy Payet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

function redacrest_affiche_gauche($flux)
{
    if ($flux['args']['exec'] == 'naviguer') {
        $contexte['id_rubrique'] = $flux['args']['id_rubrique'];
        $flux['data'].= recuperer_fond('prive/squelettes/inclure/redacrest_rubriques', $contexte);
    }

    if ($flux['args']['exec'] == 'accueil') {
        $auteur_connecte = $GLOBALS['auteur_session'];
        if ($auteur_connecte['statut'] == '1comite') {
            $contexte['id_auteur']                 = $auteur_connecte['id_auteur'];
            $contexte['nom']                 = $auteur_connecte['nom'];
            $contexte['statut']                 = $auteur_connecte['statut'];
            $flux['data'].= recuperer_fond('prive/squelettes/inclure/redacrest_accueil', $contexte);
        }
    }

    return $flux;
}
?>