<?php

/*
 * mots partout
 *
 * interface de gestion des mots clefs
 *
 * Auteur : Pierre Andrews (Mortimer)
 * � 2006 - Distribue sous licence GPL
 *
 */
function MotsPartout_ajouterBoutons($boutons_admin) {
  
  // on voit les bouton dans la barre "accueil"
  $boutons_admin['naviguer']->sousmenu["mots_partout"]= new Bouton(
																   "../"._DIR_PLUGIN_MOTSPARTOUT."/tag.png",  // icone
																   _L('motspartout:mots_partout') //titre
																   );
  return $boutons_admin;
}

function MotsPartout_ajouterOnglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['mots_partout']= new Bouton(
											  "../"._DIR_PLUGIN_MOTSPARTOUT."/tag.png", 'Configurer Mots Partout',
											  generer_url_ecrire("config_mots_partout"));
  return $flux;
}
function MotsPartout_afficherMots($flux) {
  	if($flux['args']['exec']=='mots_types'){
		$editer_mot = charger_fonction('editer_mot', 'inc');
		$flux['data'] .= $editer_mot('groupes_mot', $flux['args']['id_groupe'], $cherche_mot, $select_group, true);
	} elseif($flux['args']['exec']=='auteur_infos'){
		$editer_mot = charger_fonction('editer_mot', 'inc');
		$flux['data'] .= $editer_mot('auteur', $flux['args']['id_auteur'], $cherche_mot, $select_group, true);
	}
	return $flux;
}

?>
