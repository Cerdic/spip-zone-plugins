<?php
/**
 * Plugin auteurs partout
 * (c) 2012 cy_altern
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Gestion des objets pour lesquels il ne faut pas ajouter d'auteur
 * en gros:
 *  - les articles parce qu'ils les gèrent déja
 *  - les rubriques parce que les auteurs sont liés au mécanisme de gestion des admins restreints
 *  - tous les objets pour lesquels la notion d'auteur n'est pas pertinente ou deja geree (messages)
 *
 * @return array des tables des types à exclure
 */
function auteurspartout_types_exclus(){
	return array('spip_articles', 'spip_rubriques', 'spip_messages');
}
