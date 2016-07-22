<?php

// Sait-on extraire ce format ?
$GLOBALS['extracteur']['xml_de'] = 'extracteur_xml_de';

function extracteur_xml_de($fichier, &$charset) {
	$charset = 'utf-8';
	if (lire_fichier($fichier, $texte)) {		
		return convertir_extraction_xml_de($texte);
	}
}

function convertir_extraction_xml_de($c) {
	$item = convertir_xml_de($c);
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

	if($item['url'])
		$texte .= "<ins class='url_site'>" . trim($item['url_site']) . "</ins>\n\n" ;

	if($item['auteurs'])
		$texte .= "\n\n@@AUTEUR\n\n" . trim($item['auteurs']) . "\n\n" ;

	if($item['affiliations'])
		$texte .= "\n\n@@SIGNATURE\n\n" . trim($item['affiliations']) . "\n\n" ;

	$texte .=  "\n\n" . trim($item['texte']) . "\n" ;
	
	return $texte ;

} 
 
 
function convertir_xml_de($u) {

	include_spip('inc/charsets');
	include_spip('inc/filtres');

	// debug xml
	// pas de saut de lignes
	$m['xml'] = entites_html(preg_replace("/>\s*</Us",">\n\n\n\n<",$u));
	$m['xml'] = preg_replace("/^\n$/Ums",'',$m['xml']);

	// chopper les metas données
	$metas = extraire_balise($u,'kopf');
	$m['pages'] = extraire_attribut($metas, 'Seite');
	$m['date'] = extraire_attribut($metas, 'Edat');
	$m["url"] = "http://monde-diplomatique.de/artikel/!".  extraire_attribut($metas, 'DatNr') ;

	// pas de puces	
	$u = preg_replace(',&kenkel;\s*,','',$u);
	// espaces non prise par preg
	$u = preg_replace(',&thinsp;,',' ',$u);	

	// passer en utf-8 en nettoyant les entites
	$u = unicode2charset(html2unicode($u)) ;

	$auteur = extraire_attribut($metas, 'Autor');
	$auteur = preg_replace('/^von\s*/Uims','',$auteur);
	if(strlen($auteur) > 1) 
		$m['auteurs'] = $auteur ;

	$m['titre'] = extraire_attribut($metas, 'Titel');

	$u = extraire_balise($u,'red');
	//$u = str_replace("\n",'',$u);

	if( ($ti = trim(textebrut(extraire_balise($u,'Titel')))) != $m['titre'])
		$m['Alertes'][] = "Embrouille sur le titre : $ti >> " . $m['titre'] ;
 
 	// attention il peut y avoir un encadré plus bas avec un second titre.
 	// donc on vire le premier titre mais pas les suivants.
 	
 	preg_match_all("/<Titel>(.*)<\/Titel>/Uums", $u, $titres) ;
 	
 	$titre = array_shift($titres[1]) ;
 	 	
 	// on vire le premier titre
 	$u = str_replace('<Titel>' . $titre . '</Titel>', '', $u);
 	 	 	
 	// on change en gras les suivants
 	foreach($titres[1] as $t)
		$u = str_replace('<Titel>'. $t . '</Titel>', '{{{'.$t.'}}}',$u);
	
	// on on remplace les kast par des quote
	$u = str_replace( "<Kasten>", "<quote>", $u);
	$u = str_replace( "</Kasten>", "</quote>", $u);
	
	
	$m['surtitre'] = trim(textebrut(extraire_balise($u,'Dach')));
	$u = preg_replace('/<Dach>.*<\/Dach>/Us','',$u);

	// Nettoyage
	$u = preg_replace('/<\/?kopf[^>]*>/','',$u);
	$u = preg_replace('/<\/?red>/','',$u);
	$u = preg_replace('/<\/?zInitial>/','',$u);
	
	// copy
	$u = preg_replace('/\(c\)\s*<Kursiv>Le\s*Monde diplomatique,<\/Kursiv>\s*Berlin/Us','',$u);
	//	<Fett>Fußnoten:</Fett><br/>
	// <Fett>Fußnoten: <br/></Fett>
	//	<Fett>Fußnoten:<br/></Fett>
	$u = preg_replace('/<Fett>\s*Fußnote(n)*\s*:*\s*(<br\s*\/>)*\s*<\/Fett>(\s*<br\s*\/>)*/Us','',$u);
	$u = preg_replace("/\(c\) {Le Monde diplomatique,} Berlin/Us","",$u);
		
	// Nettoyer les auteurs qui peuvent être dans le sous-titre
	// On les connait déjà et ils ont parfois des Von
	// <zAutor>von Hubert Prolongeau</zAutor>	
	// <Unterzeile>Von <zAutor>SÉVERINE VATANT </zAutor> *</Unterzeile>
	// <Unterzeile><Kursiv>Währungsunion: Die EU versagt, aber sie merkt es nicht </Kursiv><zAutor>von John Grahl</zAutor></Unterzeile>
	// plusieurs auteurs possible : 1999_12_17/art231.xml
	
	if(preg_match_all("%\s*(Von)?( unserem Korrespondenten)?\s*<zAutor>[^<]+</zAutor>\s*(\*)?(und)?%is",$u,$matches)){ // pas de U a cause 2002_02_15/art067.xml 
		for($i=0 ; $i < sizeof($matches[0]) ; $i++){
			if(preg_match('/\*/',$matches[0][$i],$ast))
				$flag_signature = "Note d'auteur a check" ;
			$m['logs'][] = "Suppression de (auteur chapo) : " . entites_html($matches[0][$i]) ;
			$u = str_replace($matches[0][$i],'',$u);
		}
	}
	
	// nettoyage d'un <Unterzeile> vidé de son auteur	
	// <Unterzeile>  </Unterzeile>
	$u = preg_replace("/<Unterzeile>\s*<\/Unterzeile>/Us","",$u);
	
	// il y a aussi des auteurs dans le texte
	//<Brot>Von</Brot>
	//<Brot>LAKHDAR</Brot>
	//<Brot>BENYOUNES</Brot>
	// 1996_05_10/art299.xml, 1996_07_12/art260.xml
	if(preg_match("%<Brot>Von\s*(unserem)*</Brot>\s*<Brot>[^<]*</Brot>\s*<Brot>[^<]*</Brot>%Uis",$u,$matches)){
		$m['logs'][] = "Suppression (auteur) de : " . entites_html($matches[0]) ;
		$u = str_replace($matches[0],'',$u);
	}

	//<Korrespondent>Von IGNACIO RAMONET</Korrespondent> 2003_01_17/art005.xml
	$u = preg_replace('%<Korrespondent>[^<]*</Korrespondent>%Us','',$u);

	// <Kursiv>	dt. Bodo Schulze</Kursiv>
	// <FettKursiv>Aus dem Englischen von Niels Kadritzke <br/></FettKursiv>
	if(preg_match("%<Kursiv>(\s*dt\..*)</Kursiv>%Us",$u,$matches)
		OR preg_match("%<FettKursiv>(\s*Aus de.*)</FettKursiv>%Us",$u,$matches)
		OR preg_match("%<Kursiv>(\s*deutsch von.*)</Kursiv>%Us",$u,$matches)		
	){
		$m['traducteur'] = trim(textebrut($matches[1])) ;
		$m['traducteur'] = preg_replace("/^\s*dt\.\s*|^\s*deutsch von\s*/Uims","",$matches[1]) ;
		$m['logs'][] = "Suppression de (trad): " . entites_html($matches[0]) ;		
		$u = str_replace($matches[0],'',$u);
	}

	// pas de <br /> dans un <brot> 1996_05_10/art299.xml
	if(preg_match_all("%<Brot>(.*)</Brot>%Us",$u,$matches)){
		for($i=0 ; $i < sizeof($matches[1]) ; $i++){
			$no_br = preg_replace("%<br\s*/>%Us","",$matches[1][$i]) ;
			$u = str_replace($matches[1][$i],$no_br,$u);
		}	
	}

	// y'a t'il une bio dans les notes de bas de pages ?
	// il peut y avoir plusieurs <Fussnote>
	// <Fussnote>(.*)</Fussnote>
	// <Fussnote>* Journalist, Jerusalem.</Fussnote>

	if(preg_match_all("%<Fussnote>(.*)</Fussnote>%Us",$u,$matches)){
		for($i=0 ; $i < sizeof($matches[1]) ; $i++){
			if (!preg_match(',<hoch>,i', $matches[1][$i])
				 //AND !preg_match("%^\d%Uims",$matches[1][$i]) // 2004_11_12/art052.xml
				 AND preg_match("%^\s*\*%Uims",$matches[1][$i]) // 1995_09_15/art299.xml
				){
				//var_dump($matches[1][$i]);
				$note_signature = preg_replace('/^\s*\*\s*/','',$matches[1][$i]) ;
				$m['note_signature'] = $note_signature ;
				$u = str_replace($matches[0][$i],'',$u);
				$flag_signature = false ;	
			}
		}	
	}
	
	// une note signature dans un brot peut-être ? 1996_08_16/art256.xml
	if($flag_signature
		AND preg_match("%^<Brot>\*(.*)</Brot>%Uims",$u,$n)
	){
		$m['note_signature'] = $n[1] ;
		$u = str_replace($matches[0][$i],'',$u);
		$flag_signature = false ;
	}
	
	if($flag_signature)
		$m['Alertes'][] = "Signature non trouvée" ;
		
	$u = preg_replace('/<\/?Fussnote>/Us',"\n\n",$u);

	// sous titre ou chapo
	// <Unterzeile> court = sous titre
	// <Unterzeile> long = chapo
	
	if(preg_match_all("%<Unterzeile>(.*)</Unterzeile>%Us",$u,$matches)){
		for($i=0 ; $i < sizeof($matches[0]) ; $i++){
			//$m['logs'][] = $matches[0][$i] ;		
			if(strlen($matches[1][$i]) < 100){	
				$m['sous-titre'] = trim(textebrut($matches[1][$i])) ;
			}
			if(strlen($matches[1][$i]) >= 100){
				if($m['chapo'])
					$m['chapo'] .= "\n\n" ; // marche pas ? 1997_06_13/art262.xml
				$m['chapo'] .= trim(textebrut($matches[1][$i])) ;
			}	
			$u = str_replace($matches[0][$i],'',$u);
			$m['logs'][] = "Suppression de (soustitre / chapo): " . entites_html($matches[0][$i]) ;		
		}
	}
	
	// Chapo <Vorspann>
	if(preg_match("%<Vorspann>(.*)</Vorspann>%Us",$u,$matches)){
		$m['chapo'] .= trim(textebrut($matches[1])) ;
		$u = str_replace($matches[0],'',$u);
		$m['logs'][] = "Suppression de (chapo): " . entites_html($matches[0]) ;		
			
	}	

	// Chapo <Initial>
	if(preg_match("%<Initial>(.*)</Initial>%Us",$u,$matches)){
		$m['chapo'] .= trim(textebrut($matches[1])) ;
		$u = str_replace($matches[0],'',$u);
		$m['logs'][] = "Suppression de (chapo): " . entites_html($matches[0]) ;		
			
	}	


	// notes avec des espaces dedans...
	// <Hoch>3 </Hoch>
	
	// note de bas de page :
	$u = preg_replace(',^<Hoch>\s*(\d+)\s*</Hoch>\s*,Um',"<br />(\\1) ",$u); //pb d'espace fine ? en fin de hoch 2002_07_12/art002.xml

	
	// notes dans le texte
	$u = preg_replace(',(?:\.|\s)*<Hoch>\s*(\d+)\s*</Hoch>\s*,U'," (\\1) ",$u); // galere sur la note 1 2015_07_09/art00746197.xml
	$u = preg_replace(',^\s+\(,',"(",$u);
	$u = str_replace(') .',").",$u);
	
	// liens
	// <URL href="http://www.un.org/terrorism">www.un.org/terrorism</URL>
	if(preg_match_all("%<URL href=\"(.*)\">\s*(.*)</URL>%Us",$u,$matches)){
		for($i=0 ; $i < sizeof($matches[0]) ; $i++)
			$u = str_replace($matches[0][$i],'[' . $matches[2][$i] .'->' . $matches[1][$i] .']',$u);
	}

	// inters
	$u = str_replace("<Zwischentitel>","\n\n{{{",$u);
	$u = str_replace("</Zwischentitel>","}}}\n\n",$u);	

	// sauts de ligne
	$u = preg_replace('/<br\s*\/?>/',"\n\n",$u);
		
	//paragraphes
	$u = preg_replace('/<\/?Brot>/',"\n\n",$u);
	$u = str_replace("\n\n\n\n","\n\n",$u);

	// Citations
	// <Zitat>
	$u = str_replace("<Zitat>","<quote>",$u);
	$u = str_replace("</Zitat>","</quote>",$u);
 	
	// texte spip
	
	// itals
	$u = str_replace("<Kursiv>","{",$u);
	$u = str_replace("</Kursiv>","}",$u);	

	// gras
	$u = str_replace("<Fett>","{{",$u);
	$u = str_replace("</Fett>","}}",$u);	
	
	
	// images des pages.
	$images_balises = extraire_balises($u,"PdfFile");
	foreach($images_balises as $imageb){
		$m['images_pages'][] = preg_replace("/\.pdf$/",".jpg",textebrut($imageb));
		$u = str_replace($imageb,"",$u);	
	}

	$u = trim($u);
	$m['texte'] = $u ;


	return $m ;
}

