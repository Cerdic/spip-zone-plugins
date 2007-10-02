<?php

function blockreferer_header_prive($flux) {
  //  var_dump($auteur);
  if((strpos('statistiques',_request('exec'))>=0)
	  && ($GLOBALS['auteur_session']['statut'] == '0minirezo')) {
  
  $script = '<script type="text/javascript"><!--
$(document).ready(function(){
jQuery(\'.verdana1 > ul > li > a > span\').parent().each(function(i,e){$(e).after("&nbsp;<a href=\''.
	generer_action_auteur("blockreferer_manage","referer",generer_url_ecrire(_request('exec')))
.'&url_refer="+encodeURI(this.href)+"\'>[X]</a>")});
});
--></script>';
  $flux .= $script;
  }
	return $flux;

}

?>
