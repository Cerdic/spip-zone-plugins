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

function webfonts2_ieconfig_metas($table){
	$table['webfonts2']['titre'] = _T('webfonts2:titre');
    $table['webfonts2']['icone'] = 'prive/themes/spip/images/webfonts2-16.png';
    $table['webfonts2']['metas_serialize'] = 'webfonts2';
	return $table;
}

/**
 * Lister les polices configurées
 *
 * ex   Open Sans:regular, Open Sans:800, Open Sans:300,
 *
*/
function webfonts2_fonts_list(){
	$webfonts = lire_config('webfonts2/webfonts');
    $fonts = array();
	if(strlen($webfonts)){
		// enlever la dernière virgule
		$webfonts = explode(',',rtrim($webfonts,', '));

		foreach($webfonts as $font){
			$set =  explode(':',$font);
            $slug = strtolower(str_replace(' ','_',trim($set[0])));
			$fonts[$slug]['family'] = trim($set[0]);
            $fonts[$slug]['variants'][] = $set[1];
		}
	}

	return $fonts;
}

/* pipeline scss_variables

si le plugin scss est activé,
rendre disponible les webfonts dans les variables scss

*/
function webfonts2_scss_variables($variables){
	return array_merge($variables,pipeline('fonts_list'));
}
/* pipeline header_prive

*/
function webfonts2_header_prive($flux){
		$insertion_prive = lire_config('webfonts2/insertion_prive');
		if($insertion_prive == true ){
			$flux = webfonts2_insertion_css($flux);
		}
    $flux .= '<script src="'._DIR_PLUGIN_WEBFONTS2.'javascript/webfonts2.js'.'" type="text/javascript"></script>';
    return $flux;
}
 /**
  * webfonts_affichage_final
  *
  * place la délaration/appel des webfont avant les autres styles
  *
  * @param string $flux pipeline affichage final
  * @return string html
 */
function webfonts2_affichage_final($flux){
	return webfonts2_insertion_css($flux);
}


function webfonts2_insertion_css($flux){
	static $done = false;
	if (!$done){
		$webfonts = lister_webfonts();
		if(is_array($webfonts)){
			(defined('_FONTS_SUBSETS')) ? $subsets = _FONTS_SUBSETS : $subsets='' ;
			$font_request = googlefont_request($webfonts,$subsets);
			if (strlen($font_request)) {
				$methode = lire_config('webfonts2');
				if($methode == 'at_import'){
					$code = "<style>@import url('$font_request');</style>";
				}else{
					$code = '<link rel="stylesheet" type="text/css" href="'.$font_request.'" id="webfonts" />';
				}
				// le placer avant les autres CSS du flux
				if (($p = strpos($flux,"<link"))!==false)
					$flux = substr_replace($flux,$code,$p,0);
				// sinon a la fin
				else
					$flux .= $code;
			}
		}
		$done = true;
	}
	return $flux;
}


?>
