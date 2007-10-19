<?php


function passe_complexe_header_prive($flux){
  if (_request('exec')=='auteur_infos'){
	include_spip('inc/passe_complexe');
	$flux .= passe_complexe_generer_javascript("input.formo[@name=new_pass]");
  } else if (_request('exec')=='cfg'){
	include_spip('inc/passe_complexe');
	$flux .= passe_complexe_generer_javascript("input.type_pwd");
  }
  return $flux;
}

?>
