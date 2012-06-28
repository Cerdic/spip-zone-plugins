<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

    'google_map_titre'              => 'Google map',
    'google_map_explication'        => "Ajouter une google map (compatible avec l'autocomplétion ville/code postal ... géolocalisation)",
    'google_map_parametres'         => "Paramètres",
    'google_map_id_label'           => "Identifiant unique de la Google map",
    'google_map_id_explication'     => "L'identifiant doit être unique",
    
    'google_map_parametres_label'   => "Paramètres",
    'google_map_largeur_label'      => "Largeur de la carte",
    'google_map_largeur_explication'=> "Exemples : 200, pour que la carte face 200px de largeur",
    'google_map_hauteur_label'      => "Hauteur de la carte",
    'google_map_hauteur_explication'=> "Exemples : 200, pour que la carte face 200px de hauteur",
    'google_map_zoom_label'         => "Zoom initial",
    'google_map_zoom_explication'   => "De 1 à 20",
    'google_map_scrollwheel_label'         => "Zoom scrollable",
    'google_map_scrollwheel_explication'   => "Voulez-vous pouvoir agir sur le zoom avec la roulette de la souris",
    'google_map_centre_latitude_label'         => "Centrage carte (latitude)",
    'google_map_centre_latitude_explication'   => "La latitude 46.763056 correspondent au centre théorique de la france",
    'google_map_centre_longitude_label'         => "Centrage carte (longitude)",
    'google_map_centre_longitude_explication'   => "La longitude 2.424722 correspondent au centre théorique de la france",
    'google_map_type_map_label'     => "Type de carte",
    'google_map_type_map_explication'=> "ROADMAP   : normal (par défaut)<br />
                                        SATELLITE : satellite<br />
                                        HYBRID    : normale et carte satellite<br />
                                        TERRAIN   : basée sur les informations terrains ",
    'google_marqueur_latitude_label' => "Marqueur (latitude)",
    'google_marqueur_latitude_explication' => "Latitude du marqueur initial ",
    'google_marqueur_longitude_label' => "Marqueur (longitude)",
    'google_marqueur_longitude_explication' => "Longitude du marqueur initial",
    'google_marqueur_draggable_label' => "Marqueur déplaçable",
    'google_marqueur_draggable_explication' => "",
    
    'google_map_Autocompletion_label' => "Géolocalisation",
    'google_map_autocompletion_zoom_label' => "Zoom",
    'google_map_autocompletion_zoom_explication' => "Zoom de redimensionnement de  du zoom lors de ",
    
    'message_pas_resultat_commune_cp' => "Aucun résultat pour le code postal : %cp", // %cp sera remplacé par la valeur passée (str_replace)
    'message_pas_resultat_commune'    => "Aucun résultat",
    'message_pas_resultat_cp'         => "Aucun résultat",
    'message_pas_correspondance_ville_cp_gmap' => "Problème de correspondance entre la Google map et les classes css des villes et codes postaux.",
    'message_nouvelle_gmap'           => "Nouvelle Google map pour géolocalisation"

);

?>
    