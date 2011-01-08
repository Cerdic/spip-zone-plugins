<?php
/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

/**
 * Retourne une liste d'objets lies a un objet source indique
 * Si le visiteur en a les droits un lien pour les délier 
 * et un formulaire pour lier de nouveaux objets 
 *
 * @param string $objet nom de l'objet : auteur,rubrique (ou aussi 'auteurs','spip_auteurs', qui seront corriges)
 * @param string $source nom de l'objet source
 * @param int $id_source identifiant de l'objet source
 * @param string $titre_bouton titre de la boite, par defaut : <:grappes:info_lier_$objet:>
 * @return 
**/
function inc_grappes_lister_objets_dist($objet, $source, $id_source, $titre_bouton=''){
		$objet = table_objet($objet);
		$source = table_objet($source);
		$contexte = array(
			'titre_bouton'=> $titre_bouton ? $titre_bouton : _T('grappes:info_lier_'.$objet),
			'objet' => $objet,
			'source'=>$source,
			'id_source'=> $id_source,
			'id_table_source'=>id_table_objet($source),
		);
		return recuperer_fond("prive/listes/lister_objets", $contexte);	
}

?>
