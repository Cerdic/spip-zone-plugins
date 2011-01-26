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
define ('AV_VENTE_DON',     1);
define ('AV_VENTE_ACHAT',   2);

function est_payable($id_article) {
	return sql_fetsel('*', 'spip_spipal_produits', "id_article=" . intval($id_article));
}

// pour executer les squelettes comportant la balise Meta
include_spip('balise/meta');
include_spip('base/spipal');
// charger les metas donnees
$inc_meta = charger_fonction('meta', 'inc'); // inc_version l'a deja chargee
$inc_meta('spipal_metas'); 
?>
