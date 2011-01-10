<?php

/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) V1 2007 Thierry Schmit                                   *
 *  Copyright (c) V2 2011 Emmanuel Saint-James                             *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip("inc/spipal_tabledata");

function action_spipal_proposer_dist() {
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();
    
    $action     = _request('action_vente');
    $options = array();
    if ( $action ) {
        $_REQUEST['id_article'] = _request('arg');
        $options['action']      = est_a_vendre($_REQUEST['id_article'])?'maj':'creer';
        $_REQUEST['don']        = ( _request('action_vente') == AV_VENTE_DON )?1:0;
        if ( !_request('ref_produit') )
            $_REQUEST['ref_produit'] = $_REQUEST['id_article'];
    }
    else {
        $_REQUEST['r_pks']       = _request('arg');
        $options['action']       = 'supprimer';
        $options['confirmation'] = true;
    }
    mbt_maj_table_depuis_form('spip_spipal_produits', $options);
}

?>
