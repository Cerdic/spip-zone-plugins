<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

include_spip('base/abstract_sql');
include_spip('inc/filtres');
/**
 * Decrire un profil
 * renvoie un tableau de ses infos
 *
 * @param int $id_auteur
 * @param bool $url
 * @return array
 */
function inc_profil_decrire_dist($id_auteur,$url = false){
	static $profils = array();
	if (!isset($profils[$id_auteur])
	OR ($url && !isset($profils[$id_auteur][$url]))) {
		$profils[$id_auteur] = sql_fetsel('nom,email,bio,pgp','spip_auteurs','id_auteur='.intval($id_auteur));
		if ($url){
			$profils[$id_auteur]['url'] = url_absolue(generer_url_entite($id_auteur,'auteur','','',false));
			$profils[$id_auteur]['nom_lien'] = "<a href='".generer_url_entite($id_auteur,'auteur','','',false)."'>"
			  .($profils[$id_auteur]['prenom']?$profils[$id_auteur]['prenom']:$profils[$id_auteur]['nom'])
			  ."</a>";
		}
	}
	return $profils[$id_auteur];
}

?>