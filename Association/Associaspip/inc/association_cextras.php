<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Interface avec le plugin "Champs Extras 2" : ajout des objets
 * membres
 * a la liste des objets pouvant recevoir des champs extras...
**/
function association_objets_extensibles($objets){
	return array_merge($objets, array(
		'asso_membre' => _T('asso:membres'), // Adherent(e)s/Membres
		'asso_compte' => _T('asso:comptes'), // Grand Livre/Journal Comptable
		'asso_activite' => _T('asso:activites'), // Inscriptions et Participations financiere des membres aux activites
		'asso_ressource' => _T('asso:ressources'), // Ressources (par ex. livres) pretes
	));
}

?>