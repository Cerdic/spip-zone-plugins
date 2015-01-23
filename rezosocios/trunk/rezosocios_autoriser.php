<?php

/**
 * Autorisation de créer un rezosocio
 *
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_rezosocio_creer_dist($faire, $type, $id, $qui, $opt) {
	if ($qui['statut'] != '0minirezo' OR $qui['restreint'])
		return false;

	return true;
}

/**
 * Autorisation d'associer des mots à un objet
 *
 * Si groupe_champ ou id_groupe est fourni dans le tableau d'options,
 * on regarde les droits pour ce groupe en particulier
 *
 * On interdit aussi d'associer des mots à d'autres mots ou groupes de mots
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_associerrezosocios_dist($faire,$type,$id,$qui,$opt){
	// jamais de mots sur des mots
	if ($type=='rezosocio') return false;
	$droit = substr($qui['statut'],1);
	spip_log(lire_config('rezosocios/rezosocios_objets', array()),'test.'._LOG_ERREUR);
	return
		(in_array(table_objet_sql($type),lire_config('rezosocios/rezosocios_objets', array('spip_articles'))))
		AND (($id>0 AND autoriser('modifier', $type, $id, $qui, $opt))
			OR (
				$id<0
				AND abs($id) == $qui['id_auteur']
				AND autoriser('ecrire', $type, $id, $qui, $opt)
			)
		);
	return false;
}
?>