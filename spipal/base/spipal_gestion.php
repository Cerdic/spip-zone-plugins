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

function spipal_upgrade($meta, $courante, $table='meta')
{
	$installee = isset($GLOBALS['spipal_metas']['base_version']) ? 
		$GLOBALS['spipal_metas']['spipal_base_version'] : 0;
        
	if (!$installee) {
		include_spip('base/create');
		alterer_base($GLOBALS['spipal_tables_principales'],
			     $GLOBALS['spipal_tables_auxiliaires']);
		ecrire_meta($meta, $courante, NULL, $table);
		spipal_base_init($table);
	} else {
		$GLOBALS['spipal_maj_erreur'] = 0;
		if ($courante > $installee) {
			include_spip('base/upgrade');
			$n = maj_while($installee, $courante, $GLOBALS['spipal_maj'], $meta, $table);
			$n = $n ? $n[0] : $GLOBALS['spipal_maj_erreur'];
			// signaler que les dernieres MAJ sont a refaire
			if ($n) ecrire_meta($meta, $n-1, $table);
		}
		return $GLOBALS['spipal_maj_erreur'];
	}
}
	
function spipal_base_init($table)
{
	foreach(array(
                    'url_paypal'          => 'https://www.paypal.com/cgi-bin/webscr',  //prod
                    //'url_paypal'          => 'https://www.sandbox.paypal.com/fr/cgi-bin/webscr',  //test
                    'notify_url'          => 'www.paypal.com',
                    'url_retour'          => $GLOBALS['meta']['adresse_site'],
                    'compte_paypal'       => $GLOBALS['meta']['email_webmaster'],
                    'style_page'          => 'PayPal',
                    'donner'              => 'on',
                    'vendre'              => '',
                    'garder_notification' => 'on'
		      ) as $k => $v) {
	  ecrire_meta($k, $v, NULL, $table);
	}
}

function spipal_vider_tables($nom_meta, $table)
{
	global $spipal_tables_principales, $spipal_tables_auxiliaires;
	effacer_meta($nom_meta, $table);
	foreach($spipal_tables_principales as $nom => $desc)
		sql_drop_table($nom);
	foreach($spipal_tables_auxiliaires as $nom => $desc)
		sql_drop_table($nom);
	spip_log("$table $nom_meta desinstalle");
}

// Tableau des modifs de la base a l'avenir.
$GLOBALS['spipal_maj']=array();

?>
