<?php
/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) 2007 Thierry Schmit                                      *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

define('_DIR_PLUGIN_SPIPAL_ICONES', _DIR_PLUGIN_SPIPAL.'img_pack/');
define ('AV_VENTE_GRATUIT', 0);
define ('AV_VENTE_ACHAT',   1);
define ('AV_VENTE_DON',     2); //prix de vente = 0;

function est_a_vendre($id_article, $return_row = true) {
	$res = sql_select('*', 'spip_spipal_produits', "id_article=" . intval($id_article));
	$n = sql_count($res);
	if ( !$return_row )
	  return ( $n == 1 );
        return ( $n == 1 )? sql_fetch($res):null;
}

// pour executer les squelettes comportant la balise Meta
include_spip('balise/meta');
include_spip('base/spipal');
// charger les metas donnees
$inc_meta = charger_fonction('meta', 'inc'); // inc_version l'a deja chargee
$inc_meta('spipal_metas'); 
?>
