<?php
function op_ajouterBouton($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo") {
    // on voit le bouton dans la barre "naviguer"
	    $boutons_admin['configuration']->sousmenu['op']= new Bouton(
		    '../'._DIR_PLUGINS.'openPublishing/images/opconfig-24.gif', _T('Configurer openPublishing'));
	}
	return $boutons_admin;
}

function op_ajouterOnglets($flux) {
  if($flux['args']=='op')
	{
	$flux['data']['voir']= new Bouton(null, 'Voir la configuration',
											  generer_url_ecrire("op"));
	$flux['data']['modifier']= new Bouton(null, 'Modifier la configuration',
											  generer_url_ecrire("op_modifier","action=creer"));
	$flux['data']['effacer']= new Bouton(null, 'Supprimer Open-Publishing',
											  generer_url_ecrire("op_effacer"));
	}
	return $flux;
}

function op_headerPrive($texte) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	$texte.= '<link rel="stylesheet" type="text/css" href="' . (_DIR_PLUGINS.end($p)) . '/css/op.css" />' . "\n";
	return $texte;
}
?> 
