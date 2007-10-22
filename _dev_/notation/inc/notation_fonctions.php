<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Ajouter un bouton dans la barre forum
*  
**/

/* Gestion des notations dans les forums
*  Supprime [notation]
*  Transforme les [+] et [-] en images
*/
function notation_critique($p)
{ $p = preg_replace('/\[notation\]/', '', $p);
  $p = preg_replace('/\[\+\]/', '<img src="'.find_in_path('img_pack/notation-plus.gif').'"> ', $p);
  $p = preg_replace('/\[-\]/', '<img src="'.find_in_path('img_pack/notation-moins.gif').'"> ', $p);
  return $p;
}

// Ajout des boutons dans l'interface privee
function notation_ajouterBoutons($boutons_admin) 
{	// si on est admin
	// if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) 
	{	// Bouton dans la barre "forum"
		$boutons_admin['forum']->sousmenu['notation'] = 
			new Bouton(	"../"._DIR_PLUGIN_NOTATION."img_pack/notation.png",	// icone
						_T('notation:notation')									// titre
			);
	}
	return $boutons_admin;
}

function notation_ajouterOnglets($flux) 
{	$rubrique = $flux['args'];
	return $flux;
}

?>