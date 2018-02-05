<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function disposition_insert_head_css($flux){
	$flux.="\n".'<link rel="stylesheet" type="text/css" media="all" href="'.find_in_path('css/disposition.less').'" />';
	$flux.="\n".'<link rel="stylesheet" type="text/css" media="all" href="'.find_in_path('css/bootstrap-modal.css').'" />';	
	return $flux;
}


function disposition_insert_head($flux){
   // $flux .= "\n"."<!-- un commentaire pour rien Mikha ! -->\n";
   
   // modale
    $flux .= "\n".'<script type="text/javascript" src="'.find_in_path('js/bootstrap-modal.js').'"></script>';
    $flux .= "\n".'<script type="text/javascript" src="'.find_in_path('js/bootstrap-modalmanager.js').'"></script>';
	$flux .= "\n".'<script type="text/javascript" src="'.find_in_path('js/bootstrap-transition.js').'"></script>';
	//$flux .= "\n".'<script type="text/javascript" src="'.find_in_path('js/bootstrap-modal-disposition.js').'"></script>';
	
    
    // carousel
     $flux .= "\n".'<script type="text/javascript" src="'.find_in_path('js/bootstrap-carousel.js').'"></script>';

    return $flux;
}


?>
