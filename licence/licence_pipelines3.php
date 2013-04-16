<?php
/**
 * Plugin Licence
 * (c) 2007-2013 fanouch
 * Distribue sous licence GPL
 * 
 * @package SPIP\Licences\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline affiche_milieu (SPIP)
 * 
 * Insertion au centre des pages d'articles dans le privé
 * d'un formulaire d'édition de la licence de l'article
 *
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
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
 * Insertion dans le pipeline pre_insertion (SPIP)
 * 
 * Si création d'un nouvel article, on lui attribue la licence par défaut si
 * on utilise correctement les fonctions internes de SPIP pour créer des articles
 * cf : http://trac.rezo.net/trac/spip/browser/branches/spip-2.1/ecrire/action/editer_article.php#L214
 *
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline auquel on a ajouté la licence
 */
function licence_pre_insertion($flux){
	if ($flux['args']['table']=='spip_articles') {
		include_spip('inc/config');
		$licence_defaut = lire_config('licence/licence_defaut');
		$flux['data']['id_licence'] = $licence_defaut;
	}
	return $flux;
}


/**
 * Insertion dans le pipeline editer_contenu_objet (SPIP)
 * 
 * Ajout dans le formulaire d'édition de document du sélecteur de licence
 *
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array  $flux 
 * 		Le contexte du pipeline complété
 */
function licence_editer_contenu_objet($flux){
	if(in_array($flux['args']['type'],array('document'))){
		if(preg_match(",<li [^>]*class=[\"']editer editer_credits.*>(.*)<\/li>,Uims",$flux['data'],$regs)){
			include_spip('inc/licence');
			$ajouts = recuperer_fond('prive/licence_document_saisies',array('id_document'=>$flux['args']['id'],'licences' => $GLOBALS['licence_licences']));
			$flux['data'] = str_replace($regs[0],$ajouts.$regs[0],$flux['data']);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline xmlrpc_methodes (xmlrpc)
 * Ajout de méthodes xml-rpc spécifiques à Licence
 * 
 * @param array $flux
 * 		Un array des methodes déjà présentes, fonctionnant sous la forme :
 * 			-* clé = nom de la méthode;
 * 			-* valeur = le nom de la fonction à appeler;
 * @return array $flux
 * 		L'array complété avec nos nouvelles méthodes 
 */
function licence_xmlrpc_methodes($flux){
	$flux['spip.liste_licences'] = 'licence_liste_licences';
	return $flux;
}

/**
 * Insertion dans le pipeline xmlrpc_server_class (xmlrpc)
 * 
 * Ajout de fonctions spécifiques utilisées par le serveur xml-rpc
 * On inclu le fichier contenant les classes spécifiques
 * 
 * @param $flux
 * @return $flux
 */
function licence_xmlrpc_server_class($flux){
	include_spip('inc/licence_xmlrpc');
	return $flux;
}
?>