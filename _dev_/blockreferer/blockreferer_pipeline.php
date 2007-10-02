<?php

function blockreferer_header_prive($flux) {

  if(strpos('statistiques',_request('exec'))>=0) {
	$script = '<script type="text/javascript"><!--
$(document).ready(function(){
jQuery(\'.verdana1 > ul > li > a > span\').parent().each(function(i,e){$(e).append("<a href=\'"+encodeURI(this.href)+"\'>[X]</a>")});
});
--></script>';
	$flux .= $script;
  }
	return $flux;

}

?>
