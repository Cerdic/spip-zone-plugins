<?php
/*
 * Plugin COG-GPS
 * (c) 2009 Guillaume Wauquier
 * Distribue sous licence GPL
 *
 */

function coggps_config_tab_fichier()
{
    return array(
        'FR' => array(
            'nom' => 'Geonames : Informations géographiques FRANCE',
            'fichier' => 'http://download.geonames.org/export/dump/FR.zip',
            'nom_fichier' => 'FR.txt'
        )
    );
}

function coggps_config_correspondance_colonne()
{
    return array(
        'FR' => array(
            'colonnes' => array(
                "lon" => 5,
                "lat" => 4,
                "elevation" => 15,
                "elevation_moyenne" => 16,
                "population" => 14,
                "autre_nom" => 3
            ),
            'liaison' => 13,
            'filtre' => array('cle' => 7, 'valeur' => 'ADM4')
        )

    );
}


?>