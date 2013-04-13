<?php
/**
 * Plugin Licence
 * (c) 2007-2012 fanouch
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion au centre des pages d'articles dans le privé
 * Affiche un formulaire d'édition de la licence de l'article
 *
 * @param array $flux Le contexte du pipeline
 */
function licence_affiche_milieu($flux) {

	if ($flux['args']['exec'] == 'article'){
		$contexte['id_article'] = $flux["args"]["id_article"];
		
		$texte = recuperer_fond('prive/squelettes/contenu/licence_article3',$contexte,array('ajax'=>true));
		
		if (($p = strpos($flux['data'],'<!--affiche_milieu-->'))!==false)
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	return $flux;
}

/**
 * Si création d'un nouvel article, on lui attribue la licence par défaut si
 * on utilise correctement les fonctions internes de SPIP pour créer des articles
 * cf : http://trac.rezo.net/trac/spip/browser/branches/spip-2.1/ecrire/action/editer_article.php#L214
 *
 * @param array $flux Le contexte du pipeline
 */
function licence_pre_insertion($flux){
	// si creation d'un nouvel article lui attribuer la licence par defaut de la config
	if ($flux['args']['table']=='spip_articles') {
		include_spip('inc/config');
		$licence_defaut = lire_config('licence/licence_defaut');
		$flux['data']['id_licence'] = $licence_defaut;
	}
	return $flux;
}


/**
 * Insertion dans le pipeline editer_contenu_objet
 *
 * @param array $flux Le contexte du pipeline
 * @return $flux Le contexte du pipeline complété
 */
function licence_editer_contenu_objet($flux){
	$type_form = $flux['args']['type'];
	$id_document = $flux['args']['id'];
	if(in_array($type_form,array('document'))){
		if(preg_match(",<li [^>]*class=[\"']editer editer_credits.*>(.*)<\/li>,Uims",$flux['data'],$regs)){
			include_spip('inc/licence');
			$ajouts = recuperer_fond('prive/licence_document_saisies',array('id_document'=>$id_document,'licences' => $GLOBALS['licence_licences']));
			$flux['data'] = str_replace($regs[0],$ajouts.$regs[0],$flux['data']);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition
 * Récupération de l'id_licence lors de la validation du formulaire de documents
 *
 * @param array $flux Le contexte du pipeline
 * @return $flux Le contexte du pipeline complété
 */
function licence_pre_edition($flux){
	if(($flux['args']['type'] == 'document') && ($flux['args']['action'] == 'modifier') && _request('id_licence')){
		$flux['data']['id_licence'] = _request('id_licence');
	}
	return $flux;
}

/**
 * Insertion dans le pipeline xmlrpc_methodes (xmlrpc)
 * Ajout de méthodes xml-rpc spécifiques à Licence
 * 
 * @param array $flux : un array des methodes déjà présentes, fonctionnant sous la forme :
 * -* clé = nom de la méthode;
 * -* valeur = le nom de la fonction à appeler;
 * @return array $flux : l'array complété avec nos nouvelles méthodes 
 */
function licence_xmlrpc_methodes($flux){
	$flux['spip.liste_licences'] = 'licence_liste_licences';
	return $flux;
}

/**
 * Insertion dans le pipeline xmlrpc_server_class (xmlrpc)
 * Ajout de fonctions spécifiques utilisées par le serveur xml-rpc 
 */
function licence_xmlrpc_server_class($flux){
	include_spip('inc/licence_xmlrpc');
	return $flux;
}
?>