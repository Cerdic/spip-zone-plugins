<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_i2_configuration_initiale_dist(){
ecrire_meta(
			'inscription2',
				serialize(array(
					'nom' => 'on',
					'nom_obligatoire' => 'on',
					'nom_fiche_mod' => 'on',
					'email' => 'on',
					'email_obligatoire' => 'on',
					'nom_famille' => 'on',
					'nom_famille_table' => 'on',
					'prenom' => 'on',
					'prenom_table' => 'on',
					'login' => 'on',
					'login_fiche_mod' => 'on',
					'adresse' => 'on',
					'adresse_fiche_mod' => 'on',
					'code_postal' => 'on',
					'code_postal_fiche_mod' => 'on',
					'ville' => 'on',
					'ville_fiche_mod' => 'on',
					'ville_table' => 'on',
					'telephone' => 'on',
					'telephone_fiche_mod' => 'on',
					'statut_nouveau' => '6forum',
					'statut_interne' => ''
				))
			);
return '';
}
?>