<?php 
    if (!defined("_ECRIRE_INC_VERSION")) return;
    
//    global $tables_principales;
    global $tables_auxiliaires;
    
    $spip_tmp_csv2spip = array(
//    	"id_mot" 	=> "bigint(21) NOT NULL",
//    	"id_syndic_article" 	=> "bigint(21) NOT NULL");
        
        "id" 	      => "INT(11) NOT NULL AUTO_INCREMENT", 
        "nom" 	      => "TEXT NOT NULL", 
        "prenom"      => "TEXT NOT NULL", 
        "groupe"      => "TEXT NOT NULL", 
        "ss_groupe"	  => "TEXT NOT NULL", 
        "mdp" 	      => "TEXT NOT NULL", 
        "pseudo_spip" => "TEXT NOT NULL", 
        "mel" 	      => "TEXT NOT NULL", 
        "id_spip" 	  => "INT(11) NOT NULL"
    );

    
    $spip_tmp_csv2spip_key = array(
    	"PRIMARY KEY" 	=> "id"
    );
    
    $tables_auxiliaires['spip_tmp_csv2spip'] = array(
    	'field' => &$spip_tmp_csv2spip,
    	'key' => &$spip_tmp_csv2spip_key);
    
//    global $tables_jointures;
//    $tables_jointures['spip_mots'][] = 'mots_syndic_articles';
//    $tables_jointures['spip_syndic_articles'][] = 'mots_syndic_articles';


?>