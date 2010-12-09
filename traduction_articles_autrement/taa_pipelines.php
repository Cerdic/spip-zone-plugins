<?php
function taa_header_prive($flux){

    $flux .= '<link rel="stylesheet" href="'.chemin('css/taa_styles.css').'" type="text/css" media="all" />';
 	return $flux;	

 }
 function taa_formulaire_charger($flux){
    $form = $flux['args']['form'];
   if ($form=='editer_article'){
       $flux['data']['_hidden'] .= '<input type="hidden" name="lang" value="'._request('lang').'"/>';
    }
    return $flux;
}
?>
