<?php
/**
 * Utilisations de pipelines par le plugin Albums
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajout de contenu aux fiches des objets.
 * 
 * - Albums liés aux objets activés dans la configuration du plugin
 * - Documents liés aux albums en cas d'absence du portfolio (cf. note)
 *
 * @note
 * Les portfolios ne sont affichés que pour les objets qu'on a le droit d'éditer (cf. `autoriser_joindredocument_dist`).
 * Mais pour les albums, les documents doivent être visibles dans tous les cas.
 * Si nécessaire, on affiche donc les documents via notre squelette maison.
 *
 * @uses marquer_doublons_album()
 * @pipeline afficher_complement_objet
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function albums_afficher_complement_objet($flux) {

	$texte = "";
	$e     = trouver_objet_exec($flux['args']['type']);
	$type  = $e['type'];
	$id    = intval($flux['args']['id']);

	// Albums liés sur les objets activés
	if (
		!$e['edition']
		AND autoriser('ajouteralbum',$type,$id)
	) {
		// on vérifie d'abord que les albums vus sont bien liés
		$table_objet_sql = table_objet_sql($type);
		$table_objet = table_objet($type);
		$id_table_objet = id_table_objet($type);
		$champs = sql_fetsel('*',$table_objet_sql,addslashes($id_table_objet)."=".intval($id));
		$marquer_doublons_album = charger_fonction('marquer_doublons_album','inc');
		$marquer_doublons_album($champs,$id,$type,$id_table_objet,$table_objet,$table_objet_sql);
		//
		$texte .= recuperer_fond('prive/squelettes/contenu/portfolio_albums', array(
			'objet' => $type,
			'id_objet' => $id,
			),
			array('ajax'=>'albums')
		);
	}

	// Documents liés aux albums quand le portfolio classique n'est pas affiché
	if (!$e['edition'] AND $type == 'album' and !autoriser('joindredocument',$type,$id)) {
		$texte .= recuperer_fond('prive/squelettes/inclure/documents_album', array('id_album' => $id));
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--afficher_complement_objet-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Ajout de contenu sur certaines pages.
 *
 * - Auteurs sur la fiche d'un album
 * - Message sur la fiche d'un album si auteur pas autorisé à modifier (cf. autorisation)
 *
 * @pipeline affiche_milieu
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function albums_affiche_milieu($flux){

	$texte    = "";
	$e        = trouver_objet_exec($flux['args']['exec']);
	$id_objet = $flux['args'][$e['id_table_objet']];

	// fiche d'un album
	if (!$e['edition'] AND $e['type']=='album') {
		// auteurs liés
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => 'album',
			'id_objet' => $id_objet
		));
		// message si l'auteur de l'album n'est pas autorisé à le modifier
		// c'est que l'album est lié à un objet qu'il ne peut pas modifier
		include_spip('action/editer_liens');
		$auteurs_album = array();
		if (is_array($liens_auteurs = objet_trouver_liens(array('auteur'=>'*'),array('album'=>$id_objet))))
			foreach($liens_auteurs as $l)
				$auteurs_album[] = $l['id_auteur'];
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
		if (in_array($id_auteur,$auteurs_album) AND !autoriser(modifier,album,$id_objet)) {
			$texte .= recuperer_fond('prive/squelettes/inclure/message_album_non_editable');
		}
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
 * Modifier ou ajouter du contenu dans la colonne de gauche.
 *
 * - Gestion des albums sur le formulaire d'édition d'un objet
 * lorsqu'on peut lui ajouter des albums mais que l'ajout de documents est désactivé.
 *
 * @note
 * Lors d'une première création de l'objet, celui-ci n'ayant pas
 * encore d'identifiant tant que le formulaire d'edition n'est pas enregistré,
 * les liaisions entre les albums liés et l'objet à créer sauvegardent
 * un identifiant d'objet négatif de la valeur de id_auteur (l'auteur
 * connecté). Ces liaisons seront corrigées apres validation dans albums_post_insertion()
 * cf. plugin Médias.
 *
 * @pipeline affiche_gauche
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function albums_affiche_gauche($flux){

	$e = trouver_objet_exec($flux['args']['exec']);
	if ($e !== false) {
		$type           = $e['type'];
		$id_table_objet = $e['id_table_objet'];
		if (
			$e['edition'] !== false // page édition uniquement
			AND (
				(isset($flux['args'][$id_table_objet]) and $id = intval($flux['args'][$id_table_objet]))
				// id non défini pour les nouveaux objets : on met un identifiant negatif
				OR ($id = 0-$GLOBALS['visiteur_session']['id_auteur'])
			)
			AND autoriser('ajouteralbum',$type,$id)
			AND !autoriser('joindredocument',$type,$id)
		){
			$flux['data'] .= recuperer_fond('prive/objets/editer/colonne_document',array('objet'=>$type,'id_objet'=>$id));
		}
	}

	return $flux;
}


/**
 * Actions effectuées après l'insertion d'un nouvel objet en base.
 *
 * - Mise à jour les liens temporaires avec les albums.
 *
 * @note
 * Lors d'une première création de l'objet, celui-ci n'ayant pas
 * encore d'identifiant tant que le formulaire d'edition n'est pas enregistré,
 * les liaisions entre les albums liés et l'objet à créer sauvegardent
 * un identifiant d'objet négatif de la valeur de id_auteur (l'auteur
 * connecté).
 * Une fois l'objet inséré en base, il faut rétablir ces liaisons
 * avec le vrai identifiant de l'objet.
 * cf. plugin Médias.
 * 
 * @pipeline post_insertion
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function albums_post_insertion($flux){

	$objet    = objet_type($flux['args']['table']);
	$id_objet = $flux['args']['id_objet'];
	include_spip('inc/autoriser');

	if (
		autoriser('ajouteralbum', $objet, $id_objet)
		AND $id_auteur = intval($GLOBALS['visiteur_session']['id_auteur'])
	){
		$id_temporaire = 0-$id_auteur;
		include_spip('action/editer_liens');
		$liens = objet_trouver_liens(array('album'=>'*'),array($objet=>$id_temporaire));
		foreach($liens as $lien){
			objet_associer(array('album'=>$lien['id_album']),array($objet=>$id_objet));
		}
		// un simple delete pour supprimer les liens temporaires
		sql_delete("spip_albums_liens", array("id_objet=".$id_temporaire,"objet=".sql_quote($objet)));
	}

	return $flux;
}


/**
 * Actions effectuées après l'édition d'un objet.
 *
 * - Mise à jour des liens avec les albums.
 *
 * @note
 * cf. pipeline du plugin Médias
 *
 * @uses marquer_doublons_album()
 * @pipeline post_edition
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function albums_post_edition($flux){

	$table_objet_sql = $flux['args']['table'];
	$serveur         = (isset($flux['args']['serveur']) ? $flux['args']['serveur'] : '');
	$type            = isset($flux['args']['type']) ? $flux['args']['type'] : objet_type($table_objet_sql);
	$id_objet        = $flux['args']['id_objet'];
	$id_table_objet  = id_table_objet($type, $serveur);
	$table_objet     = isset($flux['args']['table_objet']) ? $flux['args']['table_objet'] : table_objet($table_objet_sql,$serveur);

	// si on édite un objet, mettre ses albums liés à jour
	if ($table_objet_sql !== 'spip_albums'){
		include_spip('inc/autoriser');
		if (autoriser('autoassocieralbum',$type,$id_objet)){
			$marquer_doublons_album = charger_fonction('marquer_doublons_album','inc');
			$marquer_doublons_album($flux['data'],$id_objet,$type,$id_table_objet,$table_objet,$table_objet_sql,'',$serveur);
		}
	}

	return $flux;
}


/**
 * Plugins Jquery UI nécessaires au plugin
 *
 * @pipeline jqueryui_plugins
 *
 * @param  array $scripts Liste des js chargés
 * @return array          Liste complétée des js chargés
**/
function albums_jqueryui_plugins($scripts){
	foreach(array('jquery.ui.sortable','jquery.ui.autocomplete') as $jqueryui)
		$scripts[] = $jqueryui;
	return $scripts;
}


/**
 * Ajout de feuilles de style CSS sur les pages publiques
 *
 * @pipeline insert_head_css
 *
 * @param string $flux Feuilles de styles
 * @return string      Description complétée des feuilles de styles
 */
function albums_insert_head_css($flux) {

	if (!defined('_ALBUMS_INSERT_HEAD_CSS') OR !_ALBUMS_INSERT_HEAD_CSS){
		include_spip("inc/config");
		$cfg = (defined('_ALBUMS_INSERT_HEAD_CSS')?_ALBUMS_INSERT_HEAD_CSS:lire_config("albums/insert_head_css",1));
		if ($cfg){
			$flux .= '<link rel="stylesheet" href="'.find_in_path('css/albums.css').'" type="text/css" />';
		}
	}

	return $flux;
}


/**
 * Compter les albums liés à un objet
 *
 * @pipeline objet_compte_enfants
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function albums_objet_compte_enfants($flux){

	if ($objet = $flux['args']['objet']
	  AND $id=intval($flux['args']['id_objet'])) {
		// juste les publiés ?
		if (array_key_exists('statut', $flux['args']) and ($flux['args']['statut'] == 'publie')) {
			$flux['data']['album'] = sql_countsel('spip_albums AS D JOIN spip_albums_liens AS L ON D.id_album=L.id_album', "L.objet=".sql_quote($objet)."AND L.id_objet=".intval($id)." AND (D.statut='publie')");
		} else {
			$flux['data']['album'] = sql_countsel('spip_albums AS D JOIN spip_albums_liens AS L ON D.id_album=L.id_album', "L.objet=".sql_quote($objet)."AND L.id_objet=".intval($id)." AND (D.statut='publie' OR D.statut='prepa')");
		}
	}

	return $flux;
}


/**
 * Afficher le nombre d'albums liés dans la boîte infos des rubriques
 *
 * @pipeline boite_infos
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function albums_boite_infos($flux){

	if ($flux['args']['type']=='rubrique'
	  AND $id_rubrique = $flux['args']['id']){
		if ($nb = sql_countsel('spip_albums_liens',"objet='rubrique' AND id_objet=".intval($id_rubrique))){
			$nb = "<div>". singulier_ou_pluriel($nb, "album:info_1_album", "album:info_nb_albums") . "</div>";
			if ($p = strpos($flux['data'],"<!--nb_elements-->"))
				$flux['data'] = substr_replace($flux['data'],$nb,$p,0);
		}
	}

	return $flux;
}


/**
 * Optimiser la base de donnée en supprimant les liens orphelins
 *
 * On supprime :
 * - les albums à la poubelle
 * - les liens obsolètes
 *
 * @pipeline optimiser_base_disparus
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function albums_optimiser_base_disparus($flux){

	// albums à la poubelle
	if (
		isset($flux['args']['date']) AND $flux['args']['date']
		AND is_array($ids_albums_poubelle=sql_allfetsel('id_album', table_objet_sql('album'), "statut='poubelle' AND maj < ".$flux['args']['date']))
	){
		$ids_albums_poubelle = array_keys($ids_albums_poubelle);
		include_spip('inc/albums');
		albums_supprimer($ids_albums_poubelle);
	}

	// optimiser les liens morts entre documents et albums
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('document'=>'*'),array('album'=>'*'));

	return $flux;
}


/**
 * Compléter ou modifier le résultat de la compilation des squelettes de certains formulaires.
 *
 * - Formulaire de configuration des documents :
 *   insérer un message d'avertissement après le titre
 *   au cas où l'ajout des documents aux albums est désactivé.
 * 
 * @pipeline formulaire_fond
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function albums_formulaire_fond($flux){

	if (
		$flux['args']['form'] == 'configurer_documents'
		AND $avertissement = albums_message_cfg_documents(true)
	) {
		// On cherche titre du formulaire : <h3 class="titrem">...</h3>
		// On le capture entièrement pour le faire suivre du message
		$cherche = "/(<h3[^>]\s*class\s?=\s?['\"]titrem.*<\/h3>)/is";
		$remplace = "$1$avertissement";
		$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
	}

	return $flux;
}


/**
 * Fonction privée qui retourne un message d'avertissement
 * au cas où l'ajout de documents aux albums est désactivé.
 *
 * @param bool $baliser true pour baliser le texte avec <p>
 * @return string|bool  le message d'avertissement, sinon false
 */
function albums_message_cfg_documents($baliser=false){

	$message = false;
	$config = explode(',',$GLOBALS['meta']['documents_objets']);
	$config = (is_array($config)) ? $config : array();
	if (!in_array(table_objet_sql('album'),$config)) {
		$message = _T('album:message_avertissement_cfg_documents');
		if ($baliser) $message=inserer_attribut(wrap($message,'<p>'),'class','notice');
	}

	return $message;
}


/**
 * Compléter le tableau de réponse ou effectuer des traitements supplémentaires pour certains formulaires.
 *
 * - Formulaire d'ajout de documents :
 *   rechargement ajax du conteneur des documents : ajout de js au message de retour.
 *
 * @note
 * L'identifiant de l'album peut être négatif en cas de création
 * cf. joindre_document.php, L.206 à 222
 *
 * @pipeline formulaire_fond
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function albums_formulaire_traiter($flux){

	if (
		$flux['args']['form'] == 'joindre_document'
		AND !intval($flux['args']['args'][0]) // nouveau document
		AND $flux['args']['args'][2] == 'album'
		AND $id_album = intval($flux['args']['args'][1])
		AND (
			count($ids_documents = $flux['data']['ids'])
			OR isset($flux['data']['message_ok'])
		)
	) {
		// animation de chaque document ajouté
		// id du conteneur : «#documentX_albumY»
		foreach ($ids_documents as $id_document) $div_documents[] = "#document${id_document}-album${id_album}";
		$div_documents = implode(',',$div_documents);
		$callback = "jQuery('$div_documents').animateAppend();";
		// rechargement du conteneur des documents de l'album
		// id du conteneur : «#documents-albumY»
		$js = "if (window.jQuery) jQuery(function(){ajaxReload('documents-album$id_album',{callback:function(){ $callback }});});";
		$js = "<script type='text/javascript'>$js</script>";
		if (isset($flux['data']['message_erreur']))
			$flux['data']['message_erreur'].= $js;
		else
			$flux['data']['message_ok'] .= $js;
	}

	return $flux;
}


/**
 * Compléter ou modifier la liste des messages des compagnons.
 *
 * - Page «albums» : présentation succinte des albums & explication des filtres latéraux.
 * 
 * @pipeline compagnon_messages
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function albums_compagnon_messages($flux) {

	$exec     = $flux['args']['exec'];
	$pipeline = $flux['args']['pipeline'];
	$vus      = $flux['args']['deja_vus'];
	$aides    = &$flux['data'];

	switch ($pipeline) {

		case 'affiche_milieu':
			switch ($exec) {

				case 'albums':
					// éviter si possible une requête sql.
					if (!isset($vus['albums'])) {
						$aides[] = array(
							'id' => 'albums',
							'titre' => _T('album:c_albumotheque_titre_presentation'),
							'texte' => _T('album:c_albumotheque_presentation'),
							'statuts'=> array('1comite', '0minirezo', 'webmestre'),
							'target'=> '#albumotheque .entete h2',
						);
						$aides[] = array(
							'id' => 'albums',
							'titre' => _T('album:c_albumotheque_titre_filtres'),
							'texte' => _T('album:c_albumotheque_filtres'),
							'statuts'=> array('1comite', '0minirezo', 'webmestre'),
							'target'=> '#navigation .navigation-albums .titre-liste',
						);
					}
					break;
			}
			break;

	}

	return $flux;
}


?>
