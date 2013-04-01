<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012-2013 kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Licence GNU/GPL
 * 
 * Déclaration des pipelines utilisés par le plugin collections (hors déclaration des tables)
 * 
 * @package SPIP\Collections\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline affiche_milieu (SPIP)
 *
 * Ajout de contenu sur certaines pages de l'espace privé
 * Ajoute le formulaire d'ajout d'auteurs sur la page des collections
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline complété
 */
function collections_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les collections
	if (!$e['edition'] AND in_array($e['type'], array('collection'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}

/**
 * Insertion dans le pipeline affiche_auteurs_interventions (SPIP)
 * 
 * Ajout de la liste liste des collections auxquelles l'auteur est lié
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline complété
 */
function collections_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/collections', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('collection:info_collections_auteur')
		), array('ajax' => true));

	}
	return $flux;
}

/**
 * Insertion dans le pipeline editer_contenu_objet (SPIP)
 * 
 * On ajoute simplement le selecteur de collections dans le formulaire après le sélecteur de rubriques
 * sur l'édition des médias
 * 
 * @param array $flux 
 * 		Le contexte d'environnement du pipeline
 * @return array $flux 
 * 		Le contexte du pipeline complété
 */
function collections_editer_contenu_objet($flux){
	$args = $flux['args'];
	$type = $args['type'];
	$pipeline = pipeline('diogene_objets', array());

	if (in_array($type,array_keys($pipeline)) && $type == 'article'){
		$id_secteur = $args['contexte']['id_secteur'] ?
			$args['contexte']['id_secteur'] :
			sql_getfetsel('id_secteur','spip_rubriques','id_rubrique='.intval($args['contexte']['id_parent']));

		/**
		 * Cas des pages uniques
		 * On sort de suite
		 */
		if(!$id_secteur && (($args['contexte']['id_parent'] == 0) OR ($args['contexte']['id_parent'] == '-1') OR (!$args['contexte']['id_parent']  && !$args['contexte']['parents'])) && ($type=='article')){
			return $flux;
		}
		if($type == 'article'){
			if($id_diogene = intval(_request('id_diogene'))){
				$where = "id_diogene = ".intval($id_diogene)." AND id_secteur=".intval($id_secteur)." AND objet IN ('article','emballe_media')";
			}else{
				$where = "id_secteur=".intval($id_secteur)." AND objet IN ('article','emballe_media')";
			}
		}
		if($diogene = sql_fetsel('*','spip_diogenes',$where)){
			if(is_array(unserialize($diogene['champs_ajoutes'])) && in_array('collection',unserialize($diogene['champs_ajoutes']))){
				$saisie_collection = recuperer_fond('formulaires/diogene_ajouter_medias_collection',$args['contexte']);
				$test = preg_match(",(<li [^>]*class=[\"']editer editer_parents.*)(<li [^>]*class=[\"']editer.*),Uims",$matches);
				$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer (editer_id_parent|editer_parents).*)(<li [^>]*class=[\"']editer.*),Uims","\\1".$saisie_collection."\\3",$flux['data'],1);
			}
		}
	}
    return $flux;
}

/**
 * Insertion dans le pipeline optimiser_base_disparus (SPIP)
 * 
 * Optimiser la base de donnees en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @param int $n
 * @return int
 */
function collections_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('collection'=>'*'),'*');
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * 
 * On ajoute l'id_collection dans les champs chargés
 * 
 * Utilisé au moins dans le traitement par lot d'emballe medias dans le cas d'erreurs
 * dans le formulaire
 * 
 * On test $flux['data'] pour éviter de planter dans le privé dans les formulaires editer_liens
 * si le charger renvoit false
 * 
 * @param array $flux 
 * 		Le contexte d'environnement
 * @return array $flux 
 * 		L'environnement modifié si besoin
 */
function collections_formulaire_charger($flux){
	if($flux['data'] && _request('id_collection'))
		$flux['data']['id_collection'] = _request('id_collection');
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * 
 * Insertion à la fin du traitement des formulaires
 * 
 * On ne s'insère que dans l'espace public
 * Dans le cas de l'édition d'une collection, si un diogène de collection existe
 * et que l'on dispose d'un id_collection dans l'environnement,
 * on affiche un message de retour avec un lien pour voir la collection,
 * on en profite pour recharger en ajax les blocs :
 * - .description_collection
 * - #menu_diogene_$id_diogene
 * 
 * @param array $flux 
 * 		Le contexte d'environnement du pipeline
 * @return array $flux 
 * 		Le contexte d'environnement modifié
 */
function collections_formulaire_traiter($flux){
	if(!test_espace_prive()){
		if(
			$flux['args']['form'] == 'editer_collection'
			&& $id_diogene=sql_getfetsel('id_diogene','spip_diogenes','objet="collection"') 
		){
			if(isset($flux['data']['redirect'])){
				if($flux['data']['redirect'] == generer_url_entite($flux['data']['id_collection'],'collection')){
					if(_request('id_collection') == $flux['data']['id_collection']){
						$flux['data']['redirect'] = false;
						$flux['data']['editable'] = true;
						$flux['data']['message_ok'] .= '<script type="text/javascript">if (window.jQuery) jQuery(".description_collection,#menu_diogene_'.$id_diogene.'").ajaxReload();</script>';
					}else{
						$flux['data']['redirect'] = parametre_url(self(),'id_collection',$flux['data']['id_collection']);
					}
				}
			}else{
				$flux['data']['message_ok'] .= '<script type="text/javascript">if (window.jQuery) jQuery(".description_collection,#menu_diogene_'.$id_diogene.'").ajaxReload();</script>';
				$flux['data']['editable'] = true;
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * 
 * Ajoute des styles par défaut pour les collections (css/collections.css) 
 * dans le head des pages publiques
 * 
 * @param string $flux 
 * 		Le contenu textuel du pipeline
 * @return string $flux 
 * 		Le contenu textuel modifié du pipeline
 */
function collections_insert_head_css($flux){
	$flux .= '
<link rel="stylesheet" href="'.direction_css(find_in_path('css/collections.css')).'" type="text/css" media="all" />
';
	return $flux;
}

/**
 * Insertion dans le pipeline collections_liste_types (plugin collections)
 * 
 * Ajoute les deux types de collections possibles :
 * - coopérative
 * - personnelle
 *
 * @param array $flux
 * 		La liste des types de collections disponibles
 * @return array $flux
 * 		La liste des types de collections complétée
 */
function collections_collections_liste_types($flux){
	if(!is_array($flux))
		$flux = array();
	
	$flux['perso'] = _T('collection:type_perso');
	$flux['coop'] = _T('collection:type_coop');
	
	return $flux;
}


/**
 * Insertion dans le pipeline collections_liste_genres (plugin collections)
 *
 * Ajoute les quatre genres de collections possibles :
 * - mixed
 * - image
 * - audio
 * - video
 *
 * @param array $flux
 * 		La liste des genres possibles
 * @return array
 * 		La liste des genres complétés
 */
function collections_collections_liste_genres($flux){
	if(!is_array($flux))
		$flux = array();
	
	$flux['mixed'] = _T('collection:genre_mixed');
	$flux['image'] = _T('collection:genre_photo');
	$flux['audio'] = _T('collection:genre_musique');
	$flux['video'] = _T('collection:genre_video');
	
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_objets (plugin Diogene)
 * 
 * On ajoute les champs qui peuvent être pris en compte pour les collections
 *
 * @param array $flux 
 * 		Un tableau des champs déjà ajouté
 * @return array $flux 
 * 		Le tableau modifié
 */
function collections_diogene_objets($flux){
	$flux['collection']['diogene_max'] = 1;
	$flux['collection']['ss_rubrique'] = 1;
	if(defined('_DIR_PLUGIN_DIOGENE_SPIPICIOUS')){
		$flux['collection']['champs_sup']['spipicious'] = _T('diogene_spipicious:tags_spipicious');
	}
	if(defined('_DIR_PLUGIN_DIOGENE_MOTS')){
		$flux['collection']['champs_sup']['mots'] = _T('diogene_mots:form_legend');
	}
	if(defined('_DIR_PLUGIN_DIOGENE_GERER_AUTEURS')){
		$flux['collection']['champs_sup']['auteurs'] = _T('diogene_gerer_auteurs:label_cfg_ajout_auteurs');
	}
	
	$flux['emballe_media']['champs_sup']['collection'] = _T('collection:diogene_champ_collection');
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_traiter (plugin Diogene)
 * 
 * On associe le media à la collection sélectionnée s'il y a lieu
 * (voir pipeline editer_contenu_objet)
 * 
 * @param array $flux 
 * 		Le contexte d'environnement
 * @return array $flux 
 * 		Le contexte d'environnement modifié si besoin
 */
function collections_diogene_traiter($flux){
	if(($id_collection = _request('id_collection')) && is_numeric($flux['args']['id_objet']) && ($flux['args']['id_objet'] > 0)){
		if(is_numeric($id_collection)){
			include_spip('action/editer_liens');
			if (autoriser('modifier', 'article', $flux['args']['id_objet']))
				objet_associer(array('collection' => $id_collection), array('article' => $flux['args']['id_objet']));
		}
	}
	return $flux;
}

?>