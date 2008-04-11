<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


// Cette fonction produit le contenu du formulaire
// d'edition de groupe de mot
function inc_editer_groupemots_dist($id_groupe, $contexte=array()){
	include_spip('public/assembler');
	//. aide("motsgroupes")
	$form = recuperer_fond("prive/editer/groupe_mots", $contexte);
	$form = pipeline(
		'editer_contenu_objet',
		array(
			'data'=>$form,
			'args'=>array('type'=>'groupemot','id'=>$id_groupe,'contexte'=>$contexte)
		)
	);		
	return redirige_action_auteur('instituer_groupe_mots', $id_groupe, "mots_tous", "id_groupe=$id_groupe", $form);	
}

?>
