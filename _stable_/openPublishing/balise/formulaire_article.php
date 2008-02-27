<?php

/* Test de sécurité
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/* Les includes de spip utilisé dans cette balise
 */
include_spip('inc/texte');
include_spip('inc/lang');
include_spip('inc/mail');
include_spip('inc/date');
include_spip('inc/meta');
include_spip('inc/session');
include_spip('inc/filtres');
include_spip('inc/acces');
include_spip('inc/documents');
include_spip('inc/ajouter_documents');
include_spip('inc/getdocument');
include_spip('inc/barre');
include_spip('base/abstract_sql');

/* Les includes propre au plugin
 */

// Si SPIP est vieux, charger les fonctions de compat
if ($GLOBALS['spip_version_code'] < '1.93') include_spip('inc/op_compat.php');

include_spip('inc/op_actions'); // base de donnée
include_spip('inc/op_functions'); // fonctions diverses

spip_connect();
charger_generer_url();



/* Cette fonction défini la balise, et en particulier les variables à récuperer dans le contexte et à passer à la fonction _stat en appelant    * la fonction "calculer_balise_dynamique". On pourra ainsi récuperer l’id_article d’une boucle englobante ou la langue contenue dans l’url.    *  C’est un peu comme les paramètres que l’on passe à une balise INCLURE spip.
 *
 *  Déclare le nom de la balise et un tableau des variables à récupérer dans le contexte.
 */

function balise_FORMULAIRE_ARTICLE ($p) {

	$p = calculer_balise_dynamique($p,'FORMULAIRE_ARTICLE',array('id_rubrique'));
	return $p;
}



/* Cette fonction reçois deux tableaux :
 *
 *  1. le premier contient les variables collectée avec le tableau cité plus haut ainsi que les paramètres passés directement à la balise.
 *  2. le second reçoit les filtres appliqués à la balise, au cas où on veuille faire un prétraitement dessus (pour récuperer des variables
 *     par exemple).
 *
 * Elle doit retourner soit :
 *
 *  - une chaîne qui représente un message d’erreur
 *  - un tableau qui sera passé à la balise _dyn (contenant des arguments pour la balise, en générale, le paramètre $args)
 */

function balise_FORMULAIRE_ARTICLE_stat($args, $filtres) {

	return ($args);
}



/* c’est ici qu’on met le traitement des données (insertion en base etc).
 *
 * Elle reçoit les valeures retournées par la fonction _stat et doit retourner soit :
 *
 * - un message d’erreur
 * - un tableau représentant un squelette SPIP :
 *        1. le nom du fond (e.g. "formulaires/formulaire_forum")
 *        2. le délais
 *        3. un tableau des paramètres à passer à ce squelette (ensuite accessible par #ENV)
 *
 *  On peut acceder ici aux variables postées par le formulaire en utilisation la fonction _request('name'); et faire des traitements 
 *  en fonction de celles ci pour faire l’insertion en base, envoyer un mail etc...
 */

function balise_FORMULAIRE_ARTICLE_dyn($id_rubrique) {

/* ces variables sont indispensables pour récuperer les documents joints
 */

global $_FILES, $_HTTP_POST_FILES;

// récupération des données de configuration
$config = lire_config('op');

// si l'auteur anonymous n'est pas dans la base, le plugin openpublishing doit être mal installé
if(!$config['IDAuteur']) die(_T('opconfig:erreur_die'));


// Les différentes actions que peut faire un utilisateur
$previsualiser	= _request('previsualiser'); // demande la prévisualisation
$valider	= _request('valider'); // demande la validation
$media		= _request('media'); // demande l'ajout de document
$mots		= _request('mots'); // demande l'ajout de mot cle
$agenda		= _request('agenda'); // demande la mise en agenda
$abandonner	= _request('abandonner'); // demande l'abandon
$tags		= _request('tags'); // demande des nouveaux mot dans tag machine

// url et id de l'article
$url_site = _request('url_site');
$article = intval(stripslashes(_request('article')));

// données pour formulaire document
$formulaire_documents 	= stripslashes(_request('formulaire_documents'));
$doc 			= stripslashes(_request('doc'));
$type_doc 		= stripslashes(_request('type'));

// données pour formulaire agenda
$formulaire_agenda 	= stripslashes(_request('formulaire_agenda'));
$annee 			= stripslashes(_request('annee'));
$mois			= stripslashes(_request('mois'));
$jour 			= stripslashes(_request('jour'));
$heure 			= stripslashes(_request('heure'));
$choix_agenda 		= stripslashes(_request('choix_agenda'));

// données pour formulaire tagopen (plugin Tag machine)
$formulaire_tagopen 	= stripslashes(_request('formulaire_tagopen'));

// données pour formulaire motclefs
$formulaire_motclefs 	= stripslashes(_request('formulaire_motclefs'));
if (!empty($_POST["motschoix"])) { $motschoix=$_POST["motschoix"]; }

// donnée rubrique
$rubrique		= intval(stripslashes(_request('rubrique')));
if ($id_rubrique) { if (!$rubrique) { $rubrique=$id_rubrique;}}

// donnée article
$titre			= stripslashes(_request('titre'));
$texte			= stripslashes(_request('texte'));
$surtitre		= stripslashes(_request('surtitre'));
$soustitre		= stripslashes(_request('soustitre'));
$chapo			= stripslashes(_request('chapo'));
$descriptif		= stripslashes(_request('descriptif'));
$ps			= stripslashes(_request('ps'));

// donnée identification
$nom_inscription	= stripslashes(_request('nom_inscription'));
$mail_inscription	= stripslashes(_request('mail_inscription'));

// le message d'erreur
$mess_error		= stripslashes(_request('mess_error'));

// déclarations de variables supplémentaires (pour la fonction ajout_document)
$documents_actifs = array();
$lang = _request('var_lang');	
$nom = 'changer_lang';

// remise à zero 
$formulaire_previsu = '';
$bouton= '';
$mess_error = '';
$erreur_document = 0;

// filtrage des zones de texte si elles sont emplies
if ($titre) $titre = entites_html($titre);
if ($nom_inscription) $nom_inscription = entites_html($nom_inscription);
if ($mail_inscription) $mail_inscription = entites_html($mail_inscription);
if ($group_name) $group_name = entites_html($group_name);
if ($phone) $phone = entites_html($phone);
if ($surtitre) $surtitre = entites_html($surtitre);
if ($soustitre) $soustitre = entites_html($soustitre);
if ($chapo) $chapo = entites_html($chapo);
if ($descriptif) $descriptif = entites_html($descriptif);
if ($ps) $ps = entites_html($ps);

// Si l'utilisateur a cliqué sur le bouton "abandonner"
if ($abandonner) {

	// suppression des enregistrements éventuellement créé dans la table spip_mot_article
	if($article) sql_delete("spip_mots_articles", 'id_article = '.sql_quote($article).' LIMIT 1');
	
	// construction de la page de retour
	$url_retour = $url_site . $config['UrlAbandon'] ;
	$message = '<META HTTP-EQUIV="refresh" content="'.$config['TempsAtt'].'; url='.$url_retour.'">' . $config['TextAbandon'];
	
	return $message;
}

// on demande un nouvel identifiant pour l'article si l'utilisateur clique sur l'un des boutons action
if (($previsualiser) || ($media) || ($valider) || ($tags) || ($mots)) {
	if (!$article) $article = op_request_new_id($config['IDAuteur']);
}

// Affichage des infos si l'auteur est identifié et s'il n'a pas modifié les champs identification
$auteur_session = $GLOBALS['auteur_session'];
if($auteur_session) {
	if (!$nom_inscription) $nom_inscription = $auteur_session['nom'];
	if (!$mail_inscription) $mail_inscription = $auteur_session['email'];
}
	
// l'auteur demande la publication de son article
if($valider) {
	// vérification avant mise en Base de donnée
	$flag_ok = 'ok';

	// récupération du statut par défaut de l'article
	$statut = $config['StatutArt'];
	$RubAgenda = $config['RubAgenda'];


	// vérifications et traitements des champs texte
	// Anti spam (remplace les @ par un texte aléatoire)
	if ($config['AntiSpam'] == 'yes') {
		$texte = antispam($texte);
		$ps = antispam($ps);
		$chapo = antispam($chapo);
		$descriptif = antispam($descriptif);
		$mail_inscription = antispam($mail_inscription);
	}

	// pas de majuscule dans le titre d'un article
	if ($config['TitreMaj'] != 'yes') {
 		$titre = strtolower($titre);
	}

	// vérification taille du titre : si x caractère ou moins : erreur
	if (strlen($titre) < $config['TitreMin']) {
		$flag_ok = 'ko';
		$mess_error = _T('opconfig:erreur_min_len') . $config['TitreMin'] . _T('opconfig:caracteres');
	}
		

	// l'auteur demande une insertion dans l'agenda
	if (($choix_agenda == "OK") && ($flag_ok == 'ok')) {

		// construction de la date complete
		$tableau = split('[:]', $heure);
		$heure = $tableau[0];
		$minute = $tableau[1];

		$date_complete = date('Y-m-d H:i:s',mktime($heure, $minute, 0, $mois, $jour, $annee));

		// calcul extra, l'identification est gérée dans les brèves agenda
		$extra=array(
  			"OP_pseudo"=>$nom_inscription,
  			"OP_mail"=>$mail_inscription
		);
		$extra=serialize($extra);

		// Concatenation : le texte est composé du texte ET du chapo, descriptif, ps
		$texte_agenda = $descriptif . $chapo . $texte . $ps;

		// construction lien URL désactivé
		//$lien_url = $url_site . 'spip.php?article' . $article;
		$lien_url = '';

		sql_insertq('spip_breves', array(
			"date_heure" => $date_complete,
			"titre" => $titre,
			"texte" => $texte_agenda,
			"lien_url" => $lien_url,
			"statut" => $statut,
			"id_rubrique" => $RubAgenda,
			"extra" => $extra
		));

		sql_delete('spip_articles','id_article = '.sql_quote($article).' LIMIT 1');
	}
	else if ($flag_ok== 'ok') { // soit il s'agit d'un article, soit d'une breve. Les deux à la fois ne sont pas possible

		// préparation de la mise en base de donnée

		// on recupere le secteur et la langue associée
		$row = sql_fetsel('lang, id_secteur', 'spip_rubriques', 'id_rubrique='.sql_quote($rubrique));
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
  			"OP_pseudo"=>$nom_inscription,
  			"OP_mail"=>$mail_inscription
		);
		$extra=serialize($extra);

		sql_update('spip_articles', array(
			"titre" => sql_quote($titre),
			"id_rubrique" => sql_quote($rubrique),
			"surtitre" => sql_quote($surtitre),
			"soustitre" => sql_quote($soustitre),
			"chapo" => sql_quote($chapo),
			"descriptif" => sql_quote($descriptif),
			"ps" => sql_quote($ps),
			"texte" => sql_quote($texte),
			"statut" => sql_quote($statut),
			"lang" => sql_quote($lang),
			"id_secteur" => sql_quote($id_secteur),
			"date" => "NOW()",
			"date_redac" => "NOW()",
			"date_modif" => "NOW()",
			"extra" => sql_quote($extra)
			),
			 "id_article=".sql_quote($article));

		sql_insertq('spip_auteurs', array(
			'id_auteur' => sql_quote($config['IDAuteur']),
			'id_article' => sql_quote($article)
			));	
	}
	
	if ($flag_ok == 'ok') {
		// notification des admins
		//include_spip('inc/mail');
		//envoyer_mail("edd@riseup.net", "test", "ceci est un test de notification", $from = "", $headers = "");


		// construction de la page de retour
		$url_retour = $url_site . $config['UrlValidation'];
		$message = '<META HTTP-EQUIV="refresh" content="'.$config['TempsAtt'].'; url='.$url_retour.'">' . $config['TextValidation'];
		$message = $message . $retour .'<br />';
		return $message;
	}
}

// si l'auteur ne valide pas ou entre pour la première fois, ou bien on effectue une action


// statut de l'article : en préparation
$statut="prepa";
	
// si l'auteur demande la prévisualisation

if($previsualiser) {

	// vérification taille du titre : si x caractère ou moins : erreur
	if (strlen($titre) < $config['TitreMin']) {
		$flag_ok = 'ko';
		$mess_error = _T('opconfig:erreur_min_len') . $config['TitreMin'] . _T('opconfig:caracteres');
	}

	if(!$erreur){
		$bouton= _T('form_prop_confirmer_envoi');
	}

	// on rempli le formulaire de prévisualisation

	$formulaire_previsu = inclure_balise_dynamique(
	array('formulaires/formulaire_article_previsu', 0,
		array(
			'date_redac' => $date_redac,
			'surtitre' => interdire_scripts(typo($surtitre)),
			'soustitre' => interdire_scripts(typo($soustitre)),
			'chapo' => propre($chapo),
			'descriptif' => propre($descriptif),
			'ps' => propre($ps),
			'titre' => interdire_scripts(typo($titre)),
			'texte' => propre($texte),
			'erreur' => $erreur,
			'nom_inscription' => $nom_inscription,
			'mail_inscription' => $mail_inscription
		)
	), false);

	// aucune idée de ce que c'est, mais ça à l'air important
	$formulaire_previsu = preg_replace("@<(/?)f(orm[>[:space:]])@ism",
	"<\\1no-f\\2", $formulaire_previsu);
}
	
// si l'auteur demande des mots-clefs
if($mots) {
	if ($motschoix){
		foreach($motschoix as $mot){
			//protection contre mots-clefs vide
			$row = sql_fetsel('titre', 'spip_mots', "id_mot=$mot LIMIT 1");
			$titremot = $row['titre'];
			if (!(strcmp($titremot,"")==0)) {
				if ($mot) {
					// on lie l'article aux mots clefs choisis
					sql_insertq('spip_mots_articles', array(
						'id_mot' => sql_quote($mot),
						'id_article' => sql_quote($article)
						));
				}
			}
		}
	}
}
	
// si l'auteur demande des mots-clés avec Tag machine
if ($tags) {
	include_spip('inc/tag-machine');
	ajouter_liste_mots(_request('tags'),
		$article,
		$groupe_defaut = 'tags',
		'articles',
		'id_article',
		true);
}

// si l'auteur ajoute un documents
if($media) {

	// compatibilité php < 4.1
	if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];
		
	// récupération des variables
	$fichier = $_FILES['doc']['name'];
	$size = $_FILES['doc']['size'];
	$tmp = $_FILES['doc']['tmp_name'];
	$type = $_FILES['doc']['type'];
	$error = $_FILES['doc']['error'];
		
	// vérification si upload OK
	if( !is_uploaded_file($tmp) ) {
		echo $error;
		$mess_error = _T('opconfig:erreur_upload');
		$erreur_document = 1;
	}
	else {
		// verification si extention OK
		$tableau = split('[.]', $fichier);
		$type_ext = $tableau[1];
	
		// renomme les extensions
		if (strcmp($type_ext,"jpeg")==0) $type_ext = "jpg";
			
		$tab_ext = get_types_documents();

		$ok = 0;

		// test si l'extension est autorisé
		while ($row = mysql_fetch_array($tab_ext)) {
			if (strcmp($row[0],$type_ext)==0) {
				$ok = 1;
			}
		}

		// ajout du document
		if ($ok==1) {
			inc_ajouter_documents_dist ($tmp, $fichier, "article", $article, $type_doc, $id_document, $documents_actifs);
		}
		else { // sinon, erreur
			$mess_error = _T('opconfig:erreur_extension');
			$erreur_document = 1;
		}
	}
}

// cas d'un nouvel article ou re-affichage du formulaire
if ($config['Agenda'] == 'yes') {
	// Gestion de l'agenda
	$formulaire_agenda = inclure_balise_dynamique(
	array('formulaires/formulaire_agenda',	0,
		array(
			'annee' => $annee,
			'mois' => $mois,
			'jour' => $jour,
			'heure' => $heure,
			'choix_agenda' => $choix_agenda
		)
	), false);
}

// Gestion des documents
if ($config['DocInc'] == 'yes') {

	$bouton= 'Ajouter l\'image ou le document';
	$formulaire_documents = inclure_balise_dynamique(
	array('formulaires/formulaire_documents',	0,
		array(
			'id_article' => $article,
			'tab_ext' => get_types_documents(),
			'bouton' => $bouton,
		)
	), false);
}

// Gestion des mot-clefs avec tag machine
if ($config['TagMachine'] == 'yes') {

	$formulaire_tagopen = inclure_balise_dynamique(
	array('formulaires/formulaire_tagopen',	0,
		array(
			'id_article' => $article,
		)
	), false);
}


// Gestion des mot-clefs
if ($config['MotCle'] == 'yes') {

	$bouton= "Ajouter les nouveaux mot-clefs";
	$formulaire_motclefs = inclure_balise_dynamique(
	array('formulaires/formulaire_motclefs', 0,
		array(
			'id_article' => $article,
			'bouton' => $bouton,
		)
	), false);
}


if ($config['SurTitre'] == 'yes') {
	// champ surtitre

	$formulaire_surtitre = inclure_balise_dynamique(
	array('formulaires/formulaire_surtitre', 0,
		array(
			'surtitre' => interdire_scripts(typo($surtitre)),
		)
	), false);
}


if ($config['SousTitre'] == 'yes') {
	// champ soustitre

	$formulaire_soustitre = inclure_balise_dynamique(
	array('formulaires/formulaire_soustitre', 0,
		array(
			'soustitre' => interdire_scripts(typo($soustitre)),
		)
	), false);
}


if ($config['Descriptif'] =='yes') {
	// champ descriptif

	$formulaire_descriptif = inclure_balise_dynamique(
	array('formulaires/formulaire_descriptif', 0,
		array(
			'descriptif' => $descriptif,
		)
	), false);
}


if ($config['Chapo'] =='yes') {
	// champ chapeau

	$formulaire_chapo = inclure_balise_dynamique(
	array('formulaires/formulaire_chapo', 0,
		array(
			'chapo' => $chapo,
		)
	), false);
}


if ($config['PostScriptum'] == 'yes') {
	// champ PostScriptum

	$formulaire_ps = inclure_balise_dynamique(
	array('formulaires/formulaire_ps', 0,
		array(
			'ps' => $ps,
		)
	), false);
}


// Liste des documents associés à l'article

op_liste_vignette($article);

// le bouton valider

$bouton= _T('form_prop_confirmer_envoi');

// et on remplit le formulaire avec tout ça

return array('formulaires/formulaire_article', 0,
	array(
		'formulaire_documents' => $formulaire_documents,
		'formulaire_previsu' => $formulaire_previsu,
		'formulaire_agenda' => $formulaire_agenda,
		'formulaire_tagopen' => $formulaire_tagopen,
		'formulaire_motclefs' => $formulaire_motclefs,
		'formulaire_surtitre' => $formulaire_surtitre,
		'formulaire_soustitre' => $formulaire_soustitre,
		'formulaire_descriptif' => $formulaire_descriptif,
		'formulaire_chapo' => $formulaire_chapo,
		'formulaire_ps' => $formulaire_ps,
		'bouton' => $bouton,
		'article' => $article,
		'rubrique' => $rubrique,
		'mess_error' => $mess_error,
		'annee' => $annee,
		'mois' => $mois,
		'jour' => $jour,
		'heure' => $heure,
		'url' =>  $url,
		'titre' => interdire_scripts(typo($titre)),
		'texte' => $texte,
		'nom_inscription' => $nom_inscription,
		'mail_inscription' => $mail_inscription,
		'group_name' => $group_name,
		'phone' => $phone
	));
}



?>