<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
function formulaires_editer_asso_groupes_charger_dist($id_groupe='') {
	/* cet appel va charger dans $contexte tous les champs de la table spip_asso_dons associes a l'id_don passe en param */
	return formulaires_editer_objet_charger('asso_groupes', $id_groupe, '', '',  generer_url_ecrire('groupes'), '');	
}

function formulaires_editer_asso_groupes_traiter($id_groupe='') {
	return formulaires_editer_objet_traiter('asso_groupes', $id_groupe, '', '',  generer_url_ecrire('groupes'), '');
}
?>
