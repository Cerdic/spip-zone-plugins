<?php

/* Test de sécurité */
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Les includes de spip utilisé dans cette balise
 */

include_spip('inc/ajouter_documents'); // pour l'ajout de documents
include_spip('inc/barre');


/*
 * Les includes propre au plugin
 */

include_spip('inc/op_functions'); // fonctions diverses


spip_connect();
generer_url_entite();

function balise_FORMULAIRE_ARTICLE ($p) {

	$p = calculer_balise_dynamique($p,'FORMULAIRE_ARTICLE',array());
	return $p;
}


function balise_FORMULAIRE_ARTICLE_stat($args, $filtres) {

	return ($args);
}


function balise_FORMULAIRE_ARTICLE_dyn() {

/*
 * récuperation des données indispensables
 * le global $_FILES : indispensables pour récuperer les documents joints
 * la configuration de Publication Ouverte
 */

global $_FILES, $_HTTP_POST_FILES;
$config = lire_config('op');


/*
 * on test la validite de la configuration du plugin
 * si celle-ci n'est pas valide, alors on sort en affichant les messages d'aides
 */

$ret = OP_test_configuration($config);
if (!$ret['code'])
{
	$message = '<p>'._T('opconfig:erreur_configuration').'</p>';
	$message .= '<ul>';
	foreach($ret['message'] as $m)
	{
		$message .='<li>'.$m.'</li>';
	}
	$message .='</ul><p>'._T('opconfig:erreur_configuration_page').'</p>';

	return $message;
}



/*
 * Si tentative d'attaquer un article déjà existant, on jette.
 */

if (isset($_GET['id_article'])) return _T('opconfig:erreur_protection');


/*
 *  récapitulatif des pipelines :
 * ==============================
 *
 * (cf spip-contrib pour exemples d'utilisations et doc plus complete)
 *
 * OP_environnement : permet aux plugins d'ajouter des variables d'environnement
 * OP_pre_validation : permet aux plugins d'effectuer des traitements avant la validation
 * OP_validation : permet aux plugins d'effectuer une validation "alternative"
 * OP_action : permet aux plugins d'effectuer les traitements sur les variables.
 * OP_squelette : permet aux plugins de calculer leur formulaire.
 *
 *
 * les clé de $variables correspondent aux "names" dans les formulaires HTML
 * celles-ci sont classée en plusieurs catégories
 * actions : les boutons input
 * type : un type pour un champ : 'texte'
 * champs_pri : les champs "principaux" du formulaire (ceux n'étant pas pris en compte par un formulaire auxilliaire
 * champs_aux : les champs "auxilliaires" d'un formulaire auxilliaires
 * flag_erreur : dans le process de pre_validation, si il est mis à true, alors il y a une erreur, retour au formulaire sans validation
 * flag_valider : dans le process de validation, si il est mis à true, alors validation et evite les autres process de validation
 */

$variables = array(
	'actions' => array(),
	'type' => array(),
	'champs_pri' => array(),
	'champs_aux' => array(),
	'flag_erreur' => false,
	'flag_valider' => false
	);

/*
 * Definition des variables
 * Les variables pré-remplies sont des cas particuliés
 */

// definition des actions principales du formulaire
$variables['actions']['previsualiser'] = '';
$variables['actions']['valider'] = '';
$variables['actions']['sup_logo'] = '';
$variables['actions']['media'] = '';
$variables['actions']['abandonner'] = '';

// definition des champs principaux du formulaire
$variables['champs_pri']['id_article'] = '';
$variables['champs_pri']['id_rubrique'] = '';
$variables['champs_pri']['titre'] = '';
$variables['champs_pri']['texte'] = '';
$variables['champs_pri']['surtitre'] = '';
$variables['champs_pri']['soustitre'] = '';
$variables['champs_pri']['chapo'] = '';
$variables['champs_pri']['descriptif'] = '';
$variables['champs_pri']['ps'] = '';
$variables['champs_pri']['nom_inscription'] = '';
$variables['champs_pri']['mail_inscription'] = '';
$variables['champs_pri']['mess_error'] = '';

$variables['champs_aux']['url_site'] = _request('url_site');
$variables['champs_aux']['doc'] = '';
$variables['champs_aux']['type_doc'] = '';
$variables['champs_aux']['titre_doc'] = '';
$variables['champs_aux']['description_doc'] = '';
$variables['champs_aux']['url_doc'] = _request('url_doc');
$variables['champs_aux']['choix_agenda'] = '';
$variables['champs_aux']['choix_AuteurSpip'] = '';
$variables['champs_aux']['annee'] = '';
$variables['champs_aux']['mois'] = '';
$variables['champs_aux']['jour'] = '';
$variables['champs_aux']['heure'] = '';


if (!empty($_POST['mots'])) $variables['champs_aux']['mots'] = $_POST['mots'];

// définition des types(permet de faire passer ces champs par entities_html
$variables['type']['titre'] = 'texte';
$variables['type']['nom_inscription'] = 'texte';
$variables['type']['mail_inscription'] = 'texte';
$variables['type']['ps'] = 'texte';
$variables['type']['descriptif'] = 'texte';
$variables['type']['soustitre'] = 'texte';
$variables['type']['surtitre'] = 'texte';
$variables['type']['chapo'] = 'texte';
$variables['type']['titre_doc'] = 'texte';
$variables['type']['description_doc'] = 'texte';

/*
 * création pipeline variables d'environnement
 * ce pipeline permet aux plugins d'ajouter des actions et/ou des champs
 */

$variables = pipeline('OP_environnement', array(
			'args'=>array('pub_ouverte'=>'pub_ouverte'),
			'data'=>$variables
			));

/*
 * Récuperation des valeurs pour toutes les actions
 */

foreach ($variables['actions'] as $key => $action) {
		$variables['actions'][$key] = _request($key);
}

/*
 * Pour chacuns des champs principaux ou auxilliaires, récupérer la valeur
 * Sauf si il a déjà été traité auparavant p.e :
 * - url_site : ne doit pas être traité par stripslashes
 */

foreach ($variables['champs_pri'] as $key => $champ) {
		if (empty($champ)) $variables['champs_pri'][$key] = stripslashes(_request($key));
}

foreach ($variables['champs_aux'] as $key => $champ) {
		if (empty($champ)) $variables['champs_aux'][$key] = stripslashes(_request($key));
}

// traitement particulier pour id_article et id_rubrique
$variables['champs_pri']['id_article'] = intval($variables['champs_pri']['id_article']);
$variables['champs_pri']['id_rubrique'] = intval($variables['champs_pri']['id_rubrique']);


// traitement particulier pour un type de document automatique
if ($config['DocIncAuto'] == 'yes') {
	$variables['champs_aux']['type_doc'] = $config['DocAuto'];
}

// déclarations de variables supplémentaires (pour la fonction ajout_document)
$documents_actifs = array();
$lang = _request('var_lang');

// remise à zero
$variables['champs_pri']['formulaire_previsu'] = '';
$variables['champs_pri']['bouton'] = '';
$variables['champs_pri']['mess_error'] = '';


// filtrage par html_entities de toutes les variables typées texte
foreach ($variables['champs_pri'] as $key => $champ) {
	if ((!empty($champ)) && ($variables['type'][$key] == 'texte'))
		$variables['champs_pri'][$key] = entites_html($champ);
}

foreach ($variables['champs_aux'] as $key => $champ) {
	if ((!empty($champ)) && ($variables['type'][$key] == 'texte'))
		$variables['champs_aux'][$key] = entites_html($champ);
}


// Action Abandonner
if (!empty($variables['actions']['abandonner'])) {

	/*
	 * création pipeline abandon
	 * ce pipeline permet aux plugins d'ajouter traitement en cas d'abandon
	 * par exemple, supressions de la base de donnée des enregistrements temporaires
	 */

	$variables = pipeline('OP_abandon', array(
				'args'=>array('pub_ouverte'=>'pub_ouverte'),
				'data'=>$variables
				));

	// suppression des enregistrements éventuellement créé dans la table spip_mot_article
	if($variables['champs_pri']['id_article'])
		sql_delete(
			array('spip_mots_articles'),
		 	array('id_article = '.sql_quote($variables['champs_pri']['id_article']).' LIMIT 1')
		);


	// suppression du logo si il existe
	if ($config['Logo'] == 'yes') {
		$nom = 'arton' . $variables['champs_pri']['id_article'];
		$formats_logos = Array('jpg' ,'png', 'gif', 'bmp', 'tif');

		foreach ($formats_logos as $format) {
			if (@file_exists($d = (_DIR_LOGOS . $nom . '.' . $format)))
				@unlink($d);
		}
	}

	// construction de la page de retour
	$url_retour = $variables['champs_aux']['url_site'] . $config['UrlAbandon'] ;
	$message = '<META HTTP-EQUIV="refresh" content="'.$config['TempsAtt'].'; url='.$url_retour.'">' . $config['TextAbandon'];

	return $message;
} // FIN action Abandonner




/*
 * Gestion de l'identifiant
 * on demande un nouvel identifiant pour l'article si l'utilisateur clique sur l'un des boutons action
 */

$identifiant = false;

foreach ($variables['actions'] as $key => $action) {
	if (!empty($action)) $identifiant = true;
}

if ($identifiant == true) {
	if (!$variables['champs_pri']['id_article']) { // premier passage
		$variables['champs_pri']['id_article'] = op_request_new_id($config['IDAuteur']);
	}
}
// FIN gestion identifiant




// l'auteur demande la publication de son article
// Action Valider
if(!empty($variables['actions']['valider'])) {
	// vérification avant mise en Base de donnée

	// récupération du statut par défaut de l'article
	$statut = $config['StatutArt'];

	/*
	 * création pipeline pre_validation
	 * ce pipeline permet aux plugins d'effectuer des traitements avant la validation
	 * p.e : traitement typographique sur le texte
	 */

	$variables = pipeline('OP_pre_validation', array(
				'args'=>array('pub_ouverte'=>'pub_ouverte'),
				'data'=>$variables
				));

	// vérifications et traitements des champs texte
	// Anti spam (remplace les @ par un texte aléatoire)
	if ($config['AntiSpam'] == 'yes') {
		$variables['champs_pri']['texte'] = antispam($variables['champs_pri']['texte']);
		$variables['champs_pri']['ps'] = antispam($variables['champs_pri']['ps']);
		$variables['champs_pri']['chapo'] = antispam($variables['champs_pri']['chapo']);
		$variables['champs_pri']['descriptif'] = antispam($variables['champs_pri']['descriptif']);
		$variables['champs_pri']['mail_inscription'] = antispam($variables['champs_pri']['mail_inscription']);
	}

	// pas de majuscule dans le titre d'un article
	if ($config['TitreMaj'] != 'yes') {
 		$variables['champs_pri']['titre'] = strtolower($variables['champs_pri']['titre']);
	}

	// vérification taille du titre : si x caractère ou moins : erreur
	if (strlen($variables['champs_pri']['titre']) < $config['TitreMin']) {
		$variables['flag_erreur'] = true;
		$variables['champs_pri']['mess_error'] = _T('opconfig:erreur_min_len') . $config['TitreMin'] . _T('opconfig:caracteres');
	}

	/*
	 * création pipeline validation
	 * ce pipeline permet aux plugins d'effectuer une validation "alternative"
	 * p.e : pour passer ailleur que par la création d'un article (création d'un evenement p.e)
	 * IMPORTANT ; ne surtout pas oublier de mettre le flag_valider à true, sinon on embraye sur les autres types de validation
	 * IMPORTANT : tester le flag_valider, il ce peut qu'un autre plugin le mette à true avant :)
	 * IMPORTANT : tester sa variable action .. sinon le process se déroulera si on clique sur un autre bouton
	 */

	$variables = pipeline('OP_validation', array(
				'args'=>array('pub_ouverte'=>'pub_ouverte'),
				'data'=>$variables
				));

	// SI l'auteur demande une insertion dans l'agenda
	// ET que le flag_erreur et toujours à false
	// ET que le flag_valider et toujouts à false
	if (($variables['champs_aux']['choix_agenda'] == "OK")
		&& (!$variables['flag_erreur'])
		&& (!$variables['flag_valider'])	) {

		$RubAgenda = $config['RubAgenda'];

		// construction de la date complete
		$tableau = explode(':', $variables['champs_aux']['heure']);
		$heure = $tableau[0];
		$minute = $tableau[1];

		$date_complete = date('Y-m-d H:i:s',mktime($heure, $minute, 0, $variables['champs_aux']['mois'], $variables['champs_aux']['jour'], $variables['champs_aux']['annee']));

		// calcul extra, l'identification est gérée dans les brèves agenda
		$extra=array(
  			"OP_pseudo"=>$variables['champs_pri']['nom_inscription'],
  			"OP_mail"=>$variables['champs_pri']['mail_inscription']
		);
		$extra=serialize($extra);

		// Concatenation : le texte est composé du texte ET du chapo, descriptif, ps
		$texte_agenda = $variables['champs_pri']['descriptif']
				. $variables['champs_pri']['chapo']
				. $variables['champs_pri']['texte']
				. $variables['champs_pri']['ps'];

		sql_insertq(
			'spip_breves',
			array(
				"date_heure" => $date_complete,
				"titre" => $variables['champs_pri']['titre'],
				"texte" => $texte_agenda,
				"lien_url" => '',
				"statut" => $statut,
				"id_rubrique" => $RubAgenda,
				"extra" => $extra
			)
		);

		// on recupere l'id de la nouvelle breve
		$ret = sql_fetch(sql_select(
			array('MAX(id_breve) as id_breve'),
			array('spip_breves')
		));

		$id_breve = $ret['id_breve'];

		// les mots clef liées le sont maintenant a la breve
		$mots = sql_select (
			array('id_mot'),
			array('spip_mots_articles'),
			array('id_article = '.sql_quote($variables['champs_pri']['id_article']))
			);

		while ($mot = sql_fetch($mots)) {
			sql_insertq(
				'spip_mots_breves',
				array(
					'id_mot' => $mot['id_mot'],
					'id_breve' => $id_breve
				)
			);
		}

		// les images liées le sont maintenant a la breve
		// dés la sortie de spip 2.0, virer l'horrible compatibilité descendante
		if (version_compare($GLOBALS['spip_version_code'], '1.9300', '<')) {
			$documents = sql_select (
				array('id_document'),
				array('spip_documents_articles'),
				array('id_article = '.sql_quote($variables['champs_pri']['id_article']))
				);
		}
		else { // version 2.0
			$documents = sql_select (
				array('id_document'),
				array('spip_documents_liens'),
				array('id_objet = '.sql_quote($variables['champs_pri']['id_article']).' AND objet = '.sql_quote("article"))
				);
		}

		while ($document = sql_fetch($documents)) {

			if (version_compare($GLOBALS['spip_version_code'], '1.9300', '<')) {
				sql_insertq(
					'spip_documents_breves',
					array(
						'id_document' => $document['id_document'],
						'id_breve' => $id_breve
					)
				);
			}
			else {
				sql_insertq(
					'spip_documents_liens',
					array(
						'id_document' => $document['id_document'],
						'id_objet' => $id_breve,
						'objet' => sql_quote("breve")
					)
				);
			}
		}

		if (version_compare($GLOBALS['spip_version_code'], '1.9300', '<')) {
			sql_delete (
				array('spip_documents_articles'),
				array('id_article = '.sql_quote($variables['champs_pri']['id_article']))
			);
		}
		else {
			sql_delete (
				array('spip_documents_liens'),
				array('id_objet = '.sql_quote($variables['champs_pri']['id_article']).' AND objet = '.sql_quote("article"))
			);
		}


		// si il y a un logo attaché à l'article
		$chercher_logo = charger_fonction('chercher_logo','inc');
		$a = $chercher_logo($variables['champs_pri']['id_article'],'id_article');
		if (count($a)) { // on le ratache à la breve
			@rename ($a[0], _DIR_LOGOS . 'breveon'.$id_breve. '.' . $a[3]);
		}


		sql_delete (
			array('spip_mots_articles'),
			array('id_article = '.sql_quote($variables['champs_pri']['id_article']))
		);

		sql_delete(
			array('spip_articles'),
			array('id_article = '.sql_quote($variables['champs_pri']['id_article']).' LIMIT 1')
		);

		$variables['flag_valider'] = true;
	}

	if (	(!$variables['flag_erreur']) // par défaut, c'est un article
		&& (!$variables['flag_valider']) ) {

		// préparation de la mise en base de donnée


		// si la configuration autorise les auteurs spip
		// on lie l'article aux éventuels mots-clés choisi par l'utilisateur :
		if ($config['MotCle'] == 'yes') OP_insert_mots($variables['champs_aux']['mots'],$variables['champs_pri']['id_article']);


		// on recupere le secteur et la langue associée
		$row = sql_fetch(sql_select(
			array('lang, id_secteur'),
			array('spip_rubriques'),
			array('id_rubrique='.sql_quote($variables['champs_pri']['id_rubrique']))
			));
		$id_secteur = $row['id_secteur'];
		$lang_rub = $row['lang'];

		// La langue a la creation : si les liens de traduction sont autorises
		// dans les rubriques, on essaie avec la langue de l'auteur,
		// ou a defaut celle de la rubrique
		// Sinon c'est la langue de la rubrique qui est choisie + heritee
		if ($GLOBALS['meta']['multi_articles'] == 'oui') {
			lang_select($GLOBALS['visiteur_session']['lang']);
			if (in_array($GLOBALS['spip_lang'],
			explode(',', $GLOBALS['meta']['langues_multilingue']))) {
				$lang = $GLOBALS['spip_lang'];
			}
		}

		if (!$lang) {
			$lang = $lang_rub ? $lang_rub : $GLOBALS['meta']['langue_site'];
		}

		// calcul extra
		$extra=array(
  			"OP_pseudo"=>$variables['champs_pri']['nom_inscription'],
  			"OP_mail"=>$variables['champs_pri']['mail_inscription']
		);
		$extra=serialize($extra);


		// construction du tableau $champs pour les pipelines pre_edition et post_edition
		$champs = array(
			'surtitre' => $variables['champs_pri']['surtitre'],
			'titre' => $variables['champs_pri']['titre'],
			'soustitre' => $variables['champs_pri']['soustitre'],
			'descriptif' => $variables['champs_pri']['descriptif'],
			'nom_site' => $variables['champs_pri']['ps'],
			'url_site' => '',
			'chapo' => $variables['champs_pri']['chapo'],
			'texte' => $variables['champs_pri']['texte'],
			'ps' => $variables['champs_pri']['ps'],
			'id_rubrique' => $variables['champs_pri']['id_rubrique'],
			'statut' => $statut,
			'extra' => $extra
		);

		// calcul la date
		$champs['date'] = date('Y-m-d H:i:s');

		// Envoyer autres aux plugins
		if ($config['Pipeline'] == 'yes') {
			$champs = pipeline('pre_edition',
				array(
					'args' => array(
						'table' => 'spip_articles',
						'id_objet' => $variables['champs_pri']['id_article']
					),
					'data' => $champs
				)
			);
		}

		sql_update(
			'spip_articles',
			array(	"titre" => sql_quote($champs['titre']),
				"id_rubrique" => sql_quote($champs['id_rubrique']),
				"surtitre" => sql_quote($champs['surtitre']),
				"soustitre" => sql_quote($champs['soustitre']),
				"chapo" => sql_quote($champs['chapo']),
				"descriptif" => sql_quote($champs['descriptif']),
				"ps" => sql_quote($champs['ps']),
				"texte" => sql_quote($champs['texte']),
				"statut" => sql_quote($champs['statut']),
				"lang" => sql_quote($lang),
				"id_secteur" => sql_quote($id_secteur),
				"date" => sql_quote($champs['date']),
				"date_redac" => sql_quote($champs['date']),
				"date_modif" => sql_quote($champs['date']),
				"extra" => sql_quote($champs['extra'])),
			 array("id_article=".$variables['champs_pri']['id_article'])
		);


		if ($config['AuteurSpip'] == 'yes') {
			// si auteur SPIP, attribuer l'article à l'auteur et non à "anonyme"
			if ($variables['champs_aux']['choix_AuteurSpip'] != 'OK') {
				if (empty($GLOBALS['auteur_session'])) {
					sql_insertq(
						'spip_auteurs_articles',
						array(
							'id_auteur' => $config['IDAuteur'],
							'id_article' => $variables['champs_pri']['id_article'])
					);
				}
				else {
					sql_insertq(
						'spip_auteurs_articles',
						array(
							'id_auteur' => $GLOBALS['auteur_session']['id_auteur'],
							'id_article' => $variables['champs_pri']['id_article'])
					);
				}
			}
			else {
				sql_insertq(
					'spip_auteurs_articles',
					array(
						'id_auteur' => $config['IDAuteur'],
						'id_article' => $variables['champs_pri']['id_article'])
				);
			}
		}
		else {
			sql_insertq(
				'spip_auteurs_articles',
				array(
					'id_auteur' => $config['IDAuteur'],
					'id_article' => $variables['champs_pri']['id_article'])
			);
		}

		// Envoyer autres aux plugins
		if ($config['Pipeline'] == 'yes') {
			pipeline('post_edition',
				array(
					'args' => array(
						'table' => 'spip_articles',
						'id_objet' => $variables['champs_pri']['id_article']
					),
					'data' => $champs
				)
			);
		}

		// notification
		if (($config['Notification'] == "yes") && (!$variables['flag_erreur'])) {
			include_spip('inc/mail');
			include_spip('inc/notifications');

			if (($config['StatutArt'] == "prop") ||  ($config['StatutArt'] == "redac")) {
				notifier_proposition_article($variables['champs_pri']['id_article']);
			}
			else if ($config['StatutArt'] == "publie") {
				notifier_publication_article($variables['champs_pri']['id_article']);
			}
		}
	}

	if (!$variables['flag_erreur']) { // si pas d'erreur : on sort :)
		// construction de la page de retour
		if ($config['UrlPagePubliee'] == 'yes') { // si l'article est automatiquement publie, on peut l'afficher
                    $url_retour = $variables['champs_aux']['url_site'] . '/spip.php?article' .$variables['champs_pri']['id_article'] ;
                }else{
                   $url_retour = $variables['champs_aux']['url_site'] . $config['UrlValidation'];
                }
		$message = '<META HTTP-EQUIV="refresh" content="'.$config['TempsAtt'].'; url='.$url_retour.'">' . $config['TextValidation'];
		$message = $message . $retour .'<br />';
		return $message;
	}
}// FIN Action valider


// si l'auteur ne valide pas ou entre pour la première fois, ou bien on effectue une action



// statut de l'article : en préparation
$statut="prepa";


/*
 * création pipeline action
 * ce pipeline permet aux plugins d'effectuer les traitements sur les variables.
 * IMPORTANT : toujours commencer par un test sur sa variable action !
 * pourra contenir manipulation de la base de donnée, etc ..
 */

$variables = pipeline('OP_action', array(
			'args'=>array('pub_ouverte'=>'pub_ouverte'),
			'data'=>$variables
			));

// si la configuration autorise les auteurs spip
// on lie l'article aux éventuels mots-clés choisi par l'utilisateur :
if ($config['MotCle'] == 'yes') OP_insert_mots($variables['champs_aux']['mots'],$variables['champs_pri']['id_article']);

if ($config['AuteurSpip'] == 'yes') { // si la configuration autorise les auteurs spip
	// l'auteur est identifié et a coché la case Auteur SPIP
	if ($variables['champs_aux']['choix_AuteurSpip'] != 'OK') {
		$variables['champs_pri']['nom_inscription'] = $GLOBALS['auteur_session']['nom'];
		$variables['champs_pri']['mail_inscription'] = $GLOBALS['auteur_session']['email'];
	}
}

// l'auteur demande la suppression de son logo
if (!empty($variables['actions']['sup_logo'])) {
	$nom = 'arton' . $variables['champs_pri']['id_article'];
	$formats_logos = Array('jpg' ,'png', 'gif', 'bmp', 'tif');

	foreach ($formats_logos as $format) {
		if (@file_exists($d = (_DIR_LOGOS . $nom . '.' . $format)))
			@unlink($d);
	}
}

// si l'auteur demande la prévisualisation
if(!empty($variables['actions']['previsualiser'])) {

	// vérification taille du titre : si x caractère ou moins : erreur
	if (strlen($variables['champs_pri']['titre']) < $config['TitreMin']) {
		$variables['flag_erreur'] = true;
		$variables['champs_pri']['mess_error'] = _T('opconfig:erreur_min_len') . $config['TitreMin'] . _T('opconfig:caracteres');
	}

	// préparation du tableau contenant les variables d'environnement
	$tab_env  = array(
		'date_redac' => $date_redac,
		'surtitre' => interdire_scripts(typo($variables['champs_pri']['surtitre'])),
		'soustitre' => interdire_scripts(typo($variables['champs_pri']['soustitre'])),
		'chapo' => propre($variables['champs_pri']['chapo']),
		'descriptif' => propre($variables['champs_pri']['descriptif']),
		'ps' => propre($variables['champs_pri']['ps']),
		'titre' => interdire_scripts(typo($variables['champs_pri']['titre'])),
		'texte' => propre($variables['champs_pri']['texte']),
		'nom_inscription' => interdire_scripts(typo($variables['champs_pri']['nom_inscription'])),
		'mail_inscription' => interdire_scripts(typo($variables['champs_pri']['mail_inscription'])),
		'id_rubrique' => $variables['champs_pri']['id_rubrique']
	);

	// on ajoute aux variables d'environnement les mots-cles choisis
	if (is_array($variables['champs_aux']['mots']))
	{
		foreach($variables['champs_aux']['mots'] as $mot)
		{
			$tab_env['mots_'.$mot] = 'yes';
		}
	}

	// on rempli le formulaire de prévisualisation
	$variables['champs_pri']['formulaire_article_previsu'] =
		inclure_balise_dynamique(array('formulaires/formulaire_article_previsu', 0, $tab_env ), false);
}


// si l'auteur ajoute un documents
if(!empty($variables['actions']['media'])) {

	// ce n'est pas un document distant
	if (empty($variables['champs_aux']['url_doc'])) {
		// compatibilité php < 4.1
		if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];

		// récupération des variables
		$fichier = $_FILES['doc']['name'];
		$size = $_FILES['doc']['size'];
		$tmp = $_FILES['doc']['tmp_name'];
		$type = $_FILES['doc']['type'];
		$error = $_FILES['doc']['error'];
	}

	// Intercepter une erreur a l'envoi
	if (check_upload_error($error)) {
		$variables['champs_pri']['mess_error'] = _T('opconfig:erreur_upload');
	}
	else {

		if (empty($variables['champs_aux']['url_doc'])) {
			$mode = $variables['champs_aux']['type_doc'];
		}
		else {
			$tmp = $variables['champs_aux']['url_doc'];// fichier
			$mode = 'distant';
		}

		// verification si extention OK
		$tableau = explode('.', $fichier);
		$type_ext = $tableau[1];

		// renomme les extensions
		if (strcmp($type_ext,"jpeg")==0) $type_ext = "jpg";
		// attention a la case : tout en minuscule
		$type_ext = strtolower($type_ext);

		$return = sql_fetch(sql_select(
			array('extension'),
			array('spip_types_documents'),
			array('extension = '.sql_quote($type_ext))
			));

		if ($return['extension'] == $type_ext) {

			if ($variables['champs_aux']['type_doc'] == 'logo') { // reprise du code iconifier ... action/iconifer.php
				include_spip('inc/iconifier'); // pour les logos

				// si le logo existe déjà : refus
				if (!@file_exists( _DIR_LOGOS . 'arton'.$variables['champs_pri']['id_article']. '.' . $type_ext)) {
					// placer le document arton$article dans IMG
					$f =_DIR_LOGOS . 'arton'.$variables['champs_pri']['id_article']. '.tmp'; // nom temporaire
					$source = deplacer_fichier_upload($tmp, $f); // on deplace le fichier temp ds le rep logo
					$size = getimagesize($f);
					$formats_logos = Array('jpg' ,'png', 'gif', 'bmp', 'tif');
					if (in_array($type_ext,$formats_logos)) {
						$poids = filesize($f);

						if (_LOGO_MAX_SIZE > 0
						AND $poids > _LOGO_MAX_SIZE*1024) {
							@unlink ($f);
							$variables['champs_pri']['mess_error'] = _T('info_logo_max_poids',
								array('maxi' => taille_en_octets(_LOGO_MAX_SIZE*1024),
								'actuel' => taille_en_octets($poids)));
						}

						if (_LOGO_MAX_WIDTH * _LOGO_MAX_HEIGHT
						AND ($size[0] > _LOGO_MAX_WIDTH
						OR $size[1] > _LOGO_MAX_HEIGHT)) {
							@unlink ($f);
							//ERREUR
							$variables['champs_pri']['mess_error'] = _T('info_logo_max_taille',
									array(
									'maxi' =>
										_T('info_largeur_vignette',
											array('largeur_vignette' => _LOGO_MAX_WIDTH,
											'hauteur_vignette' => _LOGO_MAX_HEIGHT)),
									'actuel' =>
										_T('info_largeur_vignette',
											array('largeur_vignette' => $size[0],
											'hauteur_vignette' => $size[1]))
								));
						}
						@rename ($f, _DIR_LOGOS . 'arton'.$variables['champs_pri']['id_article']. '.' . $type_ext);
					}
					else {
						@unlink ($f);

						// ERREUR
						$variables['champs_pri']['mess_error'] = _T('info_logo_format_interdit',
									array('formats' => join(', ', $formats_logos)));
					}
				}
				else  {
					$variables['champs_pri']['mess_error'] = _T('opconfig:logo_existe_deja');
				}
			}
			else {

				$ajouter_document = charger_fonction('ajouter_documents','inc');
				$ajouter_document($tmp,$fichier,"article",$variables['champs_pri']['id_article'],$mode,$id_document,$documents_actifs);

				// récupération de l'id
				$ret = sql_fetch(sql_select(
					array('MAX(id_document) as id_document'),
					array('spip_documents')
				));

				$id_document = $ret['id_document'];

				// création champs dans la table documents
				sql_update(
					'spip_documents',
					array(
						'titre' => sql_quote($variables['champs_aux']['titre_doc']),
						'descriptif' => sql_quote($variables['champs_aux']['description_doc'])
					),
					array('id_document = '.$id_document)
				);
			}
		}
		else { // sinon, erreur
			$variables['champs_pri']['mess_error'] = _T('opconfig:erreur_extension');
		}
	}
}

// cas d'un nouvel article ou re-affichage du formulaire
if ($config['Agenda'] == 'yes') {
	// Gestion de l'agenda
	$variables['champs_pri']['formulaire_agenda'] =
		inclure_balise_dynamique(
			array('formulaires/formulaire_agenda',	0,
				array(
					'annee' => $variables['champs_aux']['annee'],
					'mois' => $variables['champs_aux']['mois'],
					'jour' => $variables['champs_aux']['jour'],
					'heure' => $variables['champs_aux']['heure'],
					'choix_agenda' => $variables['champs_aux']['choix_agenda']
				)
			), false);
}

// Gestion des documents
if ($config['DocInc'] == 'yes') {

	$variables['champs_pri']['formulaire_documents'] =
		inclure_balise_dynamique(
			array('formulaires/formulaire_documents', 0,
				array(
					'bouton' => 'Ajouter l\'image ou le document'
				)
			), false);
}

// Gestion des mot-clefs
if ($config['MotCle'] == 'yes') {

	// on ajoute aux variables d'environnement les mots-cles choisis
	$tab_env = array();
	if (is_array($variables['champs_aux']['mots']))
	{
		foreach($variables['champs_aux']['mots'] as $mot)
		{
			$tab_env['mots_'.$mot] = 'yes';
		}
	}

	$variables['champs_pri']['formulaire_motclefs'] =
		inclure_balise_dynamique(array('formulaires/formulaire_motclefs', 0, $tab_env), false);
}

if ($config['AuteurSpip'] == 'yes') {

	// si l'utilisateur est loggé
	if ($GLOBALS['auteur_session']) {

		$variables['champs_pri']['formulaire_auteurspip'] =
			inclure_balise_dynamique(
				array('formulaires/formulaire_auteurspip', 0,
					array(
						'choix_AuteurSpip' => $variables['champs_aux']['choix_AuteurSpip']
					)
				), false);
	}
}



// le bouton valider
$variables['champs_pri']['bouton'] = _T('form_prop_confirmer_envoi');

// Envoi de toutes les variables principales au formulaire principale
return array('formulaires/formulaire_article', 0, $variables['champs_pri']);

}

?>
