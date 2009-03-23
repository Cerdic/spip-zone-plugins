<?php
 
//
// ajout feuille de stylle
//
function sparkstats_insert_head($flux){
  #$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('sparkstats.css').'" media="all" />';

  $jsFile = find_in_path('js/jquery.sparkline.js');
  $flux .= "<script src='$jsFile' type='text/javascript'></script>";

  $flux .= '<script type="text/javascript">
  (function($){$(function(){$(".entry-title.crayon")
  .each(function() {
    var m;
    if ((m = $(this).attr("className").match(/article-titre-(\d+)/)) && (m=m[1])) {
      $.get("'.generer_url_public('sparkstats','id_article=', '&').'"+m, function(e){
        if(e)
        $("<span style=\'padding-left:20px\'>")
        .html(e)
        .appendTo(".cartouche small:eq(0)")
        .sparkline();
      });
	}
  });
  })})(jQuery);
  </script>
  ';

  return $flux;
}


?>
