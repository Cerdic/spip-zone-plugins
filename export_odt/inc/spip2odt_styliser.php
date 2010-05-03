<?php
include_spip('inc/spipoasis');

// XHTML - Preserver les balises-bloc : on liste ici tous les elements
// dont on souhaite qu'ils provoquent un saut de paragraphe
define('_TAGS_BLOCS_TO_P',
	'p|'
	.'pre|blockquote|'
	.'textarea|'
	.'form|object|center|marquee|address|'
	.'d[ltd]|map|button|fieldset');
define('_TAGS_INLINE',
	'span|strong|b|em|i|code');


define('_TAG_OOO_BREAK_P',
	'office:text|'
	.'text:h|'
	.'text:list|text:list-item|'
	.'table:table|table:table-column|table:table-row|table:table-cell|'
	.'text:table-of-content|text:index-title|text:index-body|'
	// styles
	.'style:header|style:master-page'
);

// retablir les boucles et autres tags du squelette
function inc_spip2odt_styliser($odf_dir, $contexte){
	// pas de fond dans le contexte
	unset($contexte['fond']);

	compiler_xml_oasis($odf_dir . "content.xml", $contexte);
	compiler_xml_oasis($odf_dir . "styles.xml", $contexte);

	spip2odt_ajouter_styles_perso("templates/styles.xml", $odf_dir . "styles.xml");

	spipoasis_ecrire_meta($odf_dir, $contexte);
}

/*
 * Compile le fichier indique, sauve le resultat sous le meme nom de fichier
 * et retourne le resultat de la compilation.
 * 
 * Il faut retablir la syntaxe correcte des boucles SPIP au prealable
 */
function compiler_xml_oasis($fichier, $contexte) {

	// separer nom, extension...
	$infos = pathinfo($fichier);
	$dir = $infos['dirname'] . '/';
	$ext = $infos['extension'];
	$nom = substr($infos['basename'], 0, -(strlen($ext)+1));

	// lire le content
	lire_fichier($fichier, $texte);

	// retablir les boucles
	$texte = preg_replace(",&lt;([/]?B(.*))&gt;,U","<\\1>", $texte);
	
	// retablir les includes
	$texte = preg_replace(",&lt;([/]?INCLU[RD]E(.*))&gt;,U","<\\1>", $texte);
	
	// falsifier l'en tete xml
	$texte = preg_replace(",^<"."[?]xml,","<@XML", $texte);

	// ajouter les directives de cache/charset et mime-type
	$texte = "#"."CACHE{0}
	#"."HTTP_HEADER{Content-type: text/xml; charset=UTF-8}
	$texte";


	// ecrire le squelette et le fichier fonctions associe
	ecrire_fichier(_DIR_TMP . $nom . ".html", $texte);
	lire_fichier(_DIR_PLUGIN_SPIPODF . $nom . "_fonctions.php", $fonctions);
	ecrire_fichier(_DIR_TMP . $nom . "_fonctions.php", $fonctions);

	// calculer le fond
	include_spip('inc/assembler');
	$texte = recuperer_fond(_DIR_TMP . $nom, $contexte);
	
	// nettoyer
	@unlink(_DIR_TMP."content_fonctions.php");
	@unlink(_DIR_TMP."content.html");

	$texte = preg_replace(",^<@XML,","<"."?xml", $texte);

	// convertir les balises html ajoutees par propre en tags xml
	$texte = spip2odt_convertir($texte, $dir);

	ecrire_fichier($fichier, $texte);

}


function spip2odt_convertir($texte, $dossier){
	#include_spip('inc/xml');
	#$xml = spip_xml_parse($texte);
	#var_dump($xml);
	// precaution pas inutile
	#$texte = quote_amp($texte);
	
	// reperer les puces et les substituer
	//$puce = str_replace("toto","",propre("\n- toto"));
	//$texte = str_replace($puce,"<p />* ",$texte);
	
	// remplacer les br par des p
	$texte = preg_replace(",<br(\s*/)?>,ims","<p />", $texte);
	
	// supprimer les styles enligne (comme geshi)
	$texte = preg_replace(",<style(\s[^>]*)?>.*</style>,Uims","", $texte);
	
	// faire un heritage des <p>
	$texte = spip2odt_heriter_p($texte, $dossier);
	
	
	// on ajoute ici des paragraphe, donc a faire avant reparagraphage
	$texte = spip2odt_convertir_tags_blocs($texte);
	
	// transmettre les paragraphes aux enfants
	$texte = spip2odt_reparagrapher($texte);
	
	$texte = unicode2charset(html2unicode($texte, true), 'utf-8');
	$texte = str_replace("&nbsp;", " ", $texte);
	
	//securisons tout ce qui n'est pas un tag xml odt
	$splits = preg_split(",(</?[a-z]+[:][a-z]+[^<>]*>),Ui", $texte, null, PREG_SPLIT_DELIM_CAPTURE);
	$texte = array_shift($splits);
	while (count($splits))
		$texte .= array_shift($splits).str_replace(array('<','>'),array('&lt;','&gt;'),array_shift($splits));
	
	return $texte;
}


// Conversion SPIP->ODF
function spip2odt_convertir_tags_inline($texte){
	// traitement des liens :
	if (preg_match_all(",(<a\s[^<>]*>)(.*)</a>,Uims",$texte,$regs,PREG_SET_ORDER)){
		foreach($regs as $reg){
			$href = extraire_attribut($reg[1],'href');
			$href=preg_replace(',\s*,ms','',$href);
			$texte = str_replace($reg[0],'<text:a xlink:type="simple" xlink:href="'.str_replace("&","&amp;",$href).'">'.$reg[2].'</text:a>',$texte);
		}
	}
	$texte = preg_replace(",<("._TAGS_INLINE.")(\s+[^<>]*)?>,","<text:span text:style-name='spip_\\1'>",$texte);	
	$texte = preg_replace(",</("._TAGS_INLINE.")>,","</text:span>",$texte);	
	return $texte;
}

function spip2odt_convertir_tags_blocs($texte){
	static $nb_tables_spip = 1;
	// les ul/ol li
	$texte = preg_replace(",<li(\s[^<>]*)?>,ims",'<text:list-item>',$texte);
	$texte = preg_replace(",</li>,ims","</text:list-item>",$texte);
	
	$texte = preg_replace(",<ul(\s[^<>]*)?>,ims",'<text:list text:style-name="spip_ul">',$texte);
	$texte = preg_replace(",</ul>\s*(<p(\s[^<>]*)>),ims","</text:list><text:p/>\\1",$texte);
	$texte = preg_replace(",</ul>,ims","</text:list>",$texte);
	
	$texte = preg_replace(",<ol(\s[^<>]*)?>,ims",'<text:list text:style-name="spip_ol">',$texte);
	$texte = preg_replace(",</ol>\s*(<p(\s[^<>]*)>),ims","</text:list><text:p/>\\1",$texte);
	$texte = preg_replace(",</ol>,ims","</text:list>",$texte);
	// paragrapher les items de list
	$texte = preg_replace(",(<text:list-item>)(.*)(<[/]?text:list),Uims",'\\1<text:p text:style-name="spip_li">\\2</text:p>\\3',$texte);
	
	// analyser les tables (nb colonnes) et remplacer leur tag de debut et de fin
	list($texte,,$tag,,) = spip2odt_analyser_tables($texte,$nb_tables_spip);
	while (strlen($tag)){
		$nb_tables_spip++;
		list($texte,,$tag,,) = spip2odt_analyser_tables($texte,$nb_tables_spip);
	}
	$texte = preg_replace(",<([/]?)tr(\s[^<>]*)?>,ims",'<\\1'.'table:table-row>',$texte);
	$texte = preg_replace(",<(td|th)(\s[^<>]*)?>,ims",'<table:table-cell table:style-name="td_spip" office:value-type="string"><text:p text:style-name="table_spip_contenu">',$texte);
	$texte = preg_replace(",</(td|th)>,ims",'</text:p></table:table-cell>',$texte);
	$texte = preg_replace(",<[/]?(thead|tbody)>,","",$texte);
	
	// les headings
	$texte = preg_replace(",(<h([1-6])(\s[^<>]*)?".">),is",'<text:h text:style-name="spip_h\\2" text:outline-level="\\2">',$texte);
	$texte = preg_replace(",(</h([1-6])>),is",'</text:h>',$texte);
	
	// les tags blocs restants sauf les div
	$texte = preg_replace(",(<("._TAGS_BLOCS_TO_P.")(\s[^<>]*)?".">),is",'<text:p text:style-name="spip_\\2">',$texte);
	// les div qui sont generiquement utilisees pour faire des encadres ou autre
	$splits = preg_split(",(<(div)(\s[^<>]*>|>)),is",$texte,-1,PREG_SPLIT_DELIM_CAPTURE);
	#var_dump($splits);die();
	$texte = $splits[0];
	for ($i=1;$i<count($splits);$i+=4){
		$class = 'spip_'.extraire_attribut($splits[$i],'class');
		$tag = '<text:p text:style-name="'.$class.'">';
		$texte .= $tag . (isset($splits[$i+3])?$splits[$i+3]:"");
	}
	$texte = preg_replace(",</(div|"._TAGS_BLOCS_TO_P.")>,is","</text:p>",$texte);
	return $texte;
}
function spip2odt_analyser_tables($texte, $no_table_spip){
	$avant = "";
	$tag = "";
	$content = "";
	$txt = "";
	// tant qu'il y a des tags
	$chars = preg_split(",<table(\s[^<>]*)?>,is",$texte,2,PREG_SPLIT_DELIM_CAPTURE);
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
				preg_match_all("{<table(\s*>|\s[^<>]*[^/>]>)}isS",$chars[0],$matches,PREG_SET_ORDER);
				$nopen += count($matches);
				while ($nopen>$nclose && (count($chars)>3)){
					$content.=array_shift($chars);
					$content.=array_shift($chars);
					$nclose++;
					preg_match_all("{<table(\s*>|\s[^<>]*[^/>]>)}isS",$chars[0],$matches,PREG_SET_ORDER);
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
		if (preg_match(",<table([^<>]*?)>,is",$content)){
			$content_safe="";
			list(,$ins_avant,$ins_tag,$ins_cont,$ins_apres) = spip2odt_analyser_tables($content,0);
			$content_safe .= $ins_avant;
			while(strlen($ins_tag)){
				list(,$ins_avant,$ins_tag,$ins_cont,$ins_apres) = spip2odt_analyser_tables($ins_apres,0);
				$content_safe .= $ins_avant;
			}
		}
		// splitter les lignes de la table et compter les colonnes
		$lines = preg_split(",<(tr)(\s[^<>]*)?>(.*)</tr>,Uims",$content_safe,-1,PREG_SPLIT_DELIM_CAPTURE);
		$maxcols = 0;
		for ($j=1;$j<count($lines);$j+=2){
			preg_match_all(",<t(d|h)(\s*>|\s[^<>]*[^/>]>),isS",$lines[$j],$matches,PREG_SET_ORDER);
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

function spip2odt_imagedraw($dir,$img,$align='left',$titre="",$descriptif="",$href="",$title=""){
	static $image_nb = 0;
	$insert = "";
	$src = extraire_attribut($img,'src');
	$alt = extraire_attribut($img,'alt');
	list($height,$width)=taille_image($img);
	$height = round(intval($height)/38.3378,2);
	$width = round(intval($width)/38.3378,2);
	$fichier = copie_locale($src);
	if (!$ok = @copy($fichier, $dir.basename($fichier))){
		$fichier = copie_locale(url_absolue($src)); // essayer en http
		$ok = @copy($fichier, $dir.basename($fichier));
	}
	if ($ok){
		$image_nb++;
		$src = basename($dir)."/".basename($fichier);
		$insert = '<draw:frame draw:style-name="spip_documents_'.$align.'" draw:name="Image'
		  .$image_nb.'" text:anchor-type="paragraph" svg:width="'
		  .$width.'cm" svg:height="'.$height.'cm" draw:z-index="0"><draw:image xlink:href="'
		  .$src.'" xlink:type="simple" xlink:show="embed" xlink:actuate="onLoad"/>'
		  . ($alt?"<svg:desc>$alt</svg:desc>":'')
		  . '</draw:frame>';
		  
		if ($href){
			$insert = '<draw:a xlink:type="simple" xlink:href="'.str_replace("&","&amp;",$href).'" office:name="'.$title.'">'
			 . $insert
			 . '</draw:a>';
		}
		// un caption ?
		if ($titre OR $descriptif){
			$insert = '<draw:frame draw:style-name="spip_documents_'.$align.'" '
			. 'draw:name="Frame1" text:anchor-type="paragraph" svg:x="0cm" svg:y="0cm" svg:width="'
			. max($width,7.5).'cm" svg:height="'
			. $height.'cm" style:rel-height="scale-min" draw:z-index="0"><draw:text-box>'
			. '<text:p text:style-name="'.($titre?'spip_doc_titre':'spip_doc_descriptif').'">'
			. $insert
			. ($titre?($titre.($descriptif?'</text:p><text:p text:style-name="spip_doc_descriptif">':'')):'')
			. $descriptif
			. '</text:p></draw:text-box></draw:frame>';
		}
	}
	else spip_log("erreur copy $fichier vers ".$dir.basename($fichier));
	return $insert;
}

function spip2odt_convertir_images($texte,$dossier){
	//$puce = str_replace("toto","",propre("\n- toto"));
	$dir = sous_repertoire($dossier,'Pictures');
	include_spip('inc/distant');
	$split = preg_split(',(<[a-z]+\s[^<>]*spip_documents[^<>]*>),Uims',$texte,null,PREG_SPLIT_DELIM_CAPTURE);
	$class = "";
	$texte = "";
	while (count($split)){
		$frag = array_shift($split);
		if (preg_match_all(
		  ','
		  .'(<([b-z][a-z]*)(\s[^<>]*)?>)?' # ne pas attraper les <text:p > qui precedent une image
		  .'(<a [^<>]*>)?\s*(<img\s[^<>]*>)(\s*</a>)?'
		  .'(\s*</\\2>)?'
		  .'(\s*<([a-z]+)[^<>]*spip_doc_titre[^<>]*>(.*?)</\\9>)?'
		  .'(\s*<([a-z]+)[^<>]*spip_doc_descriptif[^<>]*>(.*?)</\\12>)?'
		  .',imsS',
		   $frag, $regs,PREG_SET_ORDER)!==FALSE) {
		  #if (count($regs)) {var_dump($frag);var_dump($regs);die;}
			#if ($class && count($regs) && !count($split)) {var_dump($frag);var_dump($regs);die;}
			foreach($regs as $reg){
				// En cas de span spip_documents_xx recuperer la class
				$align = 'left'; // comme ca c'est bon pour les puces :)
				$href = "";
				$title = "";
				if ($class AND preg_match(',spip_documents_(left|right|center),i',$class,$match))
					$align = $match[1];
				if ($reg[4]){
					$href = extraire_attribut($reg[4],'href');
					$title = extraire_attribut($reg[4],'title');
				}
				$insert = spip2odt_imagedraw($dir,$reg[5],$align,isset($reg[10])?$reg[10]:"",isset($reg[13])?$reg[13]:"",$href,$title);
				$frag = str_replace($reg[0], $insert, $frag);
				$class="";
			}
		}
		$texte .= $frag;
		$texte .= $tag=array_shift($split);
		$class = extraire_attribut($tag, 'class');
	}
	return $texte;
}

function spip2odt_heriter_p($texte,$dossier){
	$split = preg_split(',(<text:p\s[^<>]*>),ims',$texte,null,PREG_SPLIT_DELIM_CAPTURE);
	$n = count($split);
	for ($i=2;$i<$n;$i+=2){
		if (strpos($split[$i],'<p ')!==FALSE){
			// enlever le <p ouvrant
			$split[$i] = preg_replace(",\A\s*<p(\s[^<>]*)?>,ms","",$split[$i]);
			// enlever le </p fermant
			$split[$i] = preg_replace(",</p>\s*(</text:p(\s[^<>]*)?>),ms","\\1",$split[$i]);
			
			// tous les <p ouvrants sont remplaces par le <text:p parent
			$split[$i] = preg_replace(",<p(\s[^<>]*)?>,ms",$split[$i-1],$split[$i]);
			// tous les </p fermants sont remplaces par </text:p>
			$split[$i] = preg_replace(",</p>,ms",'</text:p>',$split[$i]);
		}
		$split[$i] = spip2odt_convertir_images($split[$i],$dossier);
		$split[$i] = spip2odt_convertir_tags_inline($split[$i]);
	}
	return implode('',$split);
}

// Si j'ai bien compris :
// - sert a fermer tous les paragraphes ouverts lorsqu'on rencontre 
// une balise qui doit le fermer (table, li...)
// - supprime les paragraphes vides
// - corrige les paragraphes imbriques (<p><p></p></p>)
function spip2odt_reparagrapher($texte){
	// avant de reparagrapher, echappons les paragraphes dans les <draw:text-box><text:p>
	$split = preg_split(',(<draw:text-box[^<>]*>.*</draw:text-box>),Uims',$texte,null,PREG_SPLIT_DELIM_CAPTURE);
	//var_dump($split);die();
	$texte = array_shift($split);
	$texte_boxes=array();
	while (count($split)){
		$i = "@T@E@X@T@B@O@X@".count($texte_boxes)."@";
		$texte_boxes[$i] = array_shift($split);
		$texte .= $i . array_shift($split);
	}

	// Ajouter un espace aux <p> et un "STOP P"
	// transformer aussi les </p> existants en <p>, nettoyes ensuite
	$texte = preg_replace(',</?text:p(\s([^<>]*))?'.'>,iS', '<STOP P><text:p \2>',$texte);
	$texte = preg_replace(',</?text:span(\s([^<>]*))?'.'>,iS', '<STOP SPAN><text:span \2>',$texte);
	
	// Fermer les span (y compris sur "STOP P")
	$texte = preg_replace(
		',(<text:span\s.*)(</?(STOP SPAN|STOP P|'._TAG_OOO_BREAK_P.')[>[:space:]]),UimsS',
		"\\1</text:span>\\2", $texte);

	// Fermer les paragraphes (y compris sur "STOP P")
	$texte = preg_replace(
		',(<text:p\s.*)(</?(STOP P|'._TAG_OOO_BREAK_P.')[>[:space:]]),UimsS',
		"\\1</text:p>\\2", $texte);

	// Supprimer les marqueurs "STOP P"
	$texte = str_replace('<STOP P>', '', $texte);
	// Supprimer les marqueurs "STOP SPAN"
	$texte = str_replace('<STOP SPAN>', '', $texte);

	// Reduire les blancs dans les <p>
	// Do not delete multibyte utf character just before </p> having last byte equal to whitespace  
	$u = ($GLOBALS['meta']['charset']=='utf-8' && test_pcre_unicode()) ? 'u':'S';
	$texte = preg_replace(
	',(<text:p(\s[^<>]*)?>)\s*|\s*(</text:p[>[:space:]]),'.$u.'i', '\1\3',
		$texte);

	// Supprimer les <p xx></p> vides
	$texte = preg_replace(',<text:p\s[^<>]*></text:p>\s*,iS', '',
		$texte);
	$texte = preg_replace(',<text:span\s*>(.*)</text:span>,UiS', '\\1',
		$texte);
		
	// remettre les text-boxes
	$texte = str_replace(array_keys($texte_boxes),array_values($texte_boxes),$texte);
	return $texte;
}

/*
 * Ajoute des styles de presentation personnalises
 * a un fichier de presentation de oasis (styles.xml)
 */
function spip2odt_ajouter_styles_perso($fichier_perso, $fichier_oasis){

	lire_fichier($fichier_oasis, $styles);
	
	$f = find_in_path($fichier_perso);
	lire_fichier($f, $styles_defaut);
	$ajout_styles = "";
	if (preg_match_all(",<((style:style|text:list-style|text:outline-style)\s[^/>]*)(/>|>.*</(\\2)>),Uims",$styles_defaut,$matches,PREG_SET_ORDER)){
		foreach($matches as $match){
			if (preg_match(",style:name=([\"'])([^\\1]*)\\1,Ums",$match[1],$regs)){
				$nom_style = $regs[2];
				if (!preg_match(",style:name=(['\"])$nom_style\\1,",$styles))
					$ajout_styles .= $match[0];
			}
		}
	}
	$styles = str_replace('</office:styles>',$ajout_styles.'</office:styles>',$styles);	
	ecrire_fichier($fichier_oasis, $styles);

}

?>
