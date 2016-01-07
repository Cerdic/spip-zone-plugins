<?php

include_spip("inc/filtres");

// Sait-on extraire ce format ?
$GLOBALS['extracteur']['quark_xml'] = 'extracteur_quark_xml';

function extracteur_quark_xml($fichier) {
	if (lire_fichier($fichier, $texte)) {		
		return convertir_extraction_quark_xml($texte);
	}
}

function convertir_extraction_quark_xml($c) {
	$item = convertir_quark_xml($c);
	$texte = extracteur_preparer_insertion($item);
	return $texte ;
}
 
function extracteur_preparer_insertion($item){
	
	$texte = "" ;
	
	if($item['surtitre'])
		$texte .= "<ins class='surtitre'>" . trim($item['surtitre']) . "</ins>\n\n" ;

	if($item['titre'])
		$texte .= "<ins class='titre'>" . trim($item['titre']) . "</ins>\n\n" ;
	
	if($item['chapo'])
		$texte .= "<ins class='chapo'>" . trim($item['chapo']) . "</ins>\n\n" ;

	if($item['auteurs'])
		$texte .= "\n\n@@AUTEUR\n\n" . trim($item['auteurs']) . "\n\n" ;

	if($item['signature'])
		$texte .= "\n\n@@SIGNATURE\n\n" . trim($item['signature']) . "\n\n" ;

	$texte .=  "\n\n" . trim($item['texte']) . "\n" ;
	
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

	// L'article et son illustration sont dans des <spread>
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
				
				// nettoyer un peu
				$texte = nettoyer_xml_quark($paragraphe);
				
				if($texte == "")
					continue ;
					
				// init des styles
				$item["styles"][$type] = 1 ;

				// inserer des traitements perso, dans mes_fonctions
				// NDL, coupures, etc avec styles hors spip de base.
				// if(function_exists(convertion_paragraphes_quark_xml_perso()))

				// Titre // auteur NDL
				if(preg_match("/NDL-Œuvre$/i", $type)){
					
					list($titre,$ndl_auteur) = explode(" — ", $texte);
					
					$item["titre"] .= $titre ;
					$item["soustitre"] .= $ndl_auteur ;
					
					continue ;
				}

				// TIMES-Note auteur
				if(preg_match("/-Note auteur$/", $type)){
					$texte = preg_replace("/^\s*\*\s*/","",$texte);
					$item["signature"] .= $texte ;
					continue ;
				}

				// Note biblio NDL
				if(preg_match("/NDL-Biblio$/i", $type)){
					$item["texte"] .= "[<>" . $texte . "]\n\n" ;
					continue ;
				}

				// Par notre envoyé spécial...
				if(preg_match("/^SIGNATURE-/", $type)){
					$item["auteurs_tete"] .= trim($texte) ;
					continue ;
				}

				// ne garder que ici que des titres, chapo, texte, etc, generique.

				// On cherche dans le nom des feuilles de style Quark des noms de champs spip

				// Surtitre
				if(preg_match("/surtitre/i", $type)){
					if(sizeof($item["surtitre"]) > 0 and !preg_match("/^\s/", $texte) and !preg_match("/\s$/", $item["surtitre"]))
						$texte = " " . $texte ;
					$item["surtitre"] .= $texte ;
					continue ;
				}
		
				// Titre 
				if(preg_match("/titre/i", $type)){
					if(sizeof($item["titre"]) > 0 and !preg_match("/^\s/", $texte) and !preg_match("/\s$/", $item["titre"]))
						$texte = " " . $texte ;
					$item["titre"] .= $texte ;
					continue ;
				}
				
				// Chapo
				if(preg_match("/chapo/i", $type)){
					$item["chapo"] .= $texte ;
					continue ;
				}
		
				// Auteurs
				if(preg_match("/signature/i", $type)){
					$item["auteurs"] .= $texte ;
					continue ;
				}
								
				// Inters
				if(preg_match("/accroche/i", $type)){
					$item["texte"] .= "\n\n" . '{{' . "$texte" . '}}' ."\n\n" ;
					continue ;
				}
				
				// Notes de bas de page
				if(preg_match("/notes/i", $type)){
					$item["notes"] .= $texte ."\n" ;
					continue ;
				}
				
				$item["texte"] .= "$texte\n\n" ;
				
			}
		
		}
	}
	
	// s'assurer qu'on a bien un auteur.
	if(!$item["auteurs"]){
		$auteurs = preg_replace("/^\s*(P|p)ar\s*/","", $item["auteurs_tete"]);
		$auteurs = preg_replace("/(\s|\*|~)+$/","",$auteurs);
		$item["auteurs"] = $auteurs ;
	}
	
	// ajouter les notes
	
	if($item["notes"])
		$item["texte"] = $item["texte"] . "[[<>\n" . $item["notes"] ."]]" . "\n" ;
	
	$item["auteurs"] = preg_replace("/\.\s*$/","",$item["auteurs"]);
	
	$item["textebrut"] = textebrut($u);
		
	$item["xml"] = htmlspecialchars($u) ;

	//$item["xml"] = $u ;

	return $item ;
}



// Fonctions spécialisées

function remove_utf8_bom($text){
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}


//
function nettoyer_xml_quark($xml){
	
	$texte = $xml ;
	
	$texte = preg_replace("/\R+/","",$xml);
	$texte = preg_replace("/>\s+</","><",$texte);
	
	// espace insécable en balise vide.
	$texte = preg_replace("/<RICHTEXT MERGE=\"false\" NONBREAKING=\"true\"\/?>(<\/RICHTEXT>)?/ims", "<RICHTEXT MERGE=\"false\" NONBREAKING=\"true\">~</RICHTEXT>", $texte);
	$texte = preg_replace("/<RICHTEXT[^>]+><\/RICHTEXT>/ims", "<RICHTEXT> </RICHTEXT>", $texte);
	
	$ital = false ;
	
	foreach(extraire_balises($texte, "RICHTEXT") as $b){
		
		// itals sur plusieurs balises
		
		$prefixe_ital = "" ;
		$ital_statut = extraire_attribut($b, "ITALIC");
		if(!$ital AND $ital_statut == "true"){
			// début d'un ital
			$prefixe_ital = "{" ;
			$ital = true ;
		}elseif($ital AND $ital_statut == "true"){
			// ital qui continue
			$prefixe_ital = "" ;
			$ital = true ;
		}elseif($ital AND !$ital_statut){
			// fin d'ital
			$prefixe_ital = "}" ;
			$ital = false ;
		}
		
		// gras
		$gras_statut = extraire_attribut($b, "BOLD");
		if($gras_statut == "true"){
			$gras_debut = "{{" ;
			$gras_fin = "}}" ;
		}else{
			$gras_debut = "" ;
			$gras_fin = "" ;
		}
			
		$b = supprimer_tags($b);
		
		$texte_clean .= $prefixe_ital . $gras_debut . $b . $gras_fin  ; 
		//var_dump("<pre>",htmlspecialchars($texte_clean));
	}
	
	// fermer l'ital eventuellement resté ouvert
	if($ital){
		$s = "}" ;
		//var_dump("lol",$b);
	}	
	else
		$s = "";	
	
	$texte = $texte_clean.$s ;
		
	//$texte = supprimer_tags($texte);
		
	// entites inventées
	// espaces fines ou insécables
	$texte = str_replace("&amp;flexSpace;", "~", $texte);
	$texte = str_replace("&amp;sixPerEmSpace;", "~", $texte);
	$texte = str_replace("&amp;punctSpace;", "~", $texte);
	$texte = str_replace("&amp;thinsp;", "~", $texte);
	$texte = str_replace("&amp;nbsp;", "~", $texte);
	$texte = str_replace("&nbsp;", "~", $texte);
	
	// cesures
	$texte = str_replace("&amp;discHyphen;", "", $texte);
	// autre
	$texte = str_replace("&amp;ndash;", "—", $texte);
	$texte = str_replace("&amp;softReturn;", " ", $texte);
	

	// espaces en gras.
	$texte = str_replace(" }}","}} ",$texte);
	$texte = str_replace("{{ "," {{",$texte);

	// espaces en ital.
	$texte = str_replace(" }","} ",$texte);
	$texte = str_replace("{ "," {",$texte);

	
	/*	// espaces en italique ou en romain
	$c = preg_replace(',[{] *~ *[}],', '~', $c);
	$c = preg_replace(',[}] *~ *[{],', '~', $c);
	$c = preg_replace(',[{] +[}],', ' ', $c);
	$c = preg_replace(',[}] +[{],', ' ', $c);
	$c = preg_replace(',([ ~])[}],', '}\1', $c);
	$c = preg_replace(',[{]([ ~]),', '\1{', $c);
	$c = preg_replace(',[ ~]?([{]»),', '{»', $c);
	$c = preg_replace(',[{][}]|[}][{],', '', $c);
	*/	

	//var_dump("<pre>",$texte,"</pre>");	
	
	return trim($texte) ;
}
