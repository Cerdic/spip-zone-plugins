<?php
function chants_declarer_tables_interfaces($interfaces) {
    $interfaces['table_des_tables']['chants'] ='chants';
    $interfaces['table_des_traitements']['PAROLES']['chants'] = _TRAITEMENT_RACCOURCIS;
    return $interfaces;
}

function chants_declarer_tables_objets_sql($tables){
            $tables['spip_chants'] = array(

                    'page' => "chant",
                    'texte_retour' => 'chant:icone_retour_chant',
                    'texte_modifier' => 'chant:icone_modifier_chant',
                    'texte_creer' => 'chant:icone_ecrire_chant',
                    'texte_objets' => 'chant:chants',
                    'texte_objet' => 'chant:chant',
                    'texte_signale_edition' => 'chant:texte_travail_chant',
                    'info_aucun_objet'=> 'chant:info_aucun_chant',
                    'info_1_objet' => 'chant:info_1_chant',
                    'info_nb_objets' => 'chant:info_nb_chants',
                    'texte_logo_objet' => 'chant:logo_chant',
                    'texte_langue_objet' => 'chant:titre_langue_chant',
                    'principale' => "oui",
		    'titre' => 'titre, lang',
		    'date' => 'date',
                    'field'=> array(
                            "id_chant" => "bigint(21) NOT NULL",
                            "id_rubrique" => "bigint(21) DEFAULT '0' NOT NULL",
			    "id_secteur" => "bigint(21) DEFAULT '0' NOT NULL",
                            "titre" => "tinytext DEFAULT '' NOT NULL",
                            "alias" => "tinytext DEFAULT '' NOT NULL",
                            "copyright" => "tinytext DEFAULT '' NOT NULL",
                            "date_annee" => "year DEFAULT '0000' NOT NULL",
			    "date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
                            "paroles" => "text DEFAULT '' NOT NULL",
                            "numero" => "tinytext DEFAULT '' NOT NULL",
                            "presentation" => "tinytext DEFAULT '' NOT NULL",
                            "ligne_principale" => "tinytext DEFAULT '' NOT NULL",
                            "ccli" => "tinytext DEFAULT '' NOT NULL",
                            "capo" => "decimal(2,0) DEFAULT '0' NOT NULL",
                            "tonalite" => "tinytext DEFAULT '' NOT NULL",
                            "tempo" => "tinytext DEFAULT '' NOT NULL",
                            "signature" => "tinytext DEFAULT '' NOT NULL",
			    "statut" => "VARCHAR(255) DEFAULT 'prepa' NOT NULL",
			    "lang" => "VARCHAR(10) DEFAULT '' NOT NULL",
			    "langue_choisie" => "VARCHAR(3) DEFAULT 'non'",
			    "id_trad" => "bigint(21) DEFAULT '0' NOT NULL",
                            "maj"   => "TIMESTAMP"
                    ),
                    'key' => array(
                            "PRIMARY KEY"   => "id_chant",
			    "KEY id_rubrique" => "id_rubrique",
			    "KEY id_secteur" => "id_secteur",
			    "KEY id_trad" => "id_trad",
			    "KEY lang" => "lang",
			    "KEY statut" => "statut, date",
                    ),
		    'join' => array(
			"id_chant" => "id_chant",
			"id_rubrique" => "id_rubrique"
		    ),
		    'champs_editables' => array(
			"titre","copyright","date_annee","numero","presentation","ccli","capo","tempo","signature","paroles","tonalite","alias","ligne_pincipale"
		    ),
		    'champs_versionnes' => array(
			"titre","copyright","date_annee","numero","presentation","ccli","capo","tempo","signature","paroles","tonalite","alias","ligne_editoriale"
		    ),
		    'rechercher_champs' => array(
			"titre" => 7,"copyright" => 2,"date_annee" => 3,"ccli" => 6,"tempo" => 1,"signature" => 1,"paroles" => 8, "alia" => 6, "tonalite" => 1
		    ),
		    'statut'=> array(
			array(
			    'champ' => 'statut',
			    'publie' => 'publie',
			    'previsu' => 'publie,prop,prepa',
			    'exception' => array('statut','tout'),
			)
		    ),
		    'statut_textes_instituer' => array(
			'prepa' => 'texte_statut_en_cours_redaction',
                        'prop' => 'texte_statut_propose_evaluation',
                        'publie' => 'texte_statut_publie',
                        'refuse' => 'texte_statut_refuse',
                        'poubelle' => 'texte_statut_poubelle',
		    ),
		    'texte_changer_statut' => 'chant:texte_changer_statut',
		    'tables_jointures' => array(
                        'profondeur' => 'rubriques'
		    ),
            );
           
            return $tables;
    }
    
?>