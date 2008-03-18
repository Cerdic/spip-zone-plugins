<?php

/* Test de sécurité */
if (!defined("_ECRIRE_INC_VERSION")) return;

/* Les includes de spip utilisé dans cette balise
 */
include_spip('inc/ajouter_documents');
include_spip('inc/iconifier');
include_spip('inc/barre');

/* Les includes propre au plugin
 */

if (version_compare($GLOBALS['spip_version_code'],'1.9300','<'))
	include_spip('inc/compat_op.php');

include_spip('inc/op_functions'); // fonctions diverses

spip_connect();
charger_generer_url();

function balise_FORMULAIRE_ARTICLE ($p) {

	$p = calculer_balise_dynamique($p,'FORMULAIRE_ARTICLE',array());
	return $p;
}


function balise_FORMULAIRE_ARTICLE_stat($args, $filtres) {

	return ($args);
}


function balise_FORMULAIRE_ARTICLE_dyn() {

// ces variables sont indispensables pour récuperer les documents joints
global $_FILES, $_HTTP_POST_FILES;

// récupération des données de configuration
$config = lire_config('op');

// si l'auteur anonymous n'est pas dans la base, le plugin openpublishing doit être mal installé
if(!$config['IDAuteur']) return _T('opconfig:erreur_die');


// Les différentes actions que peut faire un utilisateur
$previsualiser	= _request('previsualiser'); // demande la prévisualisation
$valider	= _request('valider'); // demande la validation
$sup_logo	= _request('sup_logo'); // demande la supression du logo
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
	if($article)
		sql_delete(
			array('spip_mots_articles'),
		 	array('id_article = '.sql_quote($article).' LIMIT 1')
		);
	

	// suppression du logo si il existe
	if ($config['Logo'] == 'yes') {
		$nom = 'arton' . intval($article);
		$formats_logos = Array('jpg' ,'png', 'gif', 'bmp', 'tif');
	
		foreach ($formats_logos as $format) {
			if (@file_exists($d = (_DIR_LOGOS . $nom . '.' . $format)))
				@unlink($d);
		}
	}

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

// l'auteur demande la suppression de son logo
if ($sup_logo) {
	$nom = 'arton' . intval($article);
	$formats_logos = Array('jpg' ,'png', 'gif', 'bmp', 'tif');
	
	foreach ($formats_logos as $format) {
		if (@file_exists($d = (_DIR_LOGOS . $nom . '.' . $format)))
			@unlink($d);
	}
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
		
		sql_insertq(
			'spip_breves',
			array(
				"date_heure" => $date_complete,
				"titre" => $titre,
				"texte" => $texte_agenda,
				"lien_url" => $lien_url,
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

		$breve = $ret['id_breve'];

		// les mots clef liées le sont maintenant a la breve
		$mots = sql_select (
			array('id_mot'),
			array('spip_mots_articles'),
			array('id_article = '.sql_quote($article))
			);

		while ($mot = sql_fetch($mots)) {
			sql_insertq(
				'spip_mots_breves',
				array(
					'id_mot' => $mot['id_mot'],
					'id_breve' => $breve
				)
			);
		}

		// les images liées le sont maintenant a la breve

		$documents = sql_select (
			array('id_document'),
			array('spip_documents_articles'),
			array('id_article = '.sql_quote($article))
			);

		while ($document = sql_fetch($documents)) {
			sql_insertq(
				'spip_documents_breves',
				array(
					'id_document' => $document['id_document'],
					'id_breve' => $breve
				)
			);
		}

		sql_delete (
			array('spip_documents_articles'),
			array('id_article = '.sql_quote($article))
		);

		sql_delete (
			array('spip_mots_articles'),
			array('id_article = '.sql_quote($article))
		);

		sql_delete(
			array('spip_articles'),
			array('id_article = '.sql_quote($article).' LIMIT 1')
		);
	}
	else if ($flag_ok== 'ok') { // soit il s'agit d'un article, soit d'une breve. Les deux à la fois ne sont pas possible

		// préparation de la mise en base de donnée

		// on recupere le secteur et la langue associée
		$row = sql_fetch(sql_select(
			array('lang, id_secteur'),
			array('spip_rubriques'),
			array('id_rubrique='.sql_quote($rubrique))
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
  			"OP_pseudo"=>$nom_inscription,
  			"OP_mail"=>$mail_inscription
		);
		$extra=serialize($extra);


		// construction du tableau $champs pour les pipelines
		$champs = array(
			'surtitre' => $surtitre,
			'titre' => $titre,
			'soustitre' => $soustitre,
			'descriptif' => $descriptif,
			'nom_site' => $ps,
			'url_site' => '',
			'chapo' => $chapo,
			'texte' => $texte,
			'ps' => $sp,
			'id_rubrique' => $rubriques,
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
						'id_objet' => $article
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
			 array("id_article=".$article)
		);

		sql_insertq(
			'spip_auteurs_articles',
			array(
				'id_auteur' => $config['IDAuteur'],
				'id_article' => $article)
		);

		// Envoyer autres aux plugins
		if ($config['Pipeline'] == 'yes') {
			pipeline('post_edition',
				array(
					'args' => array(
						'table' => 'spip_articles',
						'id_objet' => $article
					),
					'data' => $champs
				)
			);
		}
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
			'nom_inscription' => interdire_scripts(typo($nom_inscription)),
			'mail_inscription' => interdire_scripts(typo($mail_inscription))
		)
	), false);
}
	
// si l'auteur demande des mots-clefs
if($mots) {
	if ($motschoix){
		foreach($motschoix as $mot){
			//protection contre mots-clefs vide
			$q = sql_fetch(sql_select(
				array('titre'),
				array('spip_mots'),
				array('id_mot='.$mot.' LIMIT 1'))
				);

			$titremot = $row['titre'];
			if (!(strcmp($titremot,"")==0)) {
				if ($mot) {
					// on lie l'article aux mots clefs choisis
					sql_insertq(
						'spip_mots_articles',
						array(
							'id_mot' => $mot,
							'id_article' => $article)
					);
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

	// Intercepter une erreur a l'envoi
	if (check_upload_error($error)) {
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
		// attention a la case : tout en minuscule
		$type_ext = strtolower($type_ext);
		
		$return = sql_fetch(sql_select(
			array('extension'),
			array('spip_types_documents'),
			array('extension = '.sql_quote($type_ext))
			));
		if ($return['extension'] == $type_ext) {

			if ($type_doc == 'logo') { // reprise du code iconifier ... action/iconifer.php
				// si le logo existe déjà : refus
				if (!@file_exists( _DIR_LOGOS . 'arton'.$article . '.' . $type_ext)) {
					// placer le document arton$article dans IMG
					$f =_DIR_LOGOS . 'arton'.$article . '.tmp'; // nom temporaire
					$source = deplacer_fichier_upload($tmp, $f); // on deplace le fichier temp ds le rep logo
					$size = getimagesize($f);
					$formats_logos = Array('jpg' ,'png', 'gif', 'bmp', 'tif');
					if (in_array($type_ext,$formats_logos)) {
						$poids = filesize($f);
	
						if (_LOGO_MAX_SIZE > 0
						AND $poids > _LOGO_MAX_SIZE*1024) {
							@unlink ($f);
							$mess_error = _T('info_logo_max_poids',
								array('maxi' => taille_en_octets(_LOGO_MAX_SIZE*1024),
								'actuel' => taille_en_octets($poids)));
						}
			
						if (_LOGO_MAX_WIDTH * _LOGO_MAX_HEIGHT
						AND ($size[0] > _LOGO_MAX_WIDTH
						OR $size[1] > _LOGO_MAX_HEIGHT)) {
							@unlink ($f);
							//ERREUR
							$mess_error = _T('info_logo_max_taille',
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
						@rename ($f, _DIR_LOGOS . 'arton'.$article . '.' . $type_ext);
					}
					else {
						@unlink ($f);
	
						// ERREUR
						$mess_error = _T('info_logo_format_interdit',
									array('formats' => join(', ', $formats_logos)));
					}
				}
				else  {
					$mess_error = _T('opconfig:logo_existe_deja');
				}
			}
			else {
				inc_ajouter_documents_dist ($tmp, $fichier, "article", $article, $type_doc, $id_document, $documents_actifs);
			}
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
		'bouton' => $bouton,
		'surtitre' => interdire_scripts(typo($surtitre)),
		'descriptif' => $descriptif,
		'chapo' => $chapo,
		'article' => $article,
		'soustitre' => interdire_scripts(typo($soustitre)),
		'ps' => $ps,
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