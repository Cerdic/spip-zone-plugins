<?php


function passe_complexe_header_prive($flux){
  if (_request('exec')=='auteur_infos'){
	include_spip('inc/passe_complexe');
	$flux .= passe_complexe_generer_javascript("input.formo[@name=new_pass]");
  }
  return $flux;
}

?>