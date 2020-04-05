<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/* pour que le pipeline ne rale pas ! */
function legendes_autoriser(){}

/**
 * Autorisation d'ajout d'une legende a un document
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id du document
 * @param int $qui
 * @param array $options
 * @return bool
 */
function autoriser_legende_creerdans_dist($faire,$quoi,$id,$qui,$options){
	if (!$id) return false; // interdit de creer une legende sur un document vide !
	// autorisation personnalisee par config
	if(lire_config('legendes/statuts_creerdans') && $qui['statut'])
		return  ($qui['statut']<=lire_config('legendes/statuts_creerdans','0minirezo'));
	// ou autorisation du document associe
	return autoriser('modifier','document',$id,$qui);
}

/**
 * Autorisation de modifier une legende
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id de la legende
 * @param int $qui
 * @param array $options
 * @return bool
 */
function autoriser_legende_modifier_dist($faire,$quoi,$id,$qui,$options){
	if (!isset($options['id_document']) OR !$id_document=$options['id_document'])
		$legende = sql_fetsel('*','spip_legendes','id_legende='.intval($id));
	if (!$legende['id_document']) return false;
	// autorisation personnalisee par config
	if (lire_config('legendes/statuts_modifier') && $qui['statut']){
		if ($qui['statut'] <= lire_config('legendes/statuts_modifier','0minirezo')){
			if (($qui['statut'] != '0minirezo') AND ($qui['id_auteur'] != $legende['id_auteur'])) {
				return false;
			}
		return true;
		}
	}
	// ou autorisation du document associe
	return autoriser('modifier','document',$legende['id_document'],$qui);
}


/**
 * Autorisation de supprimer une legende
 *
 * @param string $faire
 * @param string $quoi
 * @param int $id de la legende
 * @param int $qui
 * @param array $options
 * @return bool
 */
function autoriser_legende_supprimer_dist($faire,$quoi,$id,$qui,$options){
	if (!isset($options['id_document']) OR !$id_document=$options['id_document'])
		$id_document = sql_getfetsel('id_document','spip_legendes','id_legende='.intval($id));
	// autorisation personnalisee par config
	if (lire_config('legendes/statuts_supprimer') && $qui['statut']){
		if ($qui['statut'] <= lire_config('legendes/statuts_supprimer','0minirezo')){
			$legende = sql_getfetsel('id_auteur','spip_legendes','id_legende='.intval($id));
			if (($qui['statut'] != '0minirezo') AND ($qui['id_auteur'] != $legende['id_auteur'])) {
				return false;
			}
		return true;
		}
	}
	// ou autorisation du document associe
	return autoriser('modifier','document',$id_document,$qui);
}

?>