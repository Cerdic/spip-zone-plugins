<?php

	/*
		La Fonction Sale()
		(c)2005 James <klike@free.fr>
		d'après le bouton memo et le script spip_unparse
	*/

	function correspondances_standards() {
		return array(
			//Mise en page
			",<(i|em)( [^>\r]*)?".">(.+)</\\1>,Uims" => "{\\3}", //Italique
			",<(b|h[4-6]|strong)( [^>]*)?".">(.+)</\\1>,Uims" => "{{\\3}}", //Gras
			",<(h[1-3])( [^>]*)?".">(.+)</\\1>,Uims" => "\r{{{ \\3 }}}\r", //Intertitre

			//Liens, ancres & notes
			",<a[ \t\n\r][^<>]*href=[^<>]*(http[^<>'\"]*?)[^<>]*>(.*?)<\/a>,Uims" => "[\\2->\\1]", //Lien externe

			//Paragraphes
			",<(p)( [^>]*)?".">(.+)</\\1>,Uims" => "\\3\r\r", //Paragr.
			",(<no p[^>]*>)(\s*)(<\/no p>),Uims" => "", // spiperie
			",(<\/no p>)(.*)(<no p[^>]*>),Uims" => "\\2", // spiperie
			",<br( [^>]*)?".">,Uims" => "\n_ ", //Saut de ligne
			",<hr( [^>]*)?".">,Uims" => "\r----\r", //Saut de page
			",<(pre)( [^>]*)?".">(.+)</\\1>,Uims" => "<poesie>\n\\3\n</poesie>", //Poesie
			
			//typo
			",&nbsp;:,i" => " :", 

			//Images & Documents
		);
	}

function correspondances_a_bas_le_html() {

	return array(
	
	// on ne veut pas des heads / html / body 
	",<head>.*<\/head>,Uims" => "",
	",<html>,Uims" => "",
	",<\/html>,Uims" => "",
	",<body.*>,Uims" => "",
	",<\/body>,Uims" => "",

	// on ne veux pas des tables
	",<table.*>,Uims" => "",
	",<\/table.*>,Uims" => "",
	",<tr.*>,Uims" => "",
	",<\/tr>,Uims" => "",
	",<td.*>,Uims" => "",
	",<\/td>,Uims" => "",


	// on ne veux pas des div
	",<div.*>,Uims" => "",
	",<\/div.*>,Uims" => "",
	
	// divers et variés 
	",<csobj.*>,Uims" => "",
	",<\/csobj>,Uims" => "",
	",<csscriptdict.*>,Uims" => "",
	",<\/csscriptdict>,Uims" => "",
	",<spacer.*>,Uims" => "",
	
	// javascript sur les liens 
	",target=\".*\",Uims" => "",
	",onmouseover=\".*\",Uims" => "",
	",onmouseout=\".*\",Uims" => "",
	",onclick=\".*\",Uims" => "",
	

	// c est pas du html mais je le met ici quand meme
	",\t,Uims" => "",
	

		);
}

	function extraire_listes($texte,$tag,$char){
	  $pattern = "(<$tag"."[^>]*>|</$tag>)";
	
	  preg_match_all (",$pattern,Uims", $texte, $tagMatches, PREG_SET_ORDER);
	  $textMatches = preg_split (",$pattern,Uims", $texte);
	
	  $niveau = 0;
	  $prefixe= "-$char$char$char$char$char$char$char$char$char$char";
	  $texte = $textMatches [0];
	  if (count($textMatches)>1){
		  for ($i = 1; $i < count ($textMatches)-1; $i ++) {
		  	if (preg_match(",<$tag"."[^>]*>,is",$tagMatches [$i-1][0])) $niveau++;
		  	else if (strtolower($tagMatches [$i-1][0])=="</$tag>") $niveau--;
		  	$pre = substr($prefixe,0,$niveau+1);
		  	$lignes = preg_split(",<li[^>]*>,i",$textMatches [$i]);
		  	foreach ($lignes as $key=>$item){
		  		$lignes[$key] = trim(str_replace("</li>","",$item));
		  		if (strlen($lignes[$key]))
		  			$lignes[$key]="$pre " . $lignes[$key];
		  		else 
		  			unset($lignes[$key]);
		  	}
				$texte .= implode("\r",$lignes)."\r";
		  }
		  $texte .= end($textMatches);
	  }
	  return $texte;
	}
	
	function recompose_tableau($texte){
		$table_class=array('spip'=>"|",'ville'=>"£");
		$sep = $table_class['spip'];
		// detecter la classe
		$class = preg_replace(",.*<table[^>]*class=['\"]([^'\"]*)['\"][^>]*>.*?,Uims","\\1",$texte);
		if (isset($table_class[$class]))
			$sep = $table_class[$class];
		
		
		// d'abord transformer tous les | en leur entite pour pas se tromper
		$texte = str_replace($sep,"&#".ord($sep).";",$texte);
		
		// les lignes
		$texte = preg_replace(",<tr[^>]*>\s*?(.*)\s*</tr>,Uims","$sep\\1$sep\r",$texte);
		
		// les colonnes
		$texte = preg_replace(",<(td|th)(\s[^>]*)?>(.*)</\\1>,Uims","\\3$sep",$texte);
		// les doubles pipes induits en fin de ligne
		$texte = str_replace("$sep$sep\r","$sep\r",$texte);
		
		// le thead
		$texte = preg_replace(",<thead[^>]*>\s*?(.*)\s*</thead>,Uims","$sep\\1$sep\r",$texte);
		// le tbody
		$texte = preg_replace(",<tbody[^>]*>\s*?(.*)\s*</tbody>,Uims","\\1",$texte);
		// le table
		$texte = preg_replace(",<table[^>]*>\s*?(.*)\s*</table>,Uims","\\1\r\r",$texte);
		// les lignes vides inter |
		$texte = preg_replace(",\\$sep\r[\s\r]*\\$sep,Uims","$sep\r$sep",$texte);
		
		return $texte;
	}	
	function extraire_tableaux($texte){
		// tableaux
	  $pattern = '<table[^>]*>.*</table>';
	  preg_match_all (",$pattern,Uims", $texte, $tableMatches, PREG_SET_ORDER);
	  $textMatches = preg_split (",$pattern,Uims", $texte);
	
	  foreach ($tableMatches as $key => $value) {
			$tableMatches [$key][0] = recompose_tableau ($tableMatches[$key][0]);
	  }
		for ($i = 0; $i < count ($textMatches); $i ++) {
			$textMatches [$i] = $textMatches [$i] . $tableMatches [$i] [0];
		}
		$texte = implode ("", $textMatches);
		return $texte;
	}

	function spip_avant_sale($contenu) {
		if(function_exists('avant_sale'))
			return avant_sale($contenu);

		// PRETRAITEMENTS
		$contenu = str_replace("\n\r", "\r", $contenu); // echapper au greedyness de preg_replace
		$contenu = str_replace("\n", "\r", $contenu);

		// virer les commentaires html (qui cachent souvent css et jajascript)
		$contenu = preg_replace("/<!--.*-->/Uims", "", $contenu);

		$contenu = preg_replace("/<(script|style)\b.+?<\/\\1>/i", "", $contenu);
		return $contenu;
	}

	function spip_apres_sale($contenu) {
		if(function_exists('apres_sale'))
			return apres_sale($contenu);

		// POST TRAITEMENT
		$contenu = str_replace("\r", "\n", $contenu);
		$contenu = preg_replace(",\n(?=\n\n),","",$contenu);
		
		return $contenu;
	}

	function sale($contenu_sale, $correspondances = '') {
		$contenu_propre = $contenu_sale;
		
		//Pré  Traitement
		$contenu_propre = spip_avant_sale($contenu_propre);
		
		//Traitement
		if(empty($correspondances))
			$correspondances = correspondances_standards();

		foreach($correspondances as $motif => $remplacement)
			$contenu_propre = preg_replace($motif, $remplacement, $contenu_propre);
			
		$contenu_propre = extraire_listes($contenu_propre,"ul","*");
		$contenu_propre = extraire_listes($contenu_propre,"ol","#");
		$contenu_propre = extraire_tableaux($contenu_propre);

		//Post Traitement
		$contenu_propre = spip_apres_sale($contenu_propre);

		foreach(correspondances_a_bas_le_html() as $motif => $remplacement)
			$contenu_propre = preg_replace($motif, $remplacement, $contenu_propre);

		return $contenu_propre;
	}

?>
