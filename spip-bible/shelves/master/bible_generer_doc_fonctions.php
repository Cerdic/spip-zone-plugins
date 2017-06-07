<?php
include_spip('inc/bible_tableau');
function bible_generer_doc(){
     $langs = bible_tableau('langues');
     $original = bible_tableau('original');
     $texte  = bible_generer_doc_liens($langs,$original);
     $texte .= '{{{"Textes Originaux"}}}[orig<-]
<br><br>
Les versions en langues originales ont une syntaxe particulière. En effet, il faut mettre l\'abréviation du passage dans la langue de l\'article[[On peut éventuellement passer un paramètre "lang" au modèle pour forcer le choix]]. ';
    foreach ($original as $lang=>$sens){   
         $texte .= bible_generer_doc_lang($lang,true);
    }
     
     
     foreach ($langs as $lang){
        if (!$original[$lang]){
             $texte .= '<br /><br />{{{'.traduire_nom_langue($lang)."}}}[$lang<-]<br /><br />";     
             $texte .= bible_generer_doc_lang($lang);
        }
    }
    $alias = bible_tableau('alias');
    $texte .= '<br><br>{{{Alias possible}}}[alias<-]<br><br>';
    foreach($alias as $lalias=>$lesalias){
         $lesalias = implode($lesalias['options'],'<br>-');
         $texte.= '<br><br>{{'.$lalias.': }}<br>-'.$lesalias ;
    }
    
    return $texte;
        
}
function bible_generer_doc_liens($langs,$original){
     $texte = "-[Versions originales->#orig]";
     foreach ($langs as $lang){
             if(!$original[$lang]){
                 $texte.="<br>-[".traduire_nom_langue($lang)."->#$lang]";
             }
     } 
     $texte .= "<br>-[Consulter aussi les alias->#alias]";
     return $texte;
}

function bible_generer_doc_lang($lang,$original=false){
	$tableau_traduction = bible_tableau('traduction');
	$tableau_separateur = bible_tableau('separateur');
	$tableau_livres = bible_tableau('livres');
	if (!$original){
	    $texte = "{{Séparateur chapitre/verset}} : «".$tableau_separateur[$lang]."»";
	    $texte.="<br /><br />{{Abréviations des livres}}<br /><br/>";
	}
	foreach ($tableau_livres as $lang_livre=>$tableau){
		if ($lang == $lang_livre){
			foreach ($tableau as $abrev=>$livre){
				$texte.='|'.$abrev.'|'.$livre.'|<br/>';
			}	
		}
		
	}
	$texte .= '<br/>';
	
	foreach ($tableau_traduction as $abrev=>$traduction){
		if ($traduction['lang']==$lang){
			$texte .= '<br/>';
			
			
			$gateway = $traduction['gateway'];
			$wissen  = $traduction['wissen'];
			$unbound = $traduction['unbound'];
			$at = $traduction['at'];
			$nt = $traduction['nt'];
			$domaine_public = $traduction['domaine_public'];
			$deutero = $traduction['deutero'];
			$lire = $traduction['lire'];
			
			if ($gateway){
			    $texte .= '{{'.$traduction['traduction'].'}}<br />-';
			}
			else{
			    $texte .= '{{'.$traduction['traduction'].'}}<br />-';
			}
			if ($gateway){
				$url = "http://www.biblegateway.com/versions/index.php?action=getVersionInfo&vid=".$gateway;
				
			}
			else if ($wissen){
				$url = "http://www.bibelwissenschaft.de/online-bibeln/".$wissen;
			
			}
			
			else if ($unbound){
				$url = "http://www.unboundbible.org/";
			
			}
			
			else if($lire){
				$url = "http://lire.la-bible.net";
			
			}
			else {
				$url= $traduction['$url'];
			
			}
			
			$texte.= ' {source} : '.$url;
			$texte.='<br />- {valeur du paramètre traduction} : «'.$abrev.'»'; 		
			$at == true ? $texte.='<br />- {Ancien Testament} : oui ' : $texte.='<br />- {Ancien Testament} : non ';
			$deutero == true ? $texte.='<br />- {Deutérocanoniques} : oui ' : $texte.='<br />- {Deutérocanoniques} : non ';
			$nt == true ? $texte.='<br />- {Nouveau Testament} : oui' : $texte.='<br />- {Nouveau Testament} : non';
			$domaine_public == true ? $texte.= '<br />- {Domaine Public} : oui <br  />' :  $texte.= '<br />- {Domaine Public} : non <br  />' ;
		}
	
	
	}
	


	return $texte;
}	
?>