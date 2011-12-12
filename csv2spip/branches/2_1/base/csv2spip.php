<?php 
    if (!defined("_ECRIRE_INC_VERSION")) return;

function csv2spip_declarer_tables_principales($tables_principales){
    $spip_tmp_csv2spip = array(
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
	
	$tables_principales['spip_tmp_csv2spip'] =
		array('field' => &$spip_tmp_csv2spip, 'key' => &$spip_tmp_csv2spip_key);

	return $tables_principales;
}

?>
