<?php

#
# Ces fichiers sont a placer dans le repertoire base/ de votre plugin
#
/**
 * Gestion de l'importation de `spip_maps`.
 **/

/**
 * Fonction d'import de la table `spip_maps`
 * à utiliser dans le fichier d'administration du plugin.
 *
 *     ```
 *     include_spip('base/importer_spip_maps');
 *     $maj['create'][] = array('importer_spip_maps');
 *     ```
 **/
function importer_spip_maps()
{

    ######## VERIFIEZ LE NOM DE LA TABLE D'INSERTION ###########
    $table = 'spip_maps';

    // nom_du_champ_source => nom_du_champ_destination
    // mettre vide la destination ou supprimer la ligne permet de ne pas importer la colonne.
    $correspondances = array(
        'id_map' => 'id_map',
        'titre' => 'titre',
        'descriptif' => 'descriptif',
        'width' => 'width',
        'height' => 'height',
        'code_map' => 'code_map',
        'background_color' => 'background_color',
        'border_color' => 'border_color',
        'border_opacity' => 'border_opacity',
        'border_width' => 'border_width',
        'color' => 'color',
        'enable_zoom' => 'enable_zoom',
        'hover_color' => 'hover_color',
        'hover_opacity' => 'hover_opacity',
        'normalize_function' => 'normalize_function',
        'scale_colors' => 'scale_colors',
        'selected_color' => 'selected_color',
        'selected_region' => 'selected_region',
        'show_tooltip' => 'show_tooltip',
        'data_name' => 'data_name',
        'statut' => 'statut',
        'maj' => 'maj',
    );

    // transposer les donnees dans la nouvelle structure
    $inserts = array();
    list($cles, $valeurs) = donnees_spip_maps();
    // on remet les noms des cles dans le tableau de valeur
    // en s'assurant de leur correspondance au passage
    if (is_array($valeurs)) {
        foreach ($valeurs as $v) {
            $i = array();
            foreach ($v as $k => $valeur) {
                $cle = $cles[$k];
                if (isset($correspondances[$cle]) and $correspondances[$cle]) {
                    $i[ $correspondances[$cle] ] = $valeur;
                }
            }
            $inserts[] = $i;
        }
        unset($valeurs);

        // inserer les donnees en base.
        $nb_inseres = 0;
        // ne pas reimporter ceux deja la (en cas de timeout)
        $nb_deja_la = sql_countsel($table);
        $inserts = array_slice($inserts, $nb_deja_la);
        $nb_a_inserer = count($inserts);
        // on decoupe en petit bout (pour reprise sur timeout)
        $inserts = array_chunk($inserts, 100);
        foreach ($inserts as $i) {
            sql_insertq_multi($table, $i);
            $nb_inseres += count($i);
            // serie_alter() relancera la fonction jusqu'a ce que l'on sorte sans timeout.
            if (time() >= _TIME_OUT) {
                // on ecrit un gentil message pour suivre l'avancement.
                echo "<br />Insertion dans $table relanc&eacute;e : ";
                echo "<br />- $nb_deja_la &eacute;taient d&eacute;j&agrave; l&agrave;";
                echo "<br />- $nb_inseres ont &eacute;t&eacute; ins&eacute;r&eacute;s.";
                $a_faire = $nb_a_inserer - $nb_inseres;
                echo "<br />- $a_faire &agrave; faire.";

                return;
            }
        }
    }
}

/**
 * Donnees de la table spip_maps.
 **/
function donnees_spip_maps()
{
    $cles = array('id_map', 'titre', 'descriptif', 'width', 'height', 'code_map', 'background_color', 'border_color', 'border_opacity', 'border_width', 'color', 'enable_zoom', 'hover_color', 'hover_opacity', 'normalize_function', 'scale_colors', 'selected_color', 'selected_region', 'show_tooltip', 'data_name', 'statut', 'maj');

    $valeurs = array(
        array('1', 'France Continentale', '', '530', '581', 'france_continent', '#ffffff', '#ffffff', '1.00', '1', '#13bae0', 'false', '#68D0EA', '0.65', 'linear', '', '', '', 'true', '', 'prepa', '2015-08-30 09:47:10'),
        array('2', 'France', '', '819', '1043', 'france_regions', '#ffffff', '#ffffff', '1.00', '1', '#13bae0', 'false', '', '0.65', 'linear', '', '', '', 'true', '', 'prepa', '2015-08-30 09:47:10'),
        array('3', 'Map monde', '', '950', '550', 'world_en', 'null', '', '1.00', '1', '#ffffff', 'true', '', '0.70', 'polynomial', '#C8EEFF, #006491', '#666666', '', 'true', 'gdp', 'prepa', '2015-08-30 15:55:08'),
        array('4', 'Algérie', '', '508', '500', 'dz_fr', '', '', '1.00', '0', '', 'true', '', '1.00', '', '', '', '', 'true', '', 'prepa', '2015-08-30 13:01:30'),
        array('5', 'Europe', '', '680', '520', 'europe_en', '', '', '0.00', '0', '', 'false', '', '0.00', '', '', '', '', 'false', '', 'prepa', '2015-08-30 13:48:17'),
        array('6', 'Allemagne', '', '592', '801', 'germany_en', '', 'null', '1.00', '0', '', 'true', '', '0.00', '', '', '', '', '', '', 'prepa', '2015-08-30 19:02:50'),
    );

    return array($cles, $valeurs);
}
