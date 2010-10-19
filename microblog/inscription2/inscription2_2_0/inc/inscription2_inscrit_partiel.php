<?php

/**
 * Vérification que nous sommes dans une inscription partielle
 * Est-ce un email connu à qui il manque des champs obligatoires
 * (inscrit partiellement par spip listes par ex) ?
 *
 * @param int $id
 * @return bool
 */
function i2_inscrit_partiel($id){
	/**
	 * Récupération de la liste des champs obligatoires
	 */
	$chercher_champs = charger_fonction('inscription2_champs_obligatoires','inc');
	$champs = $chercher_champs();

	/**
	 * Champs de l'inscrit dans les tables spip_auteurs et spip_auteurs_elargis
	 */
	$res = sql_fetsel("*","spip_auteurs as aut LEFT JOIN spip_auteurs_elargis as autelar USING(id_auteur)","aut.id_auteur = $id");

	foreach($champs as $val){
		/**
		 * Si un champs manque, on pose une session et on laisse passer
		 */
		if($res[$val]==''){
			i2_poser_session($id);
			return;
		}
	}
	return false;
}

?>