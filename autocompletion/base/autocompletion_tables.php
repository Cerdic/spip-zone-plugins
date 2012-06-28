<?php

function autocompletion_declarer_tables_objets_sql($tables){

        $tables = array();
	//-- Table spip_communes -----------------------------------------------------------
	$tables['spip_communes'] = array(
                                    'principale' => "oui",
                                    'field'      => array(
                                        "id_commune"    => "int(11) auto_increment",
                                        "code_departement" => "varchar(3) NOT NULL default ''",
                                        "code_postal"   => "varchar(12) NOT NULL default ''",
                                        "code_insee"    => "varchar(5)  NOT NULL default ''",
                                        "prefixe"       => "varchar(8)  NOT NULL default ''",
                                        "lib_commune"   => "varchar(50) NOT NULL default ''",
                                        "latitude"      => "varchar(20) NOT NULL default ''",
                                        "longitude"     => "varchar(20) NOT NULL default ''"
                                        ),
                                    'key' => array(
                                        "PRIMARY KEY"            => "id_commune",
                                        "INDEX code_departement" => "code_departement",
                                        "INDEX code_postal"      => "code_postal",
                                        "INDEX lib_commune"      => "lib_commune",
                                        "INDEX code_insee"       => "code_insee"
                                    ),
                                    'titre' => "spip_communes"
                                    );
        
	//-- Table spip_departements -----------------------------------------------------------
        $tables['spip_departements'] = array(
                                    'principale' => "oui",
                                    'field'      => array(
                                        "id_departement"   => "int(11) auto_increment",
                                        "code_departement" => "varchar(3)  NOT NULL default ''",
                                        "code_region"      => "varchar(2)  NOT NULL default ''",
                                        "prefixe"          => "varchar(8)  NOT NULL default ''",
                                        "lib_departement"  => "varchar(40) NOT NULL default ''"
                                        ),
                                    'key' => array(
                                        "PRIMARY KEY"            => "id_departement",
                                        "INDEX code_departement" => "code_departement",
                                        "INDEX code_region"      => "code_region"
                                    ),
                                    'titre' => "spip_departements"
                                    );
        
	//-- Table spip_regions -----------------------------------------------------------
        $tables['spip_regions'] = array(
                                    'principale' => "oui",
                                    'field'      => array(
                                        "id_region"   => "int(11) auto_increment",
                                        "code_region" => "varchar(2)  NOT NULL default ''",
                                        "prefixe"     => "varchar(8)  NOT NULL default ''",
                                        "lib_region"  => "varchar(40) NOT NULL default ''"
                                        ),
                                    'key' => array(
                                        "PRIMARY KEY"       => "id_region",
                                        "INDEX code_region" => "code_region"
                                    ),
                                    'titre' => "spip_regions"
                                    );
	return $tables;
}


//function autocompletion_declarer_tables_interfaces($interfaces) {
//    
//    $interfaces['table_des_tables']['spip_communes']        = 'spip_communes';
//    $interfaces['table_des_tables']['spip_departements']    = 'spip_departements';
//    $interfaces['table_des_tables']['spip_regions']         = 'spip_regions';
//    return $interfaces;
//}

?>
