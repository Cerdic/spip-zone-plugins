<?php
/**
 * Formulaire pour composer la lettre
 */

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Chargement des valeurs par défaut du formulaire  
 */
function formulaires_editer_malettre_charger_dist(){
  $contexte = array();
  return $contexte;
}

/**
 * Vérification des valeurs du formulaire
 */
function formulaires_editer_malettre_verifier_dist(){
	$erreurs = array();    
	return $erreurs;
}

/**
 * Traitement des valeurs du formulaire
 */
function formulaires_editer_malettre_traiter_dist(){

  include_spip("inc/charsets"); 
  include_spip("inc_presentation");

  // chemin
  $path = _DIR_IMG;
  $path_archive = "lettre";
  $path_archive_full = $path.$path_archive;
  $path_url = lire_meta("adresse_site");
  $path_url_archive = $path_url."/IMG";

  $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__file__))));
  define('_DIR_PLUGIN_MALETTRE',(_DIR_PLUGINS.end($p)));
  $path_plugin = dirname(__file__)."/../";
  
  $errorFlag = false;
  $message = "";
  
                // ancien editer_malettre
	              $lettre_title = trim(strip_tags(_request('lettre_title'))); 
                $lettre_title = str_replace("\"","'", $lettre_title);
                
                $lang = _request('lang_select');
                if ($lang=="")  
                          $lang = $GLOBALS['meta']['langue_site'];
                
                            
      					// VERSION HTML & TXT
      					$sourceHTML = "";
      					$sourceTXT  = "";
      					$selection = ""; // stocke les id des articles retenus separee par une virgule
                
    						    
                // radio button
                $add = _request('add');
                if (is_array($add))   
                    $selection = implode(",", $add);
      							
                // csv articles						
      					$art_csv = _request('art_csv'); 
      					$csv = explode(",", $art_csv);
      					if (is_array($csv)) {							  
      							foreach ($csv as $value2) {								
        					   	$selection .= ",".trim($value2);
        						}
      					}
      		// radio button
                $addeve = _request('addeve');
                if (is_array($addeve))   
                    $selection_eve = implode(",", $addeve);
            
      		// csv evenements						
      					$eve_csv = _request('eve_csv'); 
      					$csv_eve = explode(",", $eve_csv);
      					if (is_array($csv_eve)) {							  
      							foreach ($csv_eve as $value2) {								
        					   	$selection_eve .= ",".trim($value2);
        						}
      					}	
                
                // edito
                include_spip('inc/config');
                $id_article_edito = lire_config("malettre/id_article_edito");
                
                // calcul du patron	
                $flag_preserver = true; // empecher ajout feuille spip_admin.css
               	$sourceHTML .= malettre_get_contents("malettre",$id_article_edito,$selection,$selection_eve,$lang);                 
    						$sourceTXT  .= malettre_get_contents("malettre_txt",$id_article_edito,$selection,$selection_eve,$lang);
                
                //$message = "titre: $lettre_title / sel;  $selection  / sek, $selection_eve / <textarea>$sourceHTML</textarea>"; 
      							
      					// ecriture fichier       											
    						if ($handle = fopen($path_archive_full."/_malettre.html", w)) { 						    
      							fwrite($handle, $sourceHTML);					
      							fclose($handle); 
                    
                    if ($handle = fopen($path_archive_full."/_malettre_txt.html", w)) { 			
        							fwrite($handle, $sourceTXT);					
        							fclose($handle);
      						  } else {
                      $errorFlag = true;
                      $message =  _T('malettre:erreur_ecriture')."($path.$path_archive)";
                    }	                     							
                } else {
                    $errorFlag = true;
                    $message = _T('malettre:erreur_ecriture')."($path.$path_archive)";                    
    						}							
        				

  
  $redirect = "";
  
  // pas d'erreur, on passe à l'étape suivante: choix destinaires
  if (!$errorFlag) {
      //refuser_traiter_formulaire_ajax();
      $redirect = parametre_url(generer_url_ecrire('malettre_envoi'),'lettre_title',$lettre_title );
  }
 
	// message
	return array(
		"editable" => false,
		"message_ok" => "$message",
    'redirect' => $redirect
	);
}

?>