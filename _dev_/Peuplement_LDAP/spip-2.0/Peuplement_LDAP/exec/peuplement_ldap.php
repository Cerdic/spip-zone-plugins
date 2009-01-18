<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/peuplement_ldap_common');
include_spip('inc/auth_ldap');

function exec_peuplement_ldap_dist(){
	//global $couleur_claire;
	global $connect_statut;
	global $couleur_claire;
	global $spip_lang_right;
        // Titre de la page
        gros_titre(_T('peuplementldap:titre_page'),'',false);
        if (_request('peuplement_ldap_etape') == NULL || _request('peuplement_ldap_etape') == 1){
        	genere_etape_1();
        }
        else{
        	if (_request('peuplement_ldap_etape') == 2){
        		genere_etape_2(recherche_ldap(_request('peuplement_ldap_filtre')));
        	}
        	else{
        		$compte_rendu = array();
        		if (_request('peuplement_ldap_btnvaliderSelection') != NULL ){ // Validation de la sélection
					foreach (array_keys($_POST) as $uneCle){
						if (strstr($uneCle,"ajouter_entree_")){
							$ligne = array();
							$info_auteur = explode("#",$_POST[$uneCle]);
							$image = getImage(insere_auteur($info_auteur[0],$info_auteur[1]));
							$ligne[0]=$info_auteur[2];
							$ligne[1]=$info_auteur[1];
							$ligne[2]="<img src=\"../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$image."\" />";
							array_push($compte_rendu,$ligne);
						}
					}
                }
				else{ // Validation du filtre
					$entreesLdap = recherche_ldap( _request('peuplement_ldap_filtre'));
					
					for ($i=0;$i<count($entreesLdap)-1;$i++){
						$ligne = array();
						$image = getImage(insere_auteur($entreesLdap[$i]["dn"],$entreesLdap[$i]["mail"][0]));
						$ligne[0]=$entreesLdap[$i]["cn"][0];
						$ligne[1]=$entreesLdap[$i]["mail"][0];
						$ligne[2]="<img src=\"../"._DIR_PLUGIN_PEUPLEMENTLDAP."/img_pack/".$image."\" />";
						array_push($compte_rendu,$ligne);
					}
				}
        		genere_etape_3($compte_rendu);
        	}
        }
}

?>