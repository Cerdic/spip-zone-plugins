<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

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
