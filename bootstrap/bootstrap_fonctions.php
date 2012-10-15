<?php

function filtre_lien_ou_expose_dist($url,$libelle=NULL,$on=false,$class="",$title="",$rel="", $evt=''){
	if ($on) {
		$bal = 'strong';
		$class = "";
		$att = "";
		// si $on passe la balise et optionnelement une ou ++classe
		// a.active span.selected.active etc....
		if (is_string($on) AND (strncmp($on,'a',1)==0 OR strncmp($on,'span',4)==0 OR strncmp($on,'strong',6)==0)){
			$on = explode(".",$on);
			// on verifie que c'est exactement une des 3 balises a, span ou strong
			if (in_array(reset($on),array('a','span','strong'))){
				$bal = array_shift($on);
				$class = implode(" ",$on);
				if ($bal=="a")
					$att = 'href="#" ';
			}
		}
		$att .= 'class="'.($class?attribut_html($class).' ':'').'on active"';
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