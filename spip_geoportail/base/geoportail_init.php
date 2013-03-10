<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* Procedure d'installation
*
**/
include_spip('inc/compat_192');

// Transferer la vignette
function geoportail_set_file_icon ($type)
{	$source = _DIR_PLUGIN_GEOPORTAIL."vignettes/".$type.".png";
	$dest = sous_repertoire(_DIR_IMG, "icones").$type.".png";
	@copy($source, $dest);
}

function geoportail_install($action){
	switch ($action)
	{	// La base est deja cree ?
		case 'test': 
			include_spip('base/abstract_sql');
			
			// Nouveaux type de fichiers non pris en compte par SPIP
			//*** Ajouter les fichiers GPX
			$row = spip_fetch_array(spip_query("SELECT * FROM spip_types_documents WHERE extension='gpx'"));
			if (!$row)
			{	spip_query("INSERT IGNORE INTO spip_types_documents (extension, titre) VALUES ('gpx', 'GPX')");
				spip_query("UPDATE spip_types_documents	SET mime_type='application/xml' WHERE extension='gpx'");
				geoportail_set_file_icon ("gpx");
			}
			//*** Ajouter fichier GXT (format Geoconcept)
			$row = spip_fetch_array(spip_query("SELECT * FROM spip_types_documents WHERE extension='gxt'"));
			if (!$row) 
			{	spip_query("INSERT IGNORE INTO spip_types_documents (extension, titre, mime_type) VALUES ('gxt', 'GXT', 'text/plain')");
				geoportail_set_file_icon ("gxt");
			}

			// Mettre a jour id dep/commune
			$desc = sql_showtable("spip_geopositions", true, '');
			if (isset($desc['field']['id_geoposition']) && !isset($desc['field']['id_com']))
			{	spip_query("ALTER TABLE spip_geopositions ADD id_dep char(3)");
				spip_query("ALTER TABLE spip_geopositions ADD id_com char(3)");
			}
			// Mettre a jour le niveau d'affichage des geoservices
			$desc = sql_showtable("spip_geoservices", true, '');
			if (isset($desc['field']['id_geoservice']) && !isset($desc['field']['niveau']))
			{	spip_query("ALTER TABLE spip_geoservices ADD niveau INTEGER DEFAULT '0' NOT NULL");
			}
			// Mettre a jour la zone des geoservices
			if (isset($desc['field']['id_geoservice']) && !isset($desc['field']['zone']))
			{	spip_query("ALTER TABLE spip_geoservices ADD zone varchar(3) NOT NULL default 'WLD'");
			}
			// Mettre a jour la zone des geoservices
			if (isset($desc['field']['id_geoservice']) && !isset($desc['field']['selection']))
			{	spip_query("ALTER TABLE spip_geoservices ADD selection TINYINT(1) default '0'");
			}
			
			// Pas de RGC
			$desc = sql_showtable("spip_georgc", true, '');
			if (!isset($desc['field']['id_dep'])) return false;
			
			// Charger la base
			$desc = sql_showtable("spip_geopositions", true, '');
			return (isset($desc['field']['id_geoposition']));
			break;

		// Installer la base
		case 'install':
			include_spip('base/create');
			// On demande la creation de la base
			include_spip('base/geoportail');
			creer_base();
			
			// Creer un index Fulltext
			spip_query("ALTER TABLE spip_georgc ADD FULLTEXT(asciiname,cpostal)");
			
			// Par defaut, on travaille sur les auteurs et les articles
			ecrire_meta('geoportail_geoarticle',1);
			ecrire_meta('geoportail_geoauteur',1);
 			ecrire_metas();
			
			break;

		// Supprimer la base
		case 'uninstall':
			spip_query("DROP TABLE spip_geopositions");
			spip_query("DROP TABLE spip_geoservices");
			spip_query("DROP TABLE spip_georgc");
			ecrire_meta('geoportail_rgc',null);
			break;
	}
}	
	
?>