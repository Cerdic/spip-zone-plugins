<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_MENUS_EXTRA_CLASS')) define('_MENUS_EXTRA_CLASS','nav');

/**
 * Ajouter le markup html pour une navbar responsive
 * [<div class="navbar navbar-inverse navbar-responsive" id="nav">
 * (#INCLURE{fond=inclure/nav,env}|navbar_responsive)
 * </div>]
 *
 * @param string $nav
 * @param string $class_collapse nom de la class à plier/déplier
 * @return string
 */
function navbar_responsive($nav, $class_collapse = 'nav-collapse-main'){
	static $navbarcount = 1;
	if (strpos($nav,'navbar-collapse')!==false) return $nav;

	$respnav = '';

	$uls = extraire_balises($nav,"ul");
	$n = 1;
	while ($ul = array_shift($uls)
		AND strpos($navclass = extraire_attribut($ul,"class"),"nav")===false){
		$n++;
	}
	if ($ul){
		$id = "navbar-".substr(md5($navbarcount . ':' . time() .':' . $nav),0,4);
		$respnav = $nav;
		$p = strpos($respnav,$ul);
		$respnav = substr_replace($respnav,
			'<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#' . $id . '" aria-controls="'. $id . '" aria-expanded="false" aria-label="Toggle navigation">'
			. '<span class="navbar-toggler-icon"></span>'
			. '</button>'
			. "\n" . '<div class="collapse navbar-collapse ' . $class_collapse . '" id="'.$id.'">',$p,0);

		$l=strlen($respnav);$p=$l-1;
		while ($n--){
			$p = strrpos($respnav,"</ul>",$p-$l);
		}
		if ($p)
			$respnav = substr_replace($respnav,'</div>',$p+5,0);
		else
			$respnav = $nav;
	}
	return $respnav;
}

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

function bootstrap4_affichage_final($flux){
	if (
		$GLOBALS['html']
		AND isset($GLOBALS['visiteur_session']['statut'])
		AND $GLOBALS['visiteur_session']['statut']=='0minirezo'
		AND $GLOBALS['visiteur_session']['webmestre']=='oui'
		AND strpos($flux,"<!-- insert_head -->")!==false
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



if (!test_espace_prive()){

/**
 * Ouvrir une boite
 * peut etre surcharge par filtre_boite_ouvrir_dist, filtre_boite_ouvrir
 *
 * @param string $titre
 * @param string $class
 * @return <type>
 */
function filtre_boite_ouvrir_dist($titre, $class='', $head_class='', $id=""){
	$class = "panel panel-default $class";
	$head_class = "panel-heading $head_class";
	// dans l'espace prive, titrer en h3 si pas de balise <hn>
	if (test_espace_prive() AND strlen($titre) AND strpos($titre,'<h')===false)
		$titre = "<h3>$titre</h3>";
	return '<div class="'.$class.($id?"\" id=\"$id":"").'">'
	.($titre?'<div class="'.$head_class.'">'.$titre.'</div>':'')
	.'<div class="panel-body">';
}

/**
 * Passer au pied d'une boite
 * peut etre surcharge par filtre_boite_pied_dist, filtre_boite_pied
 *
 * @param <type> $class
 * @return <type>
 */
function filtre_boite_pied_dist($class='act'){
	$class = "panel-footer $class";
	return 	'</div>'
	.'<div class="'.$class.'">';
}

/**
 * Fermer une boite
 * peut etre surcharge par filtre_boite_fermer_dist, filtre_boite_fermer
 *
 * @return <type>
 */
function filtre_boite_fermer_dist(){
	return '</div>'
	.'</div>';
}

}