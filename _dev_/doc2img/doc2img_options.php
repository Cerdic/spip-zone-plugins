<?php

/*! \file doc2img_options.php
 *  \brief Ensemble des fonctions, variables à chargée coté privé et public 
 *
 *  On déclare les tables doc2img comme faisant partie intégrante de SPIP 
 */   

    //Ajout de champs supplémentaires
    include_spip('base/serial');

	// declaration des tables
	$GLOBALS['table_des_tables']['doc2img'] = 'doc2img';
	global $tables_principales;

    // déclaration des champes
    $spip_doc2img['id_doc2img'] = "bigint(21) NOT NULL";
    $spip_doc2img['id_document'] = "bigint(21) NOT NULL";
    $spip_doc2img['fichier'] = "varchar(255) NOT NULL";

    // déclaration des clef primaire et etrangére
    $spip_doc2img_key = array("PRIMARY KEY"	=> "id_doc2img", 'KEY id_document' => 'id_document');

    //on sauvegarde le tout dans SPIP
    $tables_principales['spip_doc2img']  =	array('field' => &$spip_doc2img, 'key' => &$spip_doc2img_key);

?>
