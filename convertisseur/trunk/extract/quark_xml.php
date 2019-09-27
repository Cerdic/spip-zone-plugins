<?php

include_spip("inc/filtres");

// Sait-on extraire ce format ?
$GLOBALS['extracteur']['quark_xml'] = 'extracteur_quark_xml';

function extracteur_quark_xml($fichier, &$charset) {
	$charset = 'utf-8';
	if (lire_fichier($fichier, $texte)) {
		return convertir_extraction_quark_xml($texte);
	}
}

function convertir_extraction_quark_xml($c) {
	$item = convertir_quark_xml($c);
	$texte = extracteur_preparer_insertion($item);
	return $texte ;
}

function convertir_quark_xml($c) {

	// surcharge nettoyage perso ?
	if(find_in_path('convertisseur_perso.php'))
		include_spip("convertisseur_perso");

	// nettoyer le fichier
	$u = remove_utf8_bom($c) ;

	// Pages
	$b = extraire_balise($u, 'folio') ;
	$pages = textebrut($b);

	$p = explode("-", $pages) ;

	foreach($p as &$v)
		if(intval($v) < 10)
			$v = "0" . $v ;

	$item["pages"] = join(" ", $p) ;

	$mise_en_page = extraire_balise($u, "RELATIONINFO");
	$mise_en_page = extraire_attribut($mise_en_page, "parentAssetName");
	$item["mise_en_page"] = $mise_en_page ;


	// L'article et son illustration sont dans des <spread>
	// Il peut y avoir plusieurs illustrations dans un même spread
	$sequences = extraire_balises($u, "SPREAD") ;
	foreach($sequences as $s){
		// est-on dans une illustration ?
		if(extraire_balise($s , 'PICTURE')){
			$images = array();
			// on cherche dans plusieurs box des images des legendes et des crédits de meme nom
			// <ID NAME="USA_Img_2" UID="2817"/>
			foreach(extraire_balises($s, "BOX") as $box_content){
				$id = $src = $txt = "" ;
				$id = extraire_attribut(extraire_balise($box_content, "ID"),"NAME");

				$details = explode("_", $id);

				$id_article = $details[0];
				$type_balise = $details[1];
				$numero_balise = $details[2];

				$image = extraire_balise($box_content, "CONTENT");
				//var_dump($image);
				$src = textebrut($image) ;
				$src = array_pop(explode("/", $src));

				$paragraphes = extraire_balises($box_content, "PARAGRAPH") ;

				foreach($paragraphes as $p){

					$paragraphe = extraire_balise($p, "PARAGRAPH");
					$type = extraire_attribut($paragraphe, "PARASTYLE");

					// nettoyer un peu
					$texte = textebrut(nettoyer_xml_quark($paragraphe));

					$tech["styles"][$type] = 1 ;

					// Légende
					if(preg_match("/Légende|crédit/ui", $type)){
						$txt .= $texte ;
						continue ;
					}
				}

				if($src)
					$images[$id_article][$numero_balise]["source"] = $src ;
				if($type_balise == "Legende")
					$images[$id_article][$numero_balise]["legende"] = $txt ;
				if($type_balise == "Credit")
					$images[$id_article][$numero_balise]["credit"] = $txt ;
			}

			//var_dump($item["images"]);

			foreach($images as $art)
				foreach($art as $image)
					$item["images"] .= "\nSource : " . $image['source'] . " \n Légende : " . $image['legende'] . "\n Crédit : " . $image['credit'] . "\n\n" ;

			//$item["texte"] .= "//// IMAGE $src // \n <img src='$src' /> \n $legende\n $credit \n\n" ;


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
				$tech["styles"][$type] = 1 ;

				// On cherche dans le nom des feuilles de style Quark des noms de champs spip

				// Surtitre
				if(preg_match("/surtitre/i", $type)){
					if(sizeof($item["surtitre"]) > 0 and !preg_match("/^\s/", $texte) and !preg_match("/\s$/", $item["surtitre"]))
						$texte = " " . $texte ;
					$item["surtitre"] .= $texte ;
					continue ;
				}

				// Titre (mais pas surtitre)
				if(preg_match("/(?:(?<!sur)titre)/i", $type)){
					# plusieurs titres ?? On envoie dans le texte les suivants
					if(sizeof($item["titre"]) > 0 and !preg_match("/^\s/", $texte) and !preg_match("/\s$/", $item["titre"])){
						$item["texte"] .= "@TITRE\n$texte\n\n" ;
						$texte = " // " . $texte ;
					}

					$item["titre"] .= $texte ;
					continue ;
				}

				// Eventuels traitements perso
				if (function_exists('nettoyer_paragraphe')){
					$res = nettoyer_paragraphe($type, $texte, $item);
					if($res){
						$item = $res ;
						continue ;
					}
				}

				// Chapo
				if(preg_match("/chapo/i", $type)){
					$item["chapo"] .= $texte ;
					continue ;
				}

				// Inters
				if(preg_match("/(accroche|-inter|-exergue|Article_Inter)/i", $type)){
					$item["texte"] .= "\n\n" . '{{{' . "$texte" . '}}}' ."\n\n" ;
					continue ;
				}

				// Notes de bas de page
				if(preg_match("/notes/i", $type)){
					$item["notes"] .= $texte ."\n\n" ;
					continue ;
				}

				// Auteurs
				if(preg_match("/auteur/i", $type)){
					$item["auteurs"] .= $texte ;
					continue ;
				}

				// Cas général
				$item["texte"] .= "$texte\n\n" ;

			}

		}
	}

	// ajouter les notes

	if($item["notes"]){
		$item["texte"] = $item["texte"] . "[[<>\n" . trim($item["notes"]) ."\n]]" . "\n" ;
		unset($item["notes"]) ;
	}

	$item["auteurs"] = preg_replace("/\.\s*$/","",$item["auteurs"]);
	$item["auteurs"] = preg_replace("/^Par\s/i","",$item["auteurs"]);

	// passer la main pour une surcharge éventuelle
	$c = $item ;

	if (function_exists('nettoyer_conversion')){
		$item = nettoyer_conversion($item);
	}

	// var_dump($item["auteurs_tete"],"<hr>");
	// var_dump($item["auteurs"]);

	//$item["textebrut"] = textebrut($u);
	//$item["xml"] = htmlspecialchars($u) ;

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
		// essayer aussi de choper un italique non conventionnel dans la police
		if(!$ital_statut){
			$font = extraire_attribut($b, "FONT");
			if(preg_match("/.*Italic.*/i",$font)){
				$ital_statut = "true" ;
				//var_dump($b);
			}
		}
		
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
	$texte = preg_replace(",&(amp;)?flexSpace;,", "~", $texte);
	$texte = preg_replace(",&(amp;)?sixPerEmSpace;,", "~", $texte);
	$texte = preg_replace(",&(amp;)?punctSpace;,", "~", $texte);
	$texte = preg_replace(",&(amp;)?thinsp;,", "~", $texte);
	$texte = preg_replace(",&(amp;)?nbsp;,", "~", $texte);
	$texte = str_replace("&nbsp;", "~", $texte);

	// cesures
	$texte = preg_replace(",&(amp;)?discHyphen;,", "", $texte);
	// autre
	$texte = preg_replace(",&(amp;)?ndash;,", "—", $texte);
	$texte = preg_replace(",&(amp;)?softReturn;,", " ", $texte); // ou bien par "" ? commence-&softReturn;t-on

	$texte = str_replace(" &amp; ", " & ", $texte);

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
