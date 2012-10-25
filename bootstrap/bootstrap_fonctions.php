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

function bootstrap_affichage_final($flux){
	if (
		$GLOBALS['html']
		AND isset($GLOBALS['visiteur_session']['statut'])
		AND $GLOBALS['visiteur_session']['statut']=='0minirezo'
		AND $GLOBALS['visiteur_session']['webmestre']=='oui'
		AND $p=stripos($flux,"</body>")
		AND $f = find_in_path("js/hashgrid.js")
	){
		$flux = substr_replace($flux,'<script type="text/javascript" src="'.$f.'"></script>',$p,0);
	}
	return $flux;
}


/**
 * Generer un bouton_action
 * utilise par #BOUTON_ACTION
 *
 * @param string $libelle
 * @param string $url
 * @param string $class
 * @param string $confirm
 *   message de confirmation oui/non avant l'action
 * @param string $title
 * @param string $callback
 *   callback js a appeler lors de l'evenement action (apres confirmation eventuelle si $confirm est non vide)
 *   et avant execution de l'action. Si la callback renvoie false, elle annule le declenchement de l'action
 * @return string
 */
function filtre_bouton_action_dist($libelle, $url, $class="", $confirm="", $title="", $callback=""){
	if ($confirm) {
		$confirm = "confirm(\"" . attribut_html($confirm) . "\")";
	  if ($callback)
		  $callback = "$confirm?($callback):false";
	  else
		  $callback = $confirm;
	}
	$ajax = explode(" ",$class);
	if (in_array("ajax",$ajax))
		$ajax = " ajax";
	else
		$ajax = "";
	$onclick = $callback?" onclick='return ".addcslashes($callback,"'")."'":"";
	$title = $title ? " title='$title'" : "";
	return "<form class='bouton_action_post$ajax' method='post' action='$url'><div>".form_hidden($url)
		."<button type='submit' class='submit btn $class'$title$onclick>$libelle</button></div></form>";
}