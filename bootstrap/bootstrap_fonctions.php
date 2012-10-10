<?php

if (!test_espace_prive()){
function filtre_lien_ou_expose_dist($url,$libelle=NULL,$on=false,$class="",$title="",$rel="", $evt=''){
	if ($on) {
		$bal = 'a';
		$att = 'href="#" class="on active'.($class?' '.attribut_html($class):'').'"';
	} else {
		$bal = 'a';
		$att = "href='$url'"
	  	.($title?" title='".attribut_html($title)."'":'')
	  	.($class?" class='".attribut_html($class)."'":'')
	  	.($rel?" rel='".attribut_html($rel)."'":'')
		.$evt;
	}
	if ($libelle === NULL)
		$libelle = $url;
	return "<$bal $att>$libelle</$bal>";
}
}