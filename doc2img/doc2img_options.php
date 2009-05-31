<?php

/*! \file doc2img_options.php
 *  \brief Ensemble des fonctions, variables � charg�e cot� priv� et public 
 *
 *  On d�clare les tables doc2img comme faisant partie int�grante de SPIP 
 */   

    //Ajout de champs suppl�mentaires
    include_spip('base/serial');

	// declaration des tables
	$GLOBALS['table_des_tables']['doc2img'] = 'doc2img';
	global $tables_principales;

    // d�claration des champes
    $spip_doc2img['id_doc2img'] = "bigint(21) NOT NULL";
    $spip_doc2img['id_document'] = "bigint(21) NOT NULL";
    $spip_doc2img['fichier'] = "varchar(255) NOT NULL";
    $spip_doc2img['page'] = "int NOT NULL";

    // d�claration des clef primaire et etrang�re
    $spip_doc2img_key = array("PRIMARY KEY"	=> "id_doc2img", 'KEY id_document' => 'id_document', 'UNIQUE KEY document' => 'id_document,page');

    //on sauvegarde le tout dans SPIP
    $tables_principales['spip_doc2img']  =	array('field' => &$spip_doc2img, 'key' => &$spip_doc2img_key);

?>
