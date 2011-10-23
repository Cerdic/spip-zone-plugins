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
		'spip_asso_membres' => _T('asso:membres'),
		
	));
}
?>
