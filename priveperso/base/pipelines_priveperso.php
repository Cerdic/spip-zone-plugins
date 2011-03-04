<?php

function priveperso_init_tables_principales($tables_principales){


        $priveperso_texte = array(
               "rub_id" => "BIGINT(21) NOT NULL",
               //articles
               "icone_modifier_article" => "TEXT DEFAULT '' NOT NULL",
               "texte_modifier_article" => "TEXT DEFAULT '' NOT NULL",
					"icone_ecrire_article" => "TEXT DEFAULT '' NOT NULL",
					"texte_date_publication_anterieure" => "TEXT DEFAULT '' NOT NULL",
					"info_nouvel_article" => "TEXT DEFAULT '' NOT NULL",
					"texte_sur_titre" => "TEXT DEFAULT '' NOT NULL",
					"info_titre" => "TEXT DEFAULT '' NOT NULL",
					"texte_sous_titre" => "TEXT DEFAULT '' NOT NULL",
					"texte_descriptif_rapide" => "TEXT DEFAULT '' NOT NULL",
					"info_chapeau" => "TEXT DEFAULT '' NOT NULL",
					"entree_liens_sites" => "TEXT DEFAULT '' NOT NULL",
					"info_titre_url" => "TEXT DEFAULT '' NOT NULL",
					"info_url" => "TEXT DEFAULT '' NOT NULL",
					"info_texte" => "TEXT DEFAULT '' NOT NULL",
					"info_post_scriptum" => "TEXT DEFAULT '' NOT NULL",
					//rubriques
					"icone_creer_rubrique" => "TEXT DEFAULT '' NOT NULL",
					"icone_creer_sous_rubrique" => "TEXT DEFAULT '' NOT NULL",
					"icone_modifier_rubrique" => "TEXT DEFAULT '' NOT NULL",
					"info_modifier_rubrique" => "TEXT DEFAULT '' NOT NULL",
					"titre_nouvelle_rubrique" => "TEXT DEFAULT '' NOT NULL",
					"info_titre_rubriques" => "TEXT DEFAULT '' NOT NULL",
					"texte_descriptif_rapide_rubriques" => "TEXT DEFAULT '' NOT NULL",
					"entree_contenu_rubrique" => "TEXT DEFAULT '' NOT NULL",
					"info_texte_explicatif" => "TEXT DEFAULT '' NOT NULL",
					//sites
					//
					//breves
					"icone_nouvelle_breve" => "TEXT DEFAULT '' NOT NULL",
					"icone_modifier_breve" => "TEXT DEFAULT '' NOT NULL",
					"info_modifier_breve" => "TEXT DEFAULT '' NOT NULL",
					"titre_nouvelle_breve" => "TEXT DEFAULT '' NOT NULL",
					"info_titre_breves" => "TEXT DEFAULT '' NOT NULL",
					"entree_texte_breve" => "TEXT DEFAULT '' NOT NULL",
					"entree_liens_sites_breves" => "TEXT DEFAULT '' NOT NULL",
					"info_titre_url_breves" => "TEXT DEFAULT '' NOT NULL",
					"info_url_breves" => "TEXT DEFAULT '' NOT NULL"
	
        );

        $priveperso_texte_cles = array(
        			//"INDEX" => "rub_id",
               "PRIMARY KEY" => "rub_id"
        );
	
        $priveperso = array(
               "rub_id" => "BIGINT(21) NOT NULL",
               "sousrub" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
               "textperso" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
               "articles_surtitre" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
               "articles_soustitre" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
					"articles_descriptif" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
					"articles_texte" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
					"articles_chapeau" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
					"articles_ps" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
					"articles_redac" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
					"articles_urlref" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
					"rubriques_descriptif" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
					"rubriques_texte" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
					"activer_breves" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
					"activer_sites" => "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'"
        );

        $priveperso_cles = array(
        			//"INDEX" => "rub_id",
               "PRIMARY KEY" => "rub_id"
        );
        

        $tables_principales['spip_priveperso_texte'] = array(
                'field' => &$priveperso_texte,
                'key' => &$priveperso_texte_cles
        );

        $tables_principales['spip_priveperso'] = array(
                'field' => &$priveperso,
                'key' => &$priveperso_cles
        );
       
        return $tables_principales;
}	
function priveperso_init_tables_interfaces($tables_interfaces){

			$tables_interfaces['table_des_tables']['PRIVEPERSO'] = 'priveperso';

			return $tables_interfaces;

}	

function priveperso_exec_init(){

	include_spip('inc/inscrire_priveperso');

	$id_rubrique = priveperso_recupere_id_rubrique();
	if ($id_rubrique){

// On vérifie si la rubrique en cours ou une des rubriques parentes est personnalisée
		if (!priveperso_rubrique_deja_perso($id_rubrique)){
			$id_rub = priveperso_trouver_rubrique_parent_perso($id_rubrique);
			if (($id_rub!==NULL) && ($id_rub!=='0')) $id_rubrique = $id_rub;
		}


		if (priveperso_rubrique_deja_perso($id_rubrique)){
			$priveperso = priveperso_recuperer_valeurs($id_rubrique);
			foreach($GLOBALS['meta'] as $i => $v) {
				if (($x = $priveperso[$i])!==NULL) $GLOBALS['meta'][$i] = $priveperso[$i];
			}
		}
	}          
          
   return;
}

	
?> 