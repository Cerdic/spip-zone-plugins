<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012-2013 kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Licence GNU/GPL
 * 
 * Gestion de l'action editer_collection et de l'API d'édition d'une collection
 * 
 * @package SPIP\Collections\Collections\Edition
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action d'édition d'une collection dans la base de données dont
 * l'identifiant est donné en paramètre de cette fonction ou
 * en argument de l'action sécurisée
 *
 * Si aucun identifiant n'est donné, on crée alors une nouvelle collection,
 * à condition que l'on soit autorisé à en créer une
 * 
 * On utilise l'API d'édition des objets de action/editer_objet pour les modifications
 * de collections
 *  
 * @param null|int $arg
 *     Identifiant de la collection. En absence utilise l'argument
 *     de l'action sécurisée.
 * @return array
 *     Liste (identifiant de la collection, Texte d'erreur éventuel)
 */
function action_editer_collection_dist($arg=null) {
	include_spip('inc/autoriser');
	
	$err="";
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	/**
	 * Si id_collection n'est pas un nombre, c'est une création
	 * On vérifie que l'on ai le droit d'en créer une
	 */
	if (!$id_collection = intval($arg)) {
		if(!autoriser('creer','collection'))
			$err = _T('collection:info_non_autorise_creer_collection',array('id_auteur'=>$GLOBALS['visiteur_session']['id_auteur']));
		else
			$id_collection = collection_inserer();
	}

	/**
	 * Enregistre l'envoi dans la BD
	 */ 
	if ($id_collection > 0){
		if(autoriser('modifier','collection',$id_collection)){
			include_spip('action/editer_objet');
			$err = objet_modifier('collection',$id_collection);
		}else{
			$err = _T('collection:info_non_autorise_modifier_collection',array('id_auteur'=>$GLOBALS['visiteur_session']['id_auteur'],'id'=> $id_collection));		
		}
	}

	if ($err)
		spip_log("echec editeur collection: $err",_LOG_ERREUR);

	return array($id_collection,$err);
}

/**
 * Insérer une nouvelle collection en base de données
 * 
 * En plus des données enregistrées par défaut (statut, date), la fonction :
 * - enregistre l'id_auteur de l'auteur créant la collection en tant que id_admin de
 *   la table spip_collections
 * - crée une liaison automatiquement entre l'auteur connecté et la collection
 *   créée, de sorte que la personne devient par défaut auteur de la collection
 *   qu'elle crée.
 *   
 * @pipeline_appel pre_insertion
 * @pipeline_appel post_insertion
 *
 * @global array $GLOBALS['visiteur_session']
 * 
 * @return int
 *     Identifiant de la nouvelle collection
 * 
 */
function collection_inserer() {
	$champs = array(
		'statut' => 'publie',
		'date' => date('Y-m-d H:i:s')
	);
	
	$id_auteur = (is_null(_request('id_auteur'))?$GLOBALS['visiteur_session']['id_auteur']:_request('id_auteur'));
	
	if($id_auteur)
		$champs['id_admin'] = $id_auteur;
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_collections',
			),
			'data' => $champs
		)
	);

	$id = sql_insertq('spip_collections', $champs);

	if ($id){
		pipeline('post_insertion',
			array(
				'args' => array(
					'table' => 'spip_collections',
					'id_objet' => $id,
				),
				'data' => $champs
			)
		);

		// controler si le serveur n'a pas renvoye une erreur et associer l'auteur sinon
		if ($id > 0 AND $id_auteur){
			include_spip('action/editer_auteur');
			auteur_associer($id_auteur, array('collection'=>$id));
		}
	}

	return $id;
}

?>