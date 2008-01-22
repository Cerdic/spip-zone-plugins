<?php
function abcalendrier_insert_head($flux){
   $css_link="<link rel=\"stylesheet\" href=\""._DIR_PLUGIN_ABCALENDRIER."abcalendrier.css\" type=\"text/css\" media=\"projection, screen\" />\n";
      $flux .=  "\n<!-- Debut header du ABCalendrier -->\n$css_link\n<!-- Fin header du ABCalendrier -->\n\n";
	return $flux;
}
?>