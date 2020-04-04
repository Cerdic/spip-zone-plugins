<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

/* pour que le pipeline ne rale pas ! */
function agenda_autoriser(){}

function autoriser_evenement_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	if (isset($opt['contexte']['id_article']))
		return autoriser('creerevenementdans','article',$opt['contexte']['id_article'],$qui);
	return true;
}

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
	// si on a le droit de modifier l'article alors on a peut-etre le droit d'y creer un evenement
	$afficher = false;
	if (autoriser('modifier','article',$id,$qui)) {
		$afficher = true;
		// un article avec des evenements a toujours le droit
		if (!sql_countsel('spip_evenements','id_article='.intval($id))){
			// si au moins une rubrique a le flag agenda
			if (sql_countsel('spip_rubriques','agenda=1')){
				// alors il faut le flag agenda dans cette branche !
				$afficher = false;
				include_spip('inc/agenda_gestion');
				$in = calcul_hierarchie_in(sql_getfetsel('id_rubrique','spip_articles','id_article='.intval($id)));
				$afficher = sql_countsel('spip_rubriques',sql_in('id_rubrique',$in)." AND agenda=1");
			}
		}
	}
	return $afficher;
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