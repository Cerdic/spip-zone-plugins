<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

/* pour que le pipeline ne rale pas ! */
function agenda_autoriser(){}

/**
 * Autorisation d'ajout d'un evenement a un article
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param int $qui
 * @param array $options
 * @return bool
 */
function autoriser_article_creerevenementdans_dist($faire,$quoi,$id,$qui,$options){
	if (!$id) return false; // interdit de creer un evenement sur un article vide !
	// si on a le droit de modifier l'article alors on a le droit d'y creer un evenement !
	return autoriser('modifier','article',$id);
}

/**
 * Autorisation de modifier un evenement : autorisations de l'article parent
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param int $qui
 * @param array $options
 * @return bool
 */
function autoriser_evenement_modifier_dist($faire,$quoi,$id,$qui,$options){
	if (!isset($options['id_article']) OR !$id_article=$options['id_article'])
		$id_article = sql_getfetsel('id_article','spip_evenements','id_evenement='.intval($id));
	if (!$id_article) return false;
	return autoriser('modifier','article',$id_article,$qui);
}

/**
 * Autorisation de voir un evenement : autorisations de l'article parent
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param int $qui
 * @param array $options
 * @return bool
 */
function autoriser_evenement_voir_dist($faire,$quoi,$id,$qui,$options){
	if (!isset($options['id_article']) OR !$id_article=$options['id_article'])
		$id_article = sql_getfetsel('id_article','spip_evenements','id_evenement='.intval($id));
	if (!$id_article) return false;
	return autoriser('voir','article',$id_article,$qui);
}


/**
 * Autorisation de supprimer un evenement : autorisations de l'article parent
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id
 * @param int $qui
 * @param array $options
 * @return bool
 */
function autoriser_evenement_supprimer_dist($faire,$quoi,$id,$qui,$options){
	if (!isset($options['id_article']) OR !$id_article=$options['id_article'])
		$id_article = sql_getfetsel('id_article','spip_evenements','id_evenement='.intval($id));
	if (!$id_article) {
		if ($qui['statut']=='0minirezo')
			return true;
		else
			return false;
	}
	return autoriser('modifier','article',$id_article,$qui);
}

?>