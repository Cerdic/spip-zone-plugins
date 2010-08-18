<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : base/spipbb - tables necessaires au plugin    #
#  Authors : Chryjs, 2007 et als                           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#----------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined("_ECRIRE_INC_VERSION")) return;
if (defined("_BASE_SPIPBB")) return; else define("_BASE_SPIPBB", true);
include_spip('inc/spipbb_common'); //if (!defined("_INC_SPIPBB_COMMON")) die("bye");
//spipbb_log('included',2,__FILE__);
//
// Structure des tables
//

// maintenant dans spipbb_common
//$tables_spipbb = array( 'spip_visites_forums', 'spip_auteurs_spipbb', 'spip_spam_words', 'spip_spam_words_log', 'spip_ban_liste' );

// suivi des visites (sur la base de spip_visites_articles)

function spipbb_declarer_tables_principales($tables_principales){


$spip_visites_forums = array(
	"date"		=> "date NOT NULL",
	"id_forum" 	=> "bigint(21) NOT NULL",
	"visites" 	=> "int(10) NOT NULL default '0'",
	"maj" 		=> "timestamp" , // NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
	);

$spip_visites_forums_key = array(
	'PRIMARY KEY'	=> "date,id_forum"
	); //(`date`,`id_forum`)

$tables_principales['spip_visites_forums'] = array(
	'field' => &$spip_visites_forums,
	'key' => &$spip_visites_forums_key);

$spip_auteurs_spipbb = array( // table spip_auteurs_spipbb
	"id_auteur"	=> "bigint(21) NOT NULL", // primary key
	"spam_warnings"	=> "int(10) NOT NULL default '0'",
	'ip_auteur'	=> "varchar(16) default NULL",
	'ban_date'	=> "timestamp", // NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
	'ban'		=> "varchar(3) default 'non'"	// ban 'oui' 'non' default 'non'
	);

$spip_auteurs_spipbb_key = array(
		'KEY id_auteur' => "id_auteur"
		);

$tables_principales['spip_auteurs_spipbb'] = array(
		'field' => &$spip_auteurs_spipbb,
		'key' => &$spip_auteurs_spipbb_key );

$spip_spam_words = array(
	"id_spam_word"	=> "bigint(21) NOT NULL auto_increment",
	"spam_word"	=> "varchar(255) NOT NULL" );

$spip_spam_words_key = array( 'PRIMARY KEY' => "id_spam_word",
				'KEY spam_word' => "spam_word" );

$tables_principales['spip_spam_words'] = array(
		'field' => &$spip_spam_words,
		'key' => &$spip_spam_words_key );

$spip_spam_words_log = array(
	"id_spam_log"	=> "bigint(21) NOT NULL auto_increment",
	"id_auteur"	=> "bigint(21) NOT NULL",
	"ip_auteur"	=> "varchar(16) default NULL",
	"login"		=> "varchar(255) default NULL",
	"log_date"	=> "timestamp", // NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP"
	"titre"		=> "text",
	"message"	=> "mediumtext",
	"id_forum"	=> "bigint(21) NOT NULL",
	"id_article"	=> "bigint(21) NOT NULL"
		);

$spip_spam_words_log_key = array( 'PRIMARY KEY' => "id_spam_log" );

$tables_principales['spip_spam_words_log'] = array(
		'field' => &$spip_spam_words_log,
		'key' => &$spip_spam_words_log_key );


$spip_ban_liste = array(
	"id_ban"	=> "bigint(21) NOT NULL auto_increment",
	"ban_login"	=> "text",
	"ban_ip"	=> "varchar(16) default NULL",
	"ban_email"	=> "tinytext",
	"maj"		=> "timestamp" //  NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
		);

$spip_ban_liste_key = array( 'PRIMARY KEY' => "id_ban" );

$tables_principales['spip_ban_liste'] = array(
		'field' => &$spip_ban_liste,
		'key' => &$spip_ban_liste_key );

	return $tables_principales;
} // declarer_tables_principales

function spipbb_declarer_tables_auxiliaires($tables_auxiliaires){
	return $tables_auxiliaires;
} // declarer_tables_auxiliaires

//-- Relations ----------------------------------------------------
function spipbb_declarer_tables_interfaces($interface){

	//global $tables_jointures;
	$interface['tables_jointures']['visites_forums'][] = 'forums';
	$interface['tables_jointures']['auteurs_spipbb'][] = 'auteurs';

	// definir les noms raccourcis pour les <BOUCLE_(VISITES_FORUMS) ...
	$interface['table_des_tables']['visites_forums'] = 'visites_forums';

	
	return $interface;
} // declarer_tables_interfaces

/*
global $table_des_tables;
$table_des_tables['visites_forums'] = 'visites_forums';
$table_des_tables['auteurs_spipbb'] = 'auteurs_spipbb';
$table_des_tables['spam_words'] = 'spam_words';
$table_des_tables['spam_words_log'] = 'spam_words_log';
$table_des_tables['ban_liste'] = 'ban_liste';
*/


//
// API de SoyezCréateurs pour créer les objets SPIP 
// 
 
// fonction qui permet de trouver si un groupe de mots clés existe à partir du titre 
function find_groupe($titre) { 
       $titre = addslashes($titre); 
       spip_log("1. (find_groupe) recherche des occurences dans la table spip_groupes_mots de l'id de : $titre", "spipbb_install"); 
       $count = sql_countsel("spip_groupes_mots", "titre='$titre'"); 
       spip_log("2. (find_groupe) resultat de la recherche : $count occurences pour $titre", "spipbb_install"); 
       return $count; 
	} 
	 
// fonction pour trouver l'id du groupe de mots clés à partir du titre du groupe 
function id_groupe($titre) { 
 	$titre = addslashes($titre); 
    spip_log("1. (id_groupe) selection dans la table spip_groupes_mots de l'id de : $titre", "spipbb_install"); 
 	$result = sql_fetsel("id_groupe", "spip_groupes_mots", "titre='$titre'"); 
 	$resultat = $result['id_groupe']; 
    spip_log("2. (id_groupe) selection = $resultat pour $titre", "spipbb_install"); 
    return $resultat; 
	} 
 
//fonction qui permet de créer un groupe de mots clés 
function create_groupe($groupe, $descriptif='', $texte='', $unseul='non', $obligatoire='non', $articles='oui', $breves='non', $rubriques='non', $syndic='non', $evenements='non', $minirezo='oui', $comite='oui', $forum='non') { 
	$id_groupe = find_groupe($groupe); 
	$tables_liees = ''; 
 	if ($articles == 'oui')  
 	   $tables_liees.='articles,'; 
 	if ($breves == 'oui')  
       $tables_liees.='breves,'; 
 	if ($rubriques == 'oui')  
       $tables_liees.='rubriques,'; 
 	if ($syndic == 'oui')  
 	   $tables_liees.='syndic,'; 
 	if ($evenements == 'oui')  
       $tables_liees.='evenements,'; 
 	   spip_log("1. (create_groupe) pret a creer groupe : titre = $groupe. retour de find_groupe = $id_groupe", "spipbb_install"); 
 	if ($id_groupe == 0) { 
 	   $id_insert = sql_insertq( 
 	   "spip_groupes_mots", array( 
 	      "id_groupe" => '', 
 	      "titre" => $groupe, 
 	      "descriptif" => $descriptif, 
          "texte" => $texte, 
 	      "unseul" => $unseul, 
          "obligatoire" => $obligatoire, 
          "tables_liees" => $tables_liees, 
 	      "minirezo" => $minirezo, 
 	      "comite" => $comite, 
          "forum" => $forum 
 	      ) 
 	   ); 
 	spip_log("2. (create_groupe) retour de find_groupe : $id_groupe, donc insertion avec id = $id__insert et titre = $groupe", "spipbb_install"); 
 	}
 	
 	else if ($id_groupe > 0) { 
 	$id_insert = remplacer_groupe($groupe, $descriptif, $texte, $unseul, $obligatoire, $tables_liees, $minirezo, $comite, $forum); 
 	spip_log("2. (create_groupe) retour de find_groupe : $id_groupe... passage a remplacer_groupe", "spipbb_install"); 
 	}
 	
 	return $id_insert; 
} 


function supprimer_mot_groupe($nom_groupe,$nom_mot) { 
 	$id_groupe = id_groupe($nom_groupe); 
 	if ($id_groupe>0) { 
 	$id_mot = id_mot($nom_mot, $id_groupe); 
 	    if ($id_mot>0) { 
 	    	sql_delete("spip_mots", "id_mot=$id_mot"); 
        	sql_delete("spip_mots_articles", "id_mot=$id_mot"); 
 	    	sql_delete("spip_mots_rubriques", "id_mot=$id_mot"); 
        	sql_delete("spip_mots_syndic", "id_mot=$id_mot"); 
        	sql_delete("spip_mots_forum", "id_mot=$id_mot"); 
 	        } 
 	    } 
 	} 
 	
	 
function vider_groupe($nom_groupe) { 
 	$id_groupe = id_groupe($nom_groupe); 
 	   if ($id_groupe>0) { 
 	      $id_mots = sql_select('id_mot',  'spip_mots',  'id_groupe='.sql_quote($id_groupe)); 
 	         while($id_mot = sql_fetch($id_mots)){ 
 	             sql_delete("spip_mots", "id_mot=".$id_mot['id_mot']); 
 	             sql_delete("spip_mots_articles", "id_mot=".$id_mot['id_mot']); 
                 sql_delete("spip_mots_rubriques", "id_mot=".$id_mot['id_mot']); 
                 sql_delete("spip_mots_syndic", "id_mot=".$id_mot['id_mot']); 
 	             sql_delete("spip_mots_forum", "id_mot=".$id_mot['id_mot']); 
 	         } 
 	      sql_delete("spip_groupes_mots", "id_groupe=$id_groupe"); 
       } 
} 

//fonction qui mets à jour un groupe de mots clés 
function remplacer_groupe($titre, $descriptif, $texte, $unseul, $obligatoire, $tables_liees, $minirezo, $comite, $forum) { 
 	$id_groupe = id_groupe($titre); 
 	sql_updateq( 
 	"spip_groupes_mots", array( 
 	     "descriptif" => $descriptif, 
 	     "texte" => $texte, 
 	     "unseul" => $unseul, 
 	     "obligatoire" => $obligatoire, 
 	     "tables_liees" => $tables_liees, 
         "minirezo" => $minirezo, 
 	     "comite" => $comite, 
         "forum" => $forum 
 	     ), "id_groupe=$id_groupe" 
    ); 
    return true; 
} 
 
// fonction qui permet de trouver si un mot clé existe à partir du titre et de l'id du groupe 
function find_mot($titre, $id_groupe) { 
    $titre = addslashes($titre); 
    $count = sql_countsel( 
 	    "spip_mots",  
 	    "titre = '$titre' AND id_groupe = $id_groupe" 
 	
    ); 
    return $count; 
} 

//fonction qui permet de trouver l'id du mot clé à partir du titre et de l'id du groupe 
function id_mot($titre, $id_groupe) { 
 	spip_log("1. (id_mot) debut de recherche de l'id de $titre avec $id_groupe", "spipbb_install"); 
 	$titre = addslashes($titre); 
 	$result = sql_fetsel( 
 	      "id_mot",  
 	      "spip_mots",  
 	      "titre='$titre' AND id_groupe = $id_groupe" 
 	); 
 	$id_mot = $result['id_mot']; 
 	spip_log("2. (id_mot) retour de la fonction id_mot = $id_mot", "spipbb_install"); 
 	return $id_mot; 
} 

//fonction qui permet de créer un mot clé 
function create_mot($groupe, $mot, $descriptif='', $texte='') { 
 	$id_groupe = id_groupe($groupe); 
 	$find_mot = find_mot($mot, $id_groupe); 
 	if ($find_mot == 0) { 
 	     spip_log("1. (create_mot) debut create_mot. mot inexistant donc creation : $id_groupe - $mot", "spipbb_install"); 
         $id_mot = sql_insertq( 
 	        "spip_mots", array( 
            "id_mot" => '', 
 	        "titre" => $mot, 
 	        "descriptif" => $descriptif, 
            "texte" => $texte, 
 	        "id_groupe" => $id_groupe,  
            "type" => $groupe 
 	         ) 
         ); 
 	spip_log("2. (create_mot) mot cle $mot insere sous l'id $id_mot dans la table avec groupe = $id_groupe", "spipbb_install"); 
 	return $id_mot; 
    } 
 	else if ($find_mot > 0) { 
 	     $id_mot = id_mot($mot, $id_groupe); 
 	     spip_log("1. (create_mot) mise a jour dans la table du mot cle : $mot", "spipbb_install"); 
 	     remplacer_mot($id_mot, $descriptif, $texte, $id_groupe, $groupe); 
         return $id_mot; 
 	} 
    else { 
 	spip_log("insertion impossible ! debug : groupe = $groupe --- id_groupe = $id_groupe", "spipbb_install"); 
    } 
} 

//fonction qui permet de mettre à jour un mot clé  
function remplacer_mot($id_mot, $descriptif, $texte, $id_groupe, $groupe) { 
    sql_updateq( 
 	    "spip_mots", array( 
 	            "descriptif" => $descriptif, 
                "texte" => $texte, 
 	            "id_groupe" => $id_groupe, 
                "type" => $groupe 
 	            ), "id_mot=$id_mot" 
 	); 
    return true; 
} 
	 
// fonction qui permet de trouver si une rubrique existe à partir du titre 
function find_rubrique($titre) { 
 	$titre = addslashes($titre); 
 	$count = sql_countsel( 
 	     "spip_rubriques",  
 	     "titre = '$titre'" 
 	); 
    return $count; 
} 
 
//fonction qui permet de trouver l'id d'une rubrique à partir du titre 
function id_rubrique($titre) { 
 	$result = sql_fetsel( 
 	   "id_rubrique",  
 	   "spip_rubriques",  
       "titre='$titre'" 
 	); 
 	$resultat = $result['id_rubrique']; 
 	spip_log("1. (id_rubrique) recherche de l'id_rubrique de $titre = $resultat", "spipbb_install"); 
    return $resultat; 
} 

// fonction qui permet de renommer une rubrique à partir du titre 
function rename_rubrique($titre, $nouveau_titre) { 
 	$id_rubrique = id_rubrique($titre); 
 	if ($id_rubrique) { 
 	        sql_updateq( 
 		         "spip_rubriques", array( 
 	                    "titre" => $nouveau_titre 
 	                     ), "id_rubrique=$id_rubrique" 
                  ); 
    spip_log("rename_rubrique) renommage de $titre en $nouveau_titre", "spipbb_install"); 
} 
	return true; 
} 
 	         
	 
//fonction qui permet de créer une rubrique 
function create_rubrique($titre, $id_parent='0', $descriptif='') { 
 	$id_rubrique = find_rubrique($titre); 
 	if ($id_rubrique == 0) { 
 	       $id_rubrique = sql_insertq( 
 	         "spip_rubriques", array( 
 	         "titre" => $titre, 
 	         "id_parent" => $id_parent, 
 	         "descriptif" => $descriptif 
             ) 
    ); 
    sql_updateq( 
 	       "spip_rubriques", array( 
 	           "id_secteur" => $id_rubrique 
 	       ), "id_rubrique=$id_rubrique" 
 	); 
 	spip_log("1. (create_rubrique) rubrique cree : id = $id_rubrique, titre = $titre", "spipbb_install"); 
 	} 
 	else if ($id_rubrique > 0) { 
 	   $id_rubrique = id_rubrique($titre); 
 	   remplacer_rubrique($id_rubrique, $id_parent, $descriptif); 
 	} 
    return $id_rubrique; 
} 

//fonction qui mets à jour une rubrique 
function remplacer_rubrique($id_rubrique, $id_parent, $descriptif) { 
 	sql_updateq( 
 	    "spip_rubriques", array( 
 	           "id_parent" => $id_parent, 
 	           "descriptif" => $descriptif 
 	     ), "id_rubrique=$id_rubrique" 
 	); 
 	return true; 
} 

?>