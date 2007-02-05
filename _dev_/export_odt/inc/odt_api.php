<?php
include_spip('inc/odf_api');

// XHTML - Preserver les balises-bloc : on liste ici tous les elements
// dont on souhaite qu'ils provoquent un saut de paragraphe
define('_TAGS_BLOCS',
	'div|pre|ul|ol|li|blockquote|h[1-6r]|'
	.'t(able|[rdh]|body|foot|extarea)|'
	.'form|object|center|marquee|address|'
	.'d[ltd]|script|noscript|map|button|fieldset');
define('_TAGS_INLINE',
	'span|strong|b|em|i|');

// retablir les boucles et autres tags du squelette
function spip2odt_styliser_contenu($odf_dir, $contexte){
	// lire le content
	lire_fichier($odf_dir."content.xml",$texte);
	
	// retablir les boucles
	$texte = preg_replace(",&lt;([/]?B(.*))&gt;,U","<\\1>",$texte);
	
	// retablir les includes
	$texte = preg_replace(",&lt;([/]?INCLU[RD]E(.*))&gt;,U","<\\1>",$texte);
	
	// falsifier l'en tete xml
	$texte = preg_replace(",^<"."[?]xml,","<@XML",$texte);

	// ajouter les directives de cache/charset et mime-type
	$texte = "#"."CACHE{0}
	#"."HTTP_HEADER{Content-type: text/xml; charset=UTF-8}
	$texte";

	// ecrire le squelette et le fichier fonctions associe
	ecrire_fichier(_DIR_TMP."content.html",$texte);
	lire_fichier(_DIR_PLUGIN_SPIPODF."content_fonctions.php",$fonctions);
	ecrire_fichier(_DIR_TMP."content_fonctions.php",$fonctions);
	
	// calculer le fond
	include_spip('inc/assembler');
	$texte = recuperer_fond(_DIR_TMP."content",$contexte);
	
	// nettoyer
	@unlink(_DIR_TMP."content_fonctions.php");
	@unlink(_DIR_TMP."content.html");

	$texte = preg_replace(",^<@XML,","<"."?xml",$texte);

	// convertir les balises html ajoutees par propre en tags xml
	$texte = spip2odt_convertir($texte,$odf_dir);

	ecrire_fichier($odf_dir."content.xml",$texte);
}


function spip2odt_convertir($texte,$dossier){
	#include_spip('inc/xml');
	#$xml = spip_xml_parse($texte);
	#var_dump($xml);
	// on ajoute ici des paragraphe, donc a faire avant reparagraphage
	$texte = spip2odt_convertir_tags_blocs($texte);
	
	// transmettre les paragraphes aux enfants
	$texte = spip2odt_reparagrapher($texte,$dossier);
	
	// ajouter les styles
	$texte = spip2odt_ajouter_styles($texte);
	
	//$texte = str_replace("<br />","\n",$texte);
	$texte = str_replace("&nbsp;"," ",$texte);
	return $texte;
}


// Conversion SPIP->ODF
function spip2odt_convertir_tags_inline($texte){
	// traitement des liens :
	if (preg_match_all(",(<a\s[^>]*>)(.*)</a>,Uims",$texte,$regs,PREG_SET_ORDER)){
		foreach($regs as $reg){
			$href = extraire_attribut($reg[1],'href');
			$texte = str_replace($reg[0],'<text:a xlink:type="simple" xlink:href="'.$href.'">'.$reg[2].'</text:a>',$texte);
		}
	}
	
	$texte = preg_replace(",<("._TAGS_INLINE.")(\s+[^>]*)?>,","<text:span text:style-name='spip_\\1'>",$texte);	
	$texte = preg_replace(",</("._TAGS_INLINE.")>,","</text:span>",$texte);	
	return $texte;
}

function spip2odt_convertir_tags_blocs($texte){
	static $nb_tables_spip = 1;
	// les ul/ol li
	$texte = preg_replace(",<li(\s[^>]*)?>,ims",'<text:list-item>',$texte);
	$texte = preg_replace(",</li>,ims","</text:list-item>",$texte);
	
	$texte = preg_replace(",<ul(\s[^>]*)?>,ims",'<text:list text:style-name="spip_ul">',$texte);
	$texte = preg_replace(",</ul>\s*(<p(\s[^>]*)>),ims","</text:list><text:p/>\\1",$texte);
	$texte = preg_replace(",</ul>,ims","</text:list>",$texte);
	
	$texte = preg_replace(",<ol(\s[^>]*)?>,ims",'<text:list text:style-name="spip_ol">',$texte);
	$texte = preg_replace(",</ol>\s*(<p(\s[^>]*)>),ims","</text:list><text:p/>\\1",$texte);
	$texte = preg_replace(",</ol>,ims","</text:list>",$texte);
	// paragrapher les items de list
	$texte = preg_replace(",(<text:list-item>)(.*)(<[/]?text:list),Uims",'\\1<text:p text:style-name="spip_li">\\2</text:p>\\3',$texte);
	
	
	// les tables
	// <table:table table:name="Tableau1" table:style-name="Tableau1">
	// <table:table-column table:style-name="Tableau1.A" table:number-columns-repeated="3"/>
	// <table:table-row>
	// <table:table-cell table:style-name="Tableau1.A1" office:value-type="string"><text:p text:style-name="Table_20_Contents">Un tableau</text:p></table:table-cell>
	// <table:table-cell table:style-name="Tableau1.A1" office:value-type="string"><text:p text:style-name="Table_20_Contents"/></table:table-cell>
	// <table:table-cell table:style-name="Tableau1.C1" office:value-type="string"><text:p text:style-name="Table_20_Contents"/></table:table-cell>
	// </table:table-row>
	// <table:table-row>
	// <table:table-cell table:style-name="Tableau1.A2" office:value-type="string"><text:p text:style-name="Table_20_Contents"/></table:table-cell>
	// <table:table-cell table:style-name="Tableau1.A2" office:value-type="string"><text:p text:style-name="Table_20_Contents"/></table:table-cell>
	// <table:table-cell table:style-name="Tableau1.C2" office:value-type="string"><text:p text:style-name="Table_20_Contents"/></table:table-cell>
	// </table:table-row></table:table>
	/*while (preg_match(",<table(\s[^>]*)?>,imsS",$texte)){
		$nb_tables_spip++;
		$texte = preg_replace(",<table(\s[^>]*)?>,imsS",
		  '<table:table table:name="Tableau'.$nb_tables_spip.'" table:style-name="table_spip">'
		  . '<table:table-column table:style-name="table_spip.A" table:number-columns-repeated="3"/>',$texte,1);
	}*/
	// analyser les tables (nb colonnes) et remplacer leur tag de debut et de fin
	list($texte,,$tag,,) = spip2odt_analyser_tables($texte,$nb_tables_spip);
	while (strlen($tag)){
		$nb_tables_spip++;
		list($texte,,$tag,,) = spip2odt_analyser_tables($texte,$nb_tables_spip);
	}
	$texte = preg_replace(",<([/]?)tr(\s[^>]*)?>,ims",'<\\1'.'table:table-row>',$texte);
	$texte = preg_replace(",<(td|th)(\s[^>]*)?>,ims",'<table:table-cell table:style-name="td_spip" office:value-type="string"><text:p text:style-name="table_spip_contenu">',$texte);
	$texte = preg_replace(",</(td|th)>,ims",'</text:p></table:table-cell>',$texte);
	$texte = preg_replace(",<[/]?(thead|tbody)>,","",$texte);
	//$texte = preg_replace(",</table>,ims","</table:table><text:p/>",$texte);
	
	return $texte;
}
function spip2odt_analyser_tables($texte,$no_table_spip){
	$avant = "";
	$tag = "";
	$content = "";
	$txt = "";
	// tant qu'il y a des tags
	$chars = preg_split(",<table(\s[^>]*)?>,is",$texte,2,PREG_SPLIT_DELIM_CAPTURE);
	if(count($chars)>=2){
		$avant = $chars[0];
		$tag = $chars[1];
		$txt = $chars[2];
		if(substr($tag,-1)=='/'){ // self closing tag
			$cols = 0;
			$content = "";
		}
		else{
			// tag fermant
			$chars = preg_split(",(</table>),is",$txt,null,PREG_SPLIT_DELIM_CAPTURE);
			$content = "";
			if (count($chars)>3){ // plusieurs tags fermant -> verifier les tags ouvrants/fermants
				$nclose =0; $nopen = 0;
				preg_match_all("{<table(\s*>|\s[^>]*[^/>]>)}isS",$chars[0],$matches,PREG_SET_ORDER);
				$nopen += count($matches);
				while ($nopen>$nclose && (count($chars)>3)){
					$content.=array_shift($chars);
					$content.=array_shift($chars);
					$nclose++;
					preg_match_all("{<table(\s*>|\s[^>]*[^/>]>)}isS",$chars[0],$matches,PREG_SET_ORDER);
					$nopen += count($matches);
				}
			}
			if (!isset($chars[1])) { // tag fermant manquant
				$txt = $content . implode("",$chars);
				$content = "";
			}
			else {
				$content .= array_shift($chars);
				array_shift($chars); // enlever le separateur
				$txt = implode("",$chars);
			}
		}
		// $content est l'interieur de la table
		$content_safe = $content;
		// supprimer les tables imbriquees
		if (preg_match(",<table([^>]*?)>,is",$content)){
			$content_safe="";
			list(,$ins_avant,$ins_tag,$ins_cont,$ins_apres) = spip2odt_analyser_tables($content,0);
			$content_safe .= $ins_avant;
			while(strlen($ins_tag)){
				list(,$ins_avant,$ins_tag,$ins_cont,$ins_apres) = spip2odt_analyser_tables($ins_apres,0);
				$content_safe .= $ins_avant;
			}
		}
		// splitter les lignes de la table et compter les colonnes
		$lines = preg_split(",<(tr)(\s[^>]*)?>(.*)</tr>,Uims",$content_safe,-1,PREG_SPLIT_DELIM_CAPTURE);
		$maxcols = 0;
		for ($j=1;$j<count($lines);$j+=2){
			preg_match_all(",<t(d|h)(\s*>|\s[^>]*[^/>]>),isS",$lines[$j],$matches,PREG_SET_ORDER);
			$maxcols = max($maxcols,count($matches));
		}
		// renommer le tag
		$tag = '<table:table table:name="Tableau'.$no_table_spip.'" table:style-name="table_spip">'
		  . '<table:table-column table:style-name="table_spip.A" table:number-columns-repeated="'.$maxcols.'"/>';
		$texte = $avant.$tag.$content."</table:table><text:p/>".$txt;
	}
	else $avant = $texte;
	return array($texte,$avant,$tag,$content,$txt);
}

function spip2odt_convertir_images($texte,$dossier){
	if (preg_match_all(
		',(<([a-z]+) [^<>]*spip_documents[^<>]*>)?\s*(<img\s.*>),UimsS',
		$texte, $tags, PREG_SET_ORDER)) {
		$dir = sous_repertoire($dossier,'Pictures');
		include_spip('inc/distant');
		foreach ($tags as $tag) {
			// En cas de span spip_documents_xx recuperer la class
			$class = "";
			if($tag[1])
				$class = extraire_attribut($tag[1], 'class');
			$src = extraire_attribut($tag[3],'src');
			$height = round(intval(extraire_attribut($tag[3],'height'))/28.3378,2);
			$width = round(intval(extraire_attribut($tag[3],'width'))/28.3378,2);
			$fichier = copie_locale($src);
			if ($ok = @copy($fichier, $dir.basename($fichier))){
				// TODO gerer ici les cas spip_documents_left/right/center
				$src = "Pictures/".basename($fichier);
				$insert = '<draw:frame draw:style-name="fr1" draw:name="Image1" text:anchor-type="paragraph" svg:width="'
				  .$width.'cm" svg:height="'.$height.'cm" draw:z-index="0"><draw:image xlink:href="'
				  .$src.'" xlink:type="simple" xlink:show="embed" xlink:actuate="onLoad"/></draw:frame>';
				$texte = str_replace($tag[3], $insert, $texte);
			}
			else echo ("erreur copy $fichier vers ".$dir.basename($fichier));
		}
	}
	return $texte;
}

function spip2odt_reparagrapher($texte,$dossier){
	$split = preg_split(',(<text:p [^>]*>),ims',$texte,null,PREG_SPLIT_DELIM_CAPTURE);
	$n = count($split);
	for ($i=2;$i<$n;$i+=2){
		if (strpos($split[$i],'<p ')!==FALSE){
			// enlever le <p ouvrant
			$split[$i] = preg_replace(",\A\s*<p [^>]*>,ms","",$split[$i]);
			// enlever le </p fermant
			$split[$i] = preg_replace(",</p>\s*(</text:p(\s[^>]*)?>),ms","\\1",$split[$i]);
			
			// tous les <p ouvrants sont remplaces par le <text:p parent
			$split[$i] = preg_replace(",<p [^>]*>,ms",$split[$i-1],$split[$i]);
			// tous les </p fermants sont remplaces par </text:p>
			$split[$i] = preg_replace(",</p>,ms",'</text:p>',$split[$i]);
		}
		$split[$i] = spip2odt_convertir_images($split[$i],$dossier);
		$split[$i] = spip2odt_convertir_tags_inline($split[$i]);
	}
	return implode('',$split);
}

function spip2odt_ajouter_styles($texte){
	// le gras
	$styles = '<style:style style:name="spip_strong" style:family="text"><style:text-properties fo:font-weight="bold" style:font-weight-asian="bold" style:font-weight-complex="bold"/>';
	$styles .= '<style:style style:name="spip_b" style:family="text"><style:text-properties fo:font-weight="bold" style:font-weight-asian="bold" style:font-weight-complex="bold"/>';
	// l'italique
	$styles .= '</style:style><style:style style:name="spip_i" style:family="text"><style:text-properties fo:font-style="italic" style:font-style-asian="italic" style:font-style-complex="italic"/></style:style>';
	$styles .= '</style:style><style:style style:name="spip_em" style:family="text"><style:text-properties fo:font-style="italic" style:font-style-asian="italic" style:font-style-complex="italic"/></style:style>';
	// le souligne ?
	//$styles .= '<style:style style:name="spip_souligne" style:family="text"><style:text-properties style:text-underline-style="solid" style:text-underline-width="auto" style:text-underline-color="font-color"/></style:style>';
	
	// les listes numerotees
	$styles .= '<text:list-style style:name="spip_ol">'
	. '<text:list-level-style-number text:level="1" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">'
	. '<style:list-level-properties text:space-before="0.635cm" text:min-label-width="0.635cm"/>'
	. '</text:list-level-style-number>'
	. '<text:list-level-style-number text:level="2" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">'
	. '<style:list-level-properties text:space-before="1.27cm" text:min-label-width="0.635cm"/>'
	. '</text:list-level-style-number>'
	. '<text:list-level-style-number text:level="3" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">'
	. '<style:list-level-properties text:space-before="1.905cm" text:min-label-width="0.635cm"/>'
	. '</text:list-level-style-number>'
	. '<text:list-level-style-number text:level="4" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">'
	. '<style:list-level-properties text:space-before="2.54cm" text:min-label-width="0.635cm"/>'
	. '</text:list-level-style-number>'
	. '<text:list-level-style-number text:level="5" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">'
	. '<style:list-level-properties text:space-before="3.175cm" text:min-label-width="0.635cm"/>'
	. '</text:list-level-style-number>'
	. '<text:list-level-style-number text:level="6" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">'
	. '<style:list-level-properties text:space-before="3.81cm" text:min-label-width="0.635cm"/>'
	. '</text:list-level-style-number>'
	. '<text:list-level-style-number text:level="7" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">'
	. '<style:list-level-properties text:space-before="4.445cm" text:min-label-width="0.635cm"/>'
	. '</text:list-level-style-number>'
	. '<text:list-level-style-number text:level="8" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">'
	. '<style:list-level-properties text:space-before="5.08cm" text:min-label-width="0.635cm"/>'
	. '</text:list-level-style-number>'
	. '<text:list-level-style-number text:level="9" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">'
	. '<style:list-level-properties text:space-before="5.715cm" text:min-label-width="0.635cm"/>'
	. '</text:list-level-style-number>'
	. '<text:list-level-style-number text:level="10" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">'
	. '<style:list-level-properties text:space-before="6.35cm" text:min-label-width="0.635cm"/>'
	. '</text:list-level-style-number>'
	. '</text:list-style>';

	$texte = str_replace('</office:automatic-styles>',$styles.'</office:automatic-styles>',$texte);	
	return $texte;
}

?>