<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function correction_liens_internes_pre_edition($flux){
    if ($flux['args']['action'] == "modifier"){
        foreach ($flux['data'] as $champ => $valeur){
            $flux['data'][$champ] = correction_liens_internes_correction($valeur);
            }
        }
    return $flux;
    }

function correction_liens_internes_correction($texte){
    $url_site = url_de_base();
    //$f=generer_url_entite("article2.html",$public=True);
    // on repère les mauvaises urls
    $match=array(); 
    preg_match_all("#\[(.*)->($url_site(.*))\]#U",$texte,$match,PREG_SET_ORDER); 
    include_spip("inc/urls");
    foreach ($match as $lien){
        $mauvais_raccourci = $lien[0];
        $mauvaise_url = $lien[2];
        $composants_url =urls_decoder_url($mauvaise_url);
        
        if ($composants_url[0]){
            $objet      = $composants_url[1]["type"];
            $id_objet   = $composants_url[1]["id_$objet"];
            $bonne_url  = $objet.$id_objet;
            $bon_raccourci = str_replace($mauvaise_url,$bonne_url,$mauvais_raccourci);
            $texte = str_replace($mauvais_raccourci,$bon_raccourci,$texte);
            }
        }
    
    return $texte;
    }
?>