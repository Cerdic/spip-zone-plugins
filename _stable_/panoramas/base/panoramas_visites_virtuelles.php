<?php
// -----------------------------------------------------------------------------
// Declaration des tables visites_virtuelles
include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;



//-- Table visites_virtuelles ------------------------------------------
$visites_virtuelles = array(
		"id_visite"	=> "bigint(21) NOT NULL",
		"titre"	=> "text NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"largeur"	=> "int(21) DEFAULT '600' NOT NULL",
		"hauteur"	=> "int(21) DEFAULT '400' NOT NULL",
		"id_carte"	=> "bigint(21) NOT NULL", /* carte 2D associée à la visite, id du document */
		"id_lieu_depart"	=> "bigint(21) NOT NULL",
		"mode_jeu"	=> "ENUM('oui', 'non') DEFAULT 'non' NOT NULL", /* il s'agit d'un jeu plus que d'une visite virtuelle, on affiche entre autre le bouton démarrer au départ + mémorisation des scores.. etc... */
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$visites_virtuelles_key = array(
		"PRIMARY KEY"	=> "id_visite"
		);

$tables_principales['spip_visites_virtuelles'] =
	array('field' => &$visites_virtuelles, 'key' => &$visites_virtuelles_key);

global $table_primary;
$table_primary['visites_virtuelles']="id_visitevirtuelle";

global $table_date;
$table_date['visites_virtuelles'] = 'date_debut';
// si on declare les tables dans $table_des_tables, il faut mettre le prefixe

// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['visites_virtuelles']='visites_virtuelles';



//-- Table visites_virtuelles_lieux ------------------------------------------
$visites_virtuelles_lieux = array(
		"id_lieu"	=> "bigint(21) NOT NULL",
		"id_visite"	=> "bigint(21) NOT NULL",
		"titre"	=> "text NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"id_photo"	=> "bigint(21) NOT NULL", /* photo panoramique : id de l'image */
		"id_audio"	=> "bigint(21)", /* fond sonore : id du document mp3 */
		"audio_repeter"	=> "ENUM('oui', 'non') DEFAULT 'non' NOT NULL", /* fond sonore : jouer en continu ? */
		"boucler"	=> "ENUM('oui', 'non') DEFAULT 'oui' NOT NULL", /* le panorama correspond-t'il à une vue à 360 degrès ? */
		"position_x_carte"	=> "int(21) NOT NULL", /* emplacement du lieu sur la carte 2D */
		"position_y_carte"	=> "int(21) NOT NULL", 
		"acces_carte"	=> "ENUM('toujours', 'si_visite') DEFAULT 'toujours' NOT NULL", /* permettre l'accès au lieu depuis la carte : toujours, ou uniquement si le lieu a déjà été visité */
		"url_carte"	=> "text NOT NULL", /* pour pointer sur la carte vers une url marticulière et non la page lieu */
		"icone_carte"	=> "bigint(21) NOT NULL", /* icone symbolisant le lieu sur la carte */
		"decalage_x"	=> "int(21) NOT NULL", /* décalage par défaut du panorama (start_position) */
		"documents_associes"	=> "text NOT NULL", /* id des documents associés au lieu, séparés par des virgules */
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$visites_virtuelles_lieux_key = array(
		"PRIMARY KEY"	=> "id_lieu"
		);

$tables_principales['spip_visites_virtuelles_lieux'] =
	array('field' => &$visites_virtuelles_lieux, 'key' => &$visites_virtuelles_lieux_key);

global $table_primary;
$table_primary['visites_virtuelles_lieux']="id_visitevirtuelle";

global $table_date;
$table_date['visites_virtuelles_lieux'] = 'date_debut';
// si on declare les tables dans $table_des_tables, il faut mettre le prefixe

// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['visites_virtuelles_lieux']='visites_virtuelles_lieux';



//-- Table visites_virtuelles_interactions ------------------------------------------
$visites_virtuelles_interactions = array(
		"id_interaction"	=> "bigint(21) NOT NULL",
		"id_lieu"	=> "bigint(21) NOT NULL",
		"id_visite"	=> "bigint(21) NOT NULL",
		"titre"	=> "text NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"x1"	=> "bigint(21) NOT NULL", /* coordonnées de l'interaction sur l'image, en pixels */
		"y1"	=> "bigint(21) NOT NULL",
		"x2"	=> "bigint(21) NOT NULL",
		"y2"	=> "bigint(21) NOT NULL",
		"id_image_fond"	=> "bigint(21) NOT NULL", /* afficher une image dans le cadre au lieu d'un simple fond coloré transparent */
		"type"	=> "ENUM('descriptif', 'lieu', 'visite', 'article', 'rubrique', 'document', 'jeu', 'url', 'objet', 'personnage') DEFAULT 'descriptif' NOT NULL",
		"x_lieu_cible"	=> "bigint(21)", /* lorsque l'interaction pointe vers un autre lieu, permet de spécifier un décalage en x de la vue panoramique */
		"id_article_cible"	=> "bigint(21)", /* l'interaction pointe vers un article du site */
		"id_rubrique_cible"	=> "bigint(21)", /* l'interaction pointe vers une rubrique du site */
		"id_lieu_cible"	=> "bigint(21)", /* l'interaction pointe vers un autre lieu */
		"id_document_cible"	=> "bigint(21)", /* l'interaction pointe vers un document (qui sera affiché en surimpression via thickbox) */
		"id_visite_cible"	=> "bigint(21)", /* l'interaction ammène dans une autre visite */
		"id_jeu_cible"	=> "bigint(21)", /* l'interaction est un jeu (nécessite le plugin jeu) */
		"url_cible"	=> "text", /*'interaction pointe vers une url */
		"id_objet"	=> "bigint(21)", /* lorsque l'interaction est un objet, id de l'image de l'objet */
		"id_personnage"	=> "bigint(21)", /* lorsque l'interaction est un personnage, id de l'image du personnage */
		"id_personnage_survol"	=> "bigint(21)", /* lorsque l'interaction est un personnage, id de l'image du personnage affichée au survol de la souris */
		"id_objet_activation"	=> "bigint(21)", /* l'interaction n'est active que si l'objet a été ramassé */
		"id_jeu_activation"	=> "bigint(21)", /* l'interaction n'est active que si le joueur a répondu correctement aux questions posées */
		"id_lieu_activation"	=> "bigint(21)", /* l'interaction n'est active que si le joueur est passé par un lieu donné */
		"texte_avant_activation"	=> "text NOT NULL", /* texte affiché lorsque l'interaction n'est pas active */
		"texte_apres_activation"	=> "text NOT NULL", /* texte affiché lorsque l'interaction vient d'être activée */
		"id_audio_avant_activation"	=> "bigint(21)", /* son joué lorsque l'interaction n'est pas active */
		"id_audio_apres_activation"	=> "bigint(21)", /* son joué lorsque l'interaction vient d'être activée */
		"images_transition"	=> "text NOT NULL", /* liste des images à afficher séquentiellement pendant la transition (id des images séparés par des virgules) */
		"images_transition_delai"	=> "bigint(21) NOT NULL", /* durée d'affichage d'une image avant de passer à la suivante */
		"id_film_transition"	=> "bigint(21) NOT NULL", /* jouer un film pendant la transition */
		"film_transition_duree"	=> "bigint(21) NOT NULL", /* durée du film pendant la transition */
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$visites_virtuelles_interactions_key = array(
		"PRIMARY KEY"	=> "id_interaction"
		);

$tables_principales['spip_visites_virtuelles_interactions'] =
	array('field' => &$visites_virtuelles_interactions, 'key' => &$visites_virtuelles_interactions_key);

global $table_primary;
$table_primary['visites_virtuelles_interactions']="id_visitevirtuelle";

global $table_date;
$table_date['visites_virtuelles_interactions'] = 'date_debut';
// si on declare les tables dans $table_des_tables, il faut mettre le prefixe

// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['visites_virtuelles_interactions']='visites_virtuelles_interactions';


?>