<?php
/**
 * Plugin commandes pour Spip 2.1
 * Licence GPL 
 * Cyril MARION - (c) 2010 Ateliers CYM http://www.cym.fr
 *
 */


function commandes_declarer_tables_principales($tables_principales){
	// montant payes ou a payer
	$tables_principales['spip_paniers']['field']['montant']         = 'float default NULL';
	$tables_principales['spip_paniers']['field']['reference']       = 'varchar(30) not null default ""';
	$tables_principales['spip_paniers']['field']['date_commande']   = 'datetime not null default "0000-00-00 00:00:00"';
	$tables_principales['spip_paniers']['field']['date_paiement']   = 'datetime not null default "0000-00-00 00:00:00"';
	return $tables_principales;
}

function commandes_declarer_tables_auxiliaires($tables_auxiliaires){
	// montant payes ou a payer
	$tables_auxiliaires['spip_paniers_liens']['field']['montant']    	  = 'float default NULL';
	$tables_auxiliaires['spip_paniers_liens']['field']['montant_taxe']    = 'decimal(4,3) default null';
	$tables_auxiliaires['spip_paniers_liens']['field']['designation']     = 'text not null default ""';
	return $tables_auxiliaires;
}

?>
