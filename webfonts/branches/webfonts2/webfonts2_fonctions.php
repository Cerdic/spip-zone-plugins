<?php
/*
 * Plugin Webfonts2
 * (c) 2016
 * Distribue sous licence GPL
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * googlefont_request
 *
 * prepare l'url sans passer par l'api , utilisé pour l'insertion dans le head
 *
 * @param $webfonts {array} font=>variantes
 * @param $subsets  si besoin une liste de subsets pour la forme mais inutile
 * @return $request url de requète 
 * 
*/
function googlefont_request($webfonts,$subsets=''){
	$subset = '&subset=' ;
	(strlen($subsets)) ? $subset .= $subsets : $subset = '';
	foreach($webfonts as $font){
		$variants = implode(',',$font['variants']);
		$fonts[] = urlencode($font['family']).':'.$variants;	
	}	
	$fonts = implode('|',$fonts);
	$request = "https://fonts.googleapis.com/css?family=$fonts".$subset;
	return $request;
}

/**
 * google_font_search
 *
 * $fonts la liste des fonts récupérées via l'API
 * $search le motif de recherche sur item/family
*/
function google_font_search($fonts, $search){
	$res = array();
	if (is_array($fonts)) {
		foreach($fonts['items'] as $item){
			( preg_match('/' . trim($search) . '/i', $item['family']) ) ? $res[] = $item : false;
		}
	}
	return $res;
}

// permet aux plugins themes de spécifier leur webfonts
// via la pipeline fonts_list

function lister_webfonts(){
	$fonts = pipeline('fonts_list',array(
		'args'=>array(),
		'data'=>$fonts
	));
	return $fonts;
}

function balise_FONT_INDEX_dist($p){
	$p->code = "get_font_index()";
	$p->interdire_scripts = false;
	return $p;
}

function get_font_index(){
	lire_fichier(_DIR_TMP.'/googlefont_list.json',$respons);
	return json_decode($respons, true);
}

/**
 * googlefont_api_get
 *
 * retourne l'index complet de la typothèque via l'API
 *
 * @todo index.json à stocker en copie locale ou dans /tmp ? 
*/
function googlefont_api_get($api_key,$sort=false,$category=false){
	// Requète en GET sur //https://www.googleapis.com/webfonts/v1/webfonts?key=_GOOGLE_API_KEY
	$url = 'https://www.googleapis.com/webfonts/v1/webfonts?key='.$api_key;
	(strlen($sort)) ? $url .= '&sort='.$sort : $sort = false ;
	(strlen($category)) ? $url .= '&category='.$category : $category = false;	
		
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result = curl_exec($ch);
	curl_close($ch);
	$googlefonts = json_decode($result, true);
	
	return $googlefonts;
}


/*
 * fontface_declaration
 *
 * 
@font-face {
    font-family: 'open_sansitalic';
    src: url('OpenSans-Italic-webfont.eot');
    src: url('OpenSans-Italic-webfont.eot?#iefix') format('embedded-opentype'),
         url('OpenSans-Italic-webfont.woff2') format('woff2'),
         url('OpenSans-Italic-webfont.woff') format('woff'),
         url('OpenSans-Italic-webfont.ttf') format('truetype'),
         url('OpenSans-Italic-webfont.svg#open_sansitalic') format('svg');
    font-weight: normal;
    font-style: normal;

}

@param $font array family|file|weight|style
@param $formats array extension|format default 

*/

function fontface_declaration($font, $formats = array('.woff'=>'woff','.woff2'=>'woff2','.ttf'=>'truetype')){ 
	
	$default = array(
		'family'=>'Dutissimo',
		'file'=>'squelettes-dist/polices/dutissimo',
		'weight'=>'400',
		'style'=>'normal'
	);	
	$font = array_merge($default,$font);
	$font_files = '';
	$i = 1;
	foreach($formats as $extension => $format){
		$font_files .="url('".$font['file'].$extension."') format('$format')";
		($i < count($formats)) ? $font_files .=", " : $font_files .=";";	
		$i++;		
	}

	$declaration = <<<EOT
@font-face {
    font-family: '{$font['family']}';
	src: $font_files
    font-weight:{$font['weight']};
    font-style:{$font['style']};
}
EOT;

	return $declaration;
}

/*
 * function font_sets
 * @param $fonts
 */

function font_sets($fonts) {
	
}



function font_stacks(){
	return array(
		'sans-serif' => [
			'Helvetica' => '"Helvetica Neue", "Helvetica", "Arial", sans-serif',
			'Lucida' => '"Lucida Grande", "Lucida Sans Unicode", "Geneva", "Verdana", sans-serif',
			'Verdana' => '"Verdana", "Geneva", sans-serif',
			'System' => '-apple-system, BlinkMacSystemFont, "Avenir Next", "Avenir", "Segoe UI", "Lucida Grande", "Helvetica Neue", "Helvetica", "Fira Sans", "Roboto", "Noto", "Droid Sans", "Cantarell", "Oxygen", "Ubuntu", "Franklin Gothic Medium", "Century Gothic", "Liberation Sans", sans-serif',	
		],
		'serif'=> [
			'Garamond' => '"Garamond", "Baskerville", "Baskerville Old Face", "Hoefler Text", "Times New Roman", serif',
			'Georgia' => '"Georgia", "Times", "Times New Roman", serif',
			'Hoefler' => '"Hoefler Text", "Baskerville Old Face", "Garamond", "Times New Roman", serif',
		],
		'monospace'=> [
			'Consolas' => '"Consolas", "monaco", monospace',
			'Courrier' => '"Courier New", "Courier", "Lucida Sans Typewriter", "Lucida Typewriter", monospace',
			'Monaco' => '"Monaco", "Consolas", "Lucida Console", monospace'
		]
	);
}
