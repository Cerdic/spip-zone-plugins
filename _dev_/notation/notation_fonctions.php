<?php
/**
* Plugin Notation
* par JEM (jean-marc.viglino@ign.fr) / b_b
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
*  
**/

include_spip('inc/notation_util');


/** Filtre pour les tableaux :
* transforme une liste (separee par de ,) en un tableau exploitable avec IN
*/
function notation_tab ($tab)
{ $tab = explode(',',$tab);
  return $tab;
}


// Affichage des etoiles cliquables
function notation_etoile_click($nb, $id) { 
	$ret = '';
	if ($nb>0 && $nb<=0.5){
		$nb=1;
	}else{
		$nb = round($nb);
	}
	for ($i=1; $i<=notation_get_nb_notes(); $i++){
		$ret .= "<input name='notation$id' type='radio' class='auto-submit-star' value='$i' ";
		if($i==$nb){
			$ret .= "checked='checked' ";
		}
		$ret .= "/>\n";
	}
	return $ret;
}

// Affichage d'un nombre sous forme d'etoiles
function notation_etoile($nb,$id){
	if ($nb>0 && $nb<=0.5){
		$nb=1;
	}else{
		$nb = round($nb);
	}
	for ($i=1; $i<=notation_get_nb_notes(); $i++){
		$ret .= "<input name='star$id' type='radio' class='star' disabled='disabled' ";
		if($i==$nb){
			$ret .= "checked='checked' ";
		}
		$ret .= "/>\n";

	}
	return $ret;
}

/**
* les balises du plugin
*  
**/


function balise_NOTATION_ETOILE($p){
	$nb = interprete_argument_balise(1,$p);
	$id = interprete_argument_balise(2,$p);
	$p->code = "notation_etoile($nb,$id)";
	$p->interdire_scripts = false;
	return $p;
}

function balise_NOTATION_ETOILE_CLICK($p){
	$nb = interprete_argument_balise(1,$p);
	$id = interprete_argument_balise(2,$p);
	$p->code = "notation_etoile_click($nb,$id)";
	$p->interdire_scripts = false;
	return $p;
}

/**
* Ajouter un bouton dans la barre forum
*  
**/

/* Gestion des notations dans les forums
*  Supprime [notation]
*  Transforme les [+] et [-] en images
*/

function notation_critique($p){
	$p = preg_replace('/\[notation\]/', '', $p);
	$p = preg_replace('/\[\+\]/', '<img src="'.find_in_path('img_pack/notation-plus.gif').'"> ', $p);
	$p = preg_replace('/\[-\]/', '<img src="'.find_in_path('img_pack/notation-moins.gif').'"> ', $p);
	return $p;
}

/**
* Ajout des boutons dans l'interface privee
*  
**/

function notation_ajouterBoutons($boutons_admin){
	// si on est admin
	// if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) 
	{	// Bouton dans la barre "forum"
		$boutons_admin['forum']->sousmenu['notation'] = 
			new Bouton(	"../"._DIR_PLUGIN_NOTATION."img_pack/notation.png",	// icone
						_T('notation:notation')									// titre
			);
	}
	return $boutons_admin;
}


?>
