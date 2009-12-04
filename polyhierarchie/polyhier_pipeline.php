<?php
/*
 * Plugin Polyhierarchie
 * (c) 2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */

/* pour que le pipeline ne rale pas ! */
function polyhier_autoriser(){}

function polyhier_affiche_hierarchie($flux){
	$objet = $flux['args']['objet'];
	if (in_array($objet,array('article','rubrique'))){
		$id_objet = $flux['args']['id_objet'];
		include_spip('inc/polyhier');
		$parents = polyhier_get_parents($id_objet,$objet,$serveur='');
		$out = array();
		foreach($parents as $p)
			$out[] = "[->rubrique$p]";
		if (count($out)){
			$out = implode(', ',$out);
			$out = _T('polyhier:label_autres_parents')." ".$out;
			$out = PtoBR(propre($out));
			$flux['data'] .= "<div id='chemins_transverses'>$out</div>";
		}

	}
	return $flux;
}


function polyhier_affiche_droite($flux){
	if ($flux['args']['exec']=='naviguer'
		AND $id_rubrique = $flux['args']['id_rubrique']){
		$flux['data'] .= $test=recuperer_fond("prive/contenu/rubrique-enfants-indirects",$_GET, array('ajax'=>true));
	}
	return $flux;
}

/**
 * Pipeline pour charger les parents transverses dans le formulaire
 * d'edition article et rubrique
 * 
 * @param array $flux
 * @return array 
 */
function polyhier_formulaire_charger($flux){
	$form = $flux['args']['form'];
	if (
		($objet = $flux['data']['_polyhier'] AND in_array($objet,array('article','rubrique')))
		OR ($objet = substr($form,7) AND in_array($form,array('editer_article','editer_rubrique')))
		){
		// On ne fait rien si l'id_parent principal est incoherent (exemple : compat pages uniques)
		if ($flux['data']['id_parent'] < 0) return $flux;
		
		$id_table_objet = id_table_objet($objet);

		// on met en tete l'id_parent principal
		// pour unifier la saisie
		$flux['data']['parents'] = array("rubrique|".$flux['data']['id_parent']);
		if ($id_objet = intval($flux['data'][$id_table_objet])){
			include_spip('inc/polyhier');
			$parents = polyhier_get_parents($id_objet,$objet,$serveur='');
			foreach($parents as $p)
				$flux['data']['parents'][] = "rubrique|$p";
		}
		$flux['data']['_hidden'] .= "<input type='hidden' name='_polyhier' value='$objet' />";
	}
	return $flux;
}


/**
 * Pipeline pour verifier les parents transverses dans le formulaire
 * d'edition article et rubrique
 * 
 * @param array $flux
 * @return array 
 */
function polyhier_formulaire_verifier($flux){
	$form = $flux['args']['form'];
	if ($objet = _request('_polyhier')
		AND in_array($objet,array('article','rubrique'))){
		// On ne fait rien si l'id_parent principal est incoherent (exemple : compat pages uniques)
		if (_request('id_parent') < 0) return $flux;
		
		$id_table_objet = id_table_objet($objet);

		// on verifie qu'au moins un parent est present si c'est un article
		if (!count(_request('parents')) AND $objet=='article'){
			$flux['data']['parents'] = _T('polyhier:parent_obligatoire');
			set_request('parents',array()); // eviter de revenir au choix initial
		}
		// sinon, c'est ok, on rebascule le premier parent[] dans id_parent
		// ou on est a la racine..
		else {
			$id_parent = _request('parents');
			$id_parent = explode('|',is_array($id_parent)?reset($id_parent):"rubrique|0");
			set_request('id_parent',intval(end($id_parent)));
		}

	}
	return $flux;
}

/**
 * Inserer le selecteur de rubriques trasnverses dans les formulaires d'edition
 * article et rubrique
 *
 * @param string $flux
 * @return string
 */
function polyhier_editer_contenu_objet($flux){
	$args = $flux['args'];
	$type = $args['type'];
	if (in_array($type,array('rubrique','article'))){
		// On ne fait rien si l'id_parent principal est incoherent (exemple : compat pages uniques)
		if ($args['contexte']['id_parent'] < 0) return $flux;
		
		$saisie = "<script type='text/javascript'>jQuery(function() {jQuery('li.editer_parent').remove();});</script>";
		$saisie .= recuperer_fond("formulaires/inc-selecteur-parents",$args['contexte']);
		if (strpos($flux['data'],'<!--polyhier-->')!==FALSE)
			$flux['data'] = preg_replace(',(.*)(<!--polyhier-->),ims',"\\1$saisie\\2",$flux['data'],1);
		elseif (preg_match(",<li [^>]*class=[\"']editer_(descriptif|virtuel|chapo|liens_sites|texte),Uims",$flux['data'],$regs)){
			$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer_".$regs[1]."),Uims",$saisie."\\1",$flux['data'],1);
		}
		elseif (strpos($flux['data'],'<!--extra-->')!==FALSE)
			$flux['data'] = preg_replace(',(.*)(<!--extra-->),ims',"\\1$saisie\\2",$flux['data'],1);
		else
			$flux['data'] = preg_replace(',(.*)(</fieldset>),ims',"\\1\\\$saisie",$flux['data'],1);
	}
	return $flux;
}

/**
 * Appliquer les changements de polyhierarchie apres edition d'une rubrique ou
 * d'un article
 *
 * @param array $flux
 * @return array
 */
function polyhier_pre_edition($flux){
	$objet = $flux['args']['type'];
	if (_request('_polyhier') AND in_array($objet,array('article','rubrique'))){
		$id_objet = $flux['args']['id_objet'];
		$serveur = $flux['args']['serveur'];
		$id_parents = _request('parents');
		$id_parent = _request('id_parent');
		if (!$id_parents)
			$id_parents = array();

		$ids = array();
		foreach($id_parents as $sel){
			$sel = explode("|",$sel);
			if (reset($sel)=='rubrique')
				$ids[] = intval(end($sel));
		}
		$id_parents = array_diff($ids,array($id_parent));

		include_spip('inc/polyhier');
		polyhier_set_parents($id_objet,$objet,$id_parents,$serveur);
	}

	return $flux;
}
?>
