<?php
/**
 * Déclaration de bdd du plugin Authentification SAML
 *
 * @plugin     Authentification SAML
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\SimpleSaml\Sql\Tables
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajouter le champ 'nameid' à la table auteurs
 * 
 * @param array $tables_principales
 * @return array
 */
function simplesaml_declarer_tables_objets_sql($tables){
	$tables['spip_auteurs']['field']['nameid'] = "text DEFAULT '' NOT NULL";
	return $tables;
}
