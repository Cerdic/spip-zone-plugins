<?php

function convertir_extraction_quark_xml($c) {
	$item = convertir_quark_xml($c);
	$texte = "" ;
	
	if($item['surtitre'])
		$texte .= "<ins class='surtitre'>" . $item['surtitre'] . "</ins>\n\n" ;

	if($item['titre'])
		$texte .= "<ins class='titre'>" . $item['titre'] . "</ins>\n\n" ;
	
	if($item['chapo'])
		$texte .= "<ins class='chapo'>" . $item['chapo'] . "</ins>\n\n" ;

	if($item['auteurs'])
		$texte .= "\n\n@@AUTEUR\n\n" . $item['auteurs'] . "\n\n";

	if($item['signature'])
		$texte .= "\n\n@@SIGNATURE\n\n" . $item['signature'] . "\n\n";

	$texte .=  "\n\n" . $item['texte']	;
	
	return $texte ;
}
 
function convertir_quark_xml($c) {

	// nettoyer le fichier
	$u = remove_utf8_bom($c) ;
	
	// Pages
	$b = extraire_balise($u, 'folio') ;
	$item["pages"] = textebrut($b);
	
	$mise_en_page = extraire_balise($u, "RELATIONINFO");
	$mise_en_page = extraire_attribut($mise_en_page, "parentAssetName");
	$item["mise_en_page"] = $mise_en_page ;
	
	
	// type d'article
	if(preg_match("/ENCADRÉ-Titre/i", $u)){
		$item["type"] = "Encadré" ;
	}elseif(preg_match("/LIVRES/i", $fichier)){
		$item["type"] = "Note de lecture" ;
	}

	// type d'article
	if(preg_match("/DOSSIER/i", $fichier)){
		$item["dossier"] = str_replace(".qxp", "", $item["mise_en_page"]) ;
	}

	// type d'article
	
	
	

	// L'article et son illustration son dans des <spread>
	$sequences = extraire_balises($u, "SPREAD") ;
	foreach($sequences as $s){
		// est-on dans une illustration ?
		if(extraire_balise($s , 'PICTURE')){
			$image = extraire_balise($s, "CONTENT");
			//var_dump($image);
			$src = textebrut($image) ;
			$src = array_pop(explode(":", $src));

			$paragraphes = extraire_balises($s, "PARAGRAPH") ;
		
			foreach($paragraphes as $p){
							
				$paragraphe = extraire_balise($p, "PARAGRAPH");
				$type = extraire_attribut($paragraphe, "PARASTYLE");
				$texte = textebrut($paragraphe);
				$item["styles"][$type] = 1 ;
				
				// Légende
				if(preg_match("/Légende-Photo/", $type)){
					$legende = $texte ;
					continue ;
				}

				// Crédit
				if(preg_match("/Crédit-photo/", $type)){
					$credit = $texte ;
					continue ;
				}
			}
			
			$item["images"][] = array('src' => $src, 'legende' => $legende, 'credits' => $credit) ;
							
			$item["texte"] .= "// IMAGE $src // \n <img src='$src' /> \n $legende\n $credit \n\n" ;


		}else{
			// On est dans du texte	
			// Parcourir les paragraphes en séparant les éléments trouvés selon leur feuille de style.
			// Titre // Auteurs // chapo // notes // signature // Paragraphes
						
			$paragraphes = extraire_balises($s, "PARAGRAPH") ;
			
			foreach($paragraphes as $p){
				
				$paragraphe = extraire_balise($p, "PARAGRAPH");
				$type = extraire_attribut($paragraphe, "PARASTYLE");
				
				//var_dump("<pre>",htmlspecialchars($paragraphe));
				
				$texte = nettoyer_xml_quark($paragraphe);
				
				$item["styles"][$type] = 1 ;
				

				// Surtitre
				if(preg_match("/-Surtitre$/", $type)){
					$item["surtitre"] .= $texte ;
					continue ;
				}
		
				// Titre 
				if(preg_match("/titre$/i", $type)){
					$item["titre"] .= $texte ;
					continue ;
				}


				// Titre // auteur NDL
				if(preg_match("/NDL-Œuvre$/i", $type)){
					
					list($titre,$ndl_auteur) = explode(" — ", $texte);
					
					$item["titre"] .= $titre ;
					$item["soustitre"] .= $ndl_auteur ;
					
					continue ;
				}

				// Note biblio NDL
				if(preg_match("/NDL-Biblio$/i", $type)){
					$item["texte"] .= "[<>" . $texte . "]\n\n" ;
					continue ;
				}
				
				// Chapo
				if(preg_match("/chapo/i", $type)){
					$item["chapo"] .= $texte ;
					continue ;
				}
		
				// Auteurs
				if(preg_match("/SIGNATURE$/i", $type)){
					$item["auteurs"] .= $texte ;
					continue ;
				}
				
				if(preg_match("/^SIGNATURE PIED$/", $type)){
					$item["auteurs"] .= $texte ;
					continue ;
				}


				// Par notre envoyé spécial...
				if(preg_match("/^SIGNATURE-/", $type)){
					$item["auteurs_tete"] .= trim($texte) ;
					continue ;
				}
												
				// TIMES-Note auteur
				if(preg_match("/-Note auteur$/", $type)){
					$texte = preg_replace("/^\s*\*\s*/","",$texte);
					$item["signature"] .= $texte ;
					continue ;
				}
								
				// Inters
				if(preg_match("/-Accroche$/", $type)){
					$item["texte"] .= "\n\n" . '{{' . "$texte" . '}}' ."\n\n" ;
					continue ;
				}
				
				// chopper des balises text ital puis iterer
				//$ital = extraire_attribut($paragraphe, "ITALIC");
				//$item["texte"] .= $ital_ouvrant . trim($texte) . $ital_fermant . "\n\n" ;
				
				$item["texte"] .= "$texte\n\n" ;
				
			}
			
			// Révisions post concatenation des paragrahes
			// Lettrine avec ital ex : //«~J{e ne suis pas
			
			$item["texte"] = preg_replace("/^([«\s~]*\w)\{/","{\\1", $item["texte"]);
		
		
		}
	}
	
	// s'assurer qu'on a bien un auteur.
	if(!$item["auteurs"]){
		$auteurs = preg_replace("/^\s*(P|p)ar\s*/","", $item["auteurs_tete"]);
		$auteurs = preg_replace("/(\s|\*|~)+$/","",$auteurs);
		$item["auteurs"] = $auteurs ;
	}	
	$item["auteurs"] = preg_replace("/\.\s*$/","",$item["auteurs"]);
	
	$item["textebrut"] = textebrut($u);
		
	$item["xml"] = htmlspecialchars($u) ;

	//$item["xml"] = $u ;

	return $item ;
}

function extracteur_quark_xml($fichier, &$charset) {
	if (lire_fichier($fichier, $texte)) {		
		return convertir_extraction_quark_xml($texte);
	}
}

// Sait-on extraire ce format ?
$GLOBALS['extracteur']['quark_xml'] = 'extracteur_quark_xml';


// Fonctions spécialisées

function remove_utf8_bom($text){
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}
