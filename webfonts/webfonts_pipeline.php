<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

function webfonts_insert_head_css($flux){
	static $done = false;
	if (!$done){
		$fonts = $GLOBALS['meta']['googlefonts_api'];
		$fonts = array_map('trim',explode("\n",$fonts));
		$fonts = array_map('urldecode',$fonts); // passer les + en ' '

		// version directe google font api
		$fonts = array_map('urlencode',$fonts);
		$fonts = implode('|',$fonts);
		if (strlen($fonts)) {
			$code = '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family='.$fonts.'" id="webfonts" />';
			// le placer avant les autres CSS du flux
			if (($p = strpos($flux,"<link"))!==false)
				$flux = substr_replace($flux,$code,$p,0);
			// sinon a la fin
			else
				$flux .= $code;
		}

		// version loader js, mais qui genere une requete google api...
		/*
		$fonts = array_map('addslashes',$fonts);
		$fonts = implode("', '",$fonts);
		if (strlen($fonts)) {
			$fonts = "'$fonts'";
			$code = '<script src="'.find_in_path('javascript/webfont.js').'" id="webfonts"></script>'
			."<script>WebFont.load({
  google: {
    families: [$fonts]
  }
});</script>";
			$flux = $code.$flux; // on le place en premier !
		}*/
		$done = true;
	}
	return $flux;
}


?>