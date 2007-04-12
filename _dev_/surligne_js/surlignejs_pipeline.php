<?php

function Surlignejs_insert_head($flux) {
    $flux .= "<script type='text/javascript' src='".find_in_path("javascript/SEhighlight.js")."'></script>\n";
    $flux .= "<script type='text/javascript'>
    jQuery(function(){jQuery(document).SEhighlight({
    style_name:'spip_surligne',
    exact:'whole',
    style_name_suffix:false,
    engines:[/^".str_replace(array("/","."),array("\/","\."),$GLOBALS['meta']['adresse_site'])."/i,/recherche=([^&]+)/i],
    startHighlightComment:'debut_surligneconditionnel',
    stopHighlightComment:'finde_surligneconditionnel'
    })});
    </script>
    ";
  return $flux;
}

?>
