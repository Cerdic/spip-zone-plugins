<?


function recuperer_passage($livre,$chapitre_debut,$verset_debut,$chapitre_fin,$verset_fin,$wissen,$lang){
	
	include_spip('inc/bible_tableau');
	$livre_gateways = bible_tableau('gateway');
	$livre_lang = $livre_gateways[$lang][$livre];
	$livre_al	= array_flip($livre_gateways['de']);
	$livre		= $livre_al[$livre_lang];
	
	//recuperation du passage
	$passage = $livre.$chapitre_debut.','.$verset_debut.'-'.$chapitre_fin.','.$verset_fin;
	
	$url = "http://www.bibelwissenschaft.de/nc/online-bibeln/".$wissen."/lesen-im-bibeltext/bibelstelle/".$passage."/anzeige/single/#iv";
	include_spip("inc/distant");
	include_spip("inc/charsets");
	$code = importer_charset(recuperer_page($url),'utf-8');
	
	//selection du passage
	$tableau = explode('<div class="boxcontent-bible">',$code);
	$code = $tableau[1];
	$code = eregi_replace('<h1>[0-Z]*</h1>','',$code);
	$tableau = explode('<div id="popupcontent">',$code);
	$code = $tableau[0];
	$code = strip_tags($code,'<span>');
	
	$code = str_replace('<span class="chapter">','<br /><strong>',$code);
	$code = str_replace('</span> ','</strong>',$code);
	$code = str_replace('<span class="verse">','<br /><sup>',$code);
	$code = str_replace('</span>&nbsp;','</sup>',$code);
	$code = strip_tags($code,'<br><sup><strong>');
	$code = str_replace('</strong><br />','</strong>',$code);
	$code = eregi_replace('^<br />','',$code);
	return $code;
	}
?>