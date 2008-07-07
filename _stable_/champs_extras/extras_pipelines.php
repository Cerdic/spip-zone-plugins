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

function extras_afficher_contenu_objet($flux){
	if ($GLOBALS['champs_extra']){
		include_spip('base/abstract_sql');
		$type = $flux['args']['type'];
		$table_objet = table_objet($type);
		$id_table_objet = id_table_objet($type);		
		$id_objet = $flux['args']['id_objet'];
		$spip_table_objet = table_objet_sql($type);
			
		$extra = sql_getfetsel('extra',$spip_table_objet,"$id_table_objet=".intval($id_objet));
		include_spip('inc/extra');
		$flux['data'].= extra_affichage($extra,$table_objet);
	}
	
	return $flux;
}
function extras_afficher_revision_objet($flux){
	return extras_afficher_contenu_objet($flux); // pas de revisions sur les extras
}

function extras_editer_contenu_objet($flux){
	if ($GLOBALS['champs_extra']){
		$args = $flux['args'];
		include_spip('inc/extra');
		$type_extra = table_objet($args['type']);
		$extra_saisie = extra_saisie($args['contexte']['extra'],$type_extra,$args['contexte']['id_secteur']);
		if (strpos($flux['data'],'<!--extra-->')!==FALSE)
			$flux['data'] = preg_replace(',(.*)(<!--extra-->),ims',"\\1$extra_saisie\\2",$flux['data'],1);
		else
			$flux['data'] = preg_replace(',(.*)(</fieldset>),ims',"\\1\\\2$extra_saisie",$flux['data'],1);
	}
	return $flux;
}

function extras_pre_edition($flux){
	$table_objet = $flux['args']['table_objet'];
	$id_objet = $flux['args']['id_objet'];

	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table_objet, $serveur);
	
	// recuperer les extras (utilise $_POST, un peu sale...
	// a voir pour le faire marcher avec les crayons)
	if (isset($desc['field']['extra'])
	AND isset($_POST['extra'])
	AND $GLOBALS['champs_extra']) {
		include_spip('inc/extra');
		$extra = extra_update($table_objet, $id_objet, $_POST);
		if ($extra !== false)
			$flux['data']['extra'] = $extra;
	}

	return $flux;
}


?>