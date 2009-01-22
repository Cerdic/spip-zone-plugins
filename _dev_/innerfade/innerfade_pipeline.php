<?php 

    if (!defined("_ECRIRE_INC_VERSION")) return;
    
    function innerfade_insert_head($flux){
        $flux .= '<link rel="stylesheet" href="'.find_in_path('css/diaporama_innerfade.css').'" type="text/css" media="projection, screen, tv" />'."\n";
        $flux .= '<script src="'.find_in_path('js/jquery.innerfade.js').'" type="text/javascript"></script>'."\n";
        return $flux;

    }

    function innerfade_header_prive($flux){
        $flux .= '<link rel="stylesheet" href="'.find_in_path('css/diaporama_innerfade.css').'" type="text/css" media="projection, screen, tv" />'."\n";
        $flux .= '<script src="'.find_in_path('js/jquery.innerfade.js').'" type="text/javascript"></script>'."\n";
        return $flux;    
    }

?>
