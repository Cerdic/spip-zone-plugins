<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2010 - cmtmt, BoOz, kent1
 * Licence GPL v3
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Vérification que nous sommes dans une inscription partielle
 * Est-ce un email connu à qui il manque des champs obligatoires
 * (inscrit partiellement par spip listes par ex) ?
 *
 * @param int $id
 * @return bool
 */
function i3_inscrit_partiel($id){
	/**
	 * Récupération de la liste des champs obligatoires
	 */
	$chercher_champs = charger_fonction('inscription3_champs_obligatoires','inc');
	$champs = $chercher_champs();

	/**
	 * Champs de l'inscrit dans la table spip_auteurs
	 */
	$res = sql_fetsel("*","spip_auteurs","id_auteur = $id");

	foreach($champs as $val){
		/**
		 * Si un champs manque, on pose une session et on laisse passer
		 */
		if($res[$val]==''){
			i3_poser_session($id);
			return;
		}
	}
	return false;
}

?>