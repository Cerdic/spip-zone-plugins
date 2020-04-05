<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_corriger_liens_internes_dist(){
	
}

/*
	Remplacer un art123 par art456 si
	id_article=456 et id_source=123
*/

function convertisseur_corriger_liens_internes($id_article,$id_secteur,$champ="texte"){
	include_spip("base/abstract_sql");
	
	$texte = sql_getfetsel($champ, "spip_articles", "id_article=$id_article") ;
	
	// recaler des liens [->123456] ?
	include_spip("inc/lien");
	if(preg_match_all(_RACCOURCI_LIEN, $texte, $liens, PREG_SET_ORDER)){
		foreach($liens as $l){
			if(preg_match("/^(?:art)?([0-9]+)$/", $l[4],$m)){
				$id_source = $m[1];
				// trouver l'article dont l'id_source est $l[4] dans le secteur
				if($id_dest = sql_getfetsel("id_article", "spip_articles", "id_source=$id_source and id_secteur=$id_secteur")){
					$lien_actuel = $l[0] ;
					
					$lien_corrige = str_replace($l[4], $id_dest, $l[0]) ;
					//var_dump("$id_article ($champ) : $lien_actuel => $lien_corrige");
					spip_log("$id_article ($champ) : $lien_actuel => $lien_corrige","correction_liens_internes.4");
					
					// maj le texte
					$texte_corrige = str_replace($lien_actuel, $lien_corrige, $texte);
					sql_update("spip_articles", array($champ => sql_quote($texte_corrige)), "id_article=$id_article");
					
					// attention s'il y a plusieurs liens
					$texte = $texte_corrige ;
				}else{
					// $commande = escapeshellarg("Dans $id_article (source $id_source)" . $l[0] . " : lien vers " . $l[4] . " non trouvé") ;
					spip_log("$id_article ($champ) : dans " . $l[0] . ", lien vers " . $id_source . " non trouvé","correction_liens_internes_ko.4");
				}
			}
		}
	}
}
