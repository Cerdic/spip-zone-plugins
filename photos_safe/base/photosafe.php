<?php


if (!defined('_ECRIRE_INC_VERSION')) return;


function photosafe_declarer_tables_principales ($tables_principales) {

    $tables_principales['spip_photosafe'] = array(
        'field' => array(
            'id_photosafe' => "bigint(21) NOT NULL",
	     'id_objet' => "bigint(21) DEFAULT '0' NOT NULL",
            'objet'    => "VARCHAR(25) DEFAULT '' NOT NULL",
	    'safe'    => "VARCHAR(6) DEFAULT 'non' NOT NULL",
        ),
        'key' => array(
            'PRIMARY KEY' => "id_photosafe",
        ),
    );


    return $tables_principales;
}

?>