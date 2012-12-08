<?php
	function parse_css($css) {
		if (preg_match_all("~([\w_-]*)\s*:\s*([^;]*);~", $css, $matches, PREG_SET_ORDER)) {
			$js = array() ;
			foreach($matches as $match) {
				$js[] = "'".$match[1]."': '".$match[2]."'" ;
			}
			return join(", ", $js) ;
		} else
			return '' ;
	}

	/*
	 * fonction parse_spipcss 
	 * 	@$spip : le contenu d'un textarea, servant à la configuration des styles CKEditor
	 *
	 * 	retour: le contenu d'un styles.js pour CKEditor
	 */
	function parse_spipcss($spip) {
		if (preg_match_all("#(^|\n)\s*(.*?)\s*:\s*(\w+)(?:\.(\w+))?(?:\s*{\s*(.*?)\s*})?#s", $spip, $matches, PREG_SET_ORDER)) {
			$js = array() ;
			foreach($matches as $match) {
				if ($match[5]) {
					$css="{ ".parse_css($match[5])." }" ;
				} else {
					$css='' ;
				}
				$ajs = array() ;
				$ajs[] = "name: '".$match[2]."'" ;
				if ($match[3]) {
					$ajs[] = "element: '".$match[3]."'" ;
				}
				if ($match[4]) {
					$ajs[] = "attributes: { 'class': '".$match[4]."' }" ;
				}
				if ($css) {
					$ajs[] = "styles: $css" ;
				}
				$js[] = "{".join(", " , $ajs)."}" ;
			}
			return "CKEDITOR.addStylesSet('spip-styles',\n\t[\n".join(",\n",$js)."\n\t]\n);" ;
		} else 
			return '' ;
	}
	
?>
