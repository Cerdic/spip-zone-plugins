<?php

	function chatin_insert_head($flux){

        /* initialize chat  */
        session_start(); 
        $_SESSION['username'] = $GLOBALS['visiteur_session']['nom']; // Must be already set

       
	

			$js = '<script src="'.url_absolue(find_in_path('js/chat.js.php')).'" type="text/javascript"></script>';
            $css = '<link type="text/css" rel="stylesheet" media="all" href="'.url_absolue(find_in_path('css/chat.css')).'" />'; 
            
            
            $css .= '<link type="text/css" rel="stylesheet" media="all" href="'.url_absolue(find_in_path('css/screen.css')).'" />';
            
            $css .= '<!--[if lte IE 7]>  <link type="text/css" rel="stylesheet" media="all" href="'.url_absolue(find_in_path('css/screen_ie.css')).'" /><![endif]-->';





		if (strpos($flux,'<head')!==FALSE)
			return preg_replace('/(<head[^>]*>)/i', "\n\$1".$js.$css, $flux, 1);
		else 
			return $flux.$js.$css;
	}
	
	
	

?>
