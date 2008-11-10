<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/peuplement_ldap_common');
include_spip('inc/auth_ldap.php');
include('../ecrire/inc/auth_ldap.php');


function exec_peuplement_ldap(){
	//global $couleur_claire;
	global $connect_statut;
	global $couleur_claire;
	global $spip_lang_right;
	
	// Entete HTML (fixe la balise Titre)
        debut_page(_T('peuplementldap:titre_page'));
        echo "<br /><br /><br />";
        // Titre de la page
        gros_titre(_T('peuplementldap:titre_page'));
        
        if (_request('peuplement_ldap_etape') == NULL || _request('peuplement_ldap_etape') == 1){
        	genere_etape_1();
        }
        else{
        	if (_request('peuplement_ldap_etape') == 2){
        		genere_etape_2(recherche_ldap(_request('peuplement_ldap_filtre')));
        	}
        	else{
        		$resultat_insertion=array();
        		if (_request('peuplement_ldap_btnvaliderSelection') != NULL ){ // Validation de la sélection
					
					foreach (array_keys($_POST) as $uneCle){
						if (strstr($uneCle,"ajouter_entree_")){
							array_push($resultat,ajoutUnitaire($_POST[$uneCle],$ldapCnx));
							$tmp = explode("#",$_POST[$uneCle]);
							//insere_auteur($tmp[0],$tmp[1]);
						}
					}
                }
                
                
                
                
                
                
                
                
                
                
                
                
				else{ // Validation du filtre
					echo "Validation du filtre";
				}
        		
        		genere_etape_3();
        	}
        }

        
        
        
        
        	
	
	
}

?>