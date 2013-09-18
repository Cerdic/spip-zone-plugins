<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// ajout feuille de stylle
//
function sparkstats_insert_head($flux){
  #$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('sparkstats.css').'" media="all" />';
  $cfg = unserialize($GLOBALS['meta']['sparkstats']);
  if(is_array($cfg)){
  	$cible = $cfg['sparkstats_cible'];
  }else{
  	$cible = '.cartouche small,.info-publi:eq(0)';
  }
  $jsFile = find_in_path('js/jquery.sparkline.js');
  $flux .= "<script src='$jsFile' type='text/javascript'></script>";

  $flux .= '<script type="text/javascript"><!--
  (function($){
  var sparkstats_done=false;
  var sparkstats = function() {
  $(".entry-title.crayon,h1.crayon")
  .each(function() {
    if (sparkstats_done) return;
    var m;
    if ((m = $(this).attr("class").match(/article-titre-(\d+)/)) && (m=m[1])) {
      sparkstats_done = true;
      $.get("'.generer_url_public('sparkstats','id_article=', '&').'"+m, function(e){
        if(e)
        $("<span style=\'padding-left:20px\'>")
        .html(e)
        .appendTo("'.$cible.'")
        .sparkline();
      });
    }
  });
  };
  sparkstats();
  onAjaxLoad(sparkstats);
  })(jQuery);
  --></script>
  ';

  return $flux;
}


?>
