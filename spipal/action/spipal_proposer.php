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

function action_spipal_proposer_dist() {
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = intval($securiser_action());
    
    $action     =  _request('action_vente');
    if (!$action )
      sql_delete('spip_spipal_produits', "id_article=" . $arg);
    else  {
        $_REQUEST['id_article'] = $arg;
        $_REQUEST['don']        = $action;

        if ( !_request('ref_produit') )	  $_REQUEST['ref_produit'] = $arg;

	include_spip("inc/spipal_tabledata");
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table('spip_spipal_produits');
	if (est_payable($arg))
	  maj_item('spip_spipal_produits', $desc);
	else declare_item('spip_spipal_produits', $desc);
    }
}

?>
