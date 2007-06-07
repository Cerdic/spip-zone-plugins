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


/* securite (additif spip_indy, peut-être toujour utile)
 * pour eviter qu'un article soit modifié apres avoir été publié
 * remarque : en entrée de script ce test ne sert à rien
 */

$article = (int) $article;
if($article) {
	$query = "SELECT id_article FROM  spip_articles WHERE id_article=$article AND (statut='prepa' OR statut='creat')";
	$result = spip_query($query);
	if(!mysql_num_rows($result)) {
		// warning , une erreur 404 serait peut etre mieux ?
	 	die("<H3> D&eacute;sol&eacute;, sorry, lo siento : On ne peut pas modifier l'article demand&eacute;.</H3>");
	}
        $query = "SELECT * FROM spip_auteurs_articles WHERE id_article=$article AND id_auteur=$connect_id_auteur";
        $result_auteur = spip_query($query);
        $flag_auteur = (mysql_num_rows($result_auteur) > 0);
	if(!$flag_auteur) {
	 	die("<H3> D&eacute;sol&eacute;, sorry, lo siento : On ne peut pas modifier l'article demand&egrave;.</H3>");
        }
}



/* récupération des données de configuration
 * on récupere la rubrique agenda
 * on récupere l'id de l'auteur anonymous
 */

$rubrique_breve = op_get_rubrique_agenda(); 
$connect_id_auteur =  op_get_id_auteur();

// si l'auteur anonymous n'est pas dans la base, le plugin openpublishing doit être mal installé

if(!$connect_id_auteur) {
	echo _T('opconfig:erreur_anonymous');
	die(_T('opconfig:erreur_die'));
}

/* récupération des variables du formulaire HTML
 * données actions
 * url du site
 * id article (sinon on créé un nouveau article à chaque prévisualisation ou ajout de document ...)
 */

// Les différentes actions que peut faire un utilisateur

$previsualiser	= _request('previsualiser');
$valider	= _request('valider');
$media		= _request('media');
$mots		= _request('mots');
$agenda		= _request('agenda');
$abandonner	= _request('abandonner');
$tags		= _request('tags');

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

// données pour formulaire tagopen

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

// donnée identification

$nom_inscription	= stripslashes(_request('nom_inscription'));
$mail_inscription	= stripslashes(_request('mail_inscription'));
$group_name		= stripslashes(_request('group_name'));
$phone			= stripslashes(_request('phone'));

// le message d'erreur

$mess_error		= stripslashes(_request('mess_error'));

// déclarations de variables supplémentaires (pour la fonction ajout_document)

$documents_actifs = array();
$lang = _request('var_lang');	
$nom = 'changer_lang';
lang_dselect();
$langues = liste_options_langues($nom, $lang);

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

// Si l'utilisateur a cliqué sur le bouton "abandonner"

if ($abandonner) {

	// suppression des enregistrements éventuellement créé dans la table spip_mot_article

	if($article) {
		spip_query("DELETE FROM spip_mots_articles WHERE id_article = '$article'");
	}

	// construction de la page de retour

	$url_retour = $url_site . op_get_url_abandon() ;
	$message = '<META HTTP-EQUIV="refresh" content="10; url='.$url_retour.'">' . op_get_renvoi_abandon();
	$message = $message . $retour;
	return $message;
}

// on demande un nouvel identifiant pour l'article si l'utilisateur clique sur l'un des boutons action

if (($previsualiser) || ($media) || ($valider) || ($tags) || ($mots)) {
	if (!$article) $article=op_request_new_id($connect_id_auteur);
}


// Affichage des infos si l'auteur est identifié et s'il n'a pas modifié les champs identification

$auteur_session = $GLOBALS['auteur_session'];
if($auteur_session) {
	if (!$nom_inscription) $nom_inscription = $auteur_session['nom'];
	if (!$mail_inscription) $mail_inscription = $auteur_session['email'];
}
	
// l'auteur demande la publication de son article

if($valider) {

	// récupération du statut par défaut de l'article

	$statut = op_get_statut();

	// vérifications et traitements des champs texte
	// Anti spam (remplace les @ par un texte aléatoire)
	$flag = op_get_antispam();
	if ($flag == 'oui') {
		$texte = antispam($texte);
		$mail_inscription = antispam($mail_inscription);
	}

	// pas de majuscule dans le titre d'un article
	$flag = op_get_titre_minus();
	if ($flag == 'oui') {
 		$titre = strtolower($titre);
	}

	// l'auteur demande une insertion dans l'agenda

	if ($choix_agenda == "OK") {

		// construction de la date complete

		$tableau = split('[:]', $heure);
		$heure = $tableau[0];
		$minute = $tableau[1];

		$date_complete = date('Y-m-d H:i:s',mktime($heure, $minute, 0, $mois, $jour, $annee));

		// construction lien URL désactivé
		//$lien_url = $url_site . 'spip.php?article' . $article;
		$lien_url = '';

		spip_abstract_insert('spip_breves', "(id_breve,date_heure,titre,texte,lien_url,statut,id_rubrique)", "(
		" . intval($id_breve_op) .",
		" . spip_abstract_quote($date_complete) . ",
		" . spip_abstract_quote($titre) . ",
		" . spip_abstract_quote($texte) . ",
		" . spip_abstract_quote($lien_url) . ",
		" . spip_abstract_quote($statut) . ",
		" . spip_abstract_quote($rubrique_breve) . "
		)");

		// supression de l'article temporaire

		spip_query("DELETE FROM spip_articles WHERE id_article = '$article' LIMIT 1");
	}
	else { // soit il s'agit d'un article, soit d'une breve. Les deux à la fois ne sont pas possible

		// préparation de la mise en base de donnée

		// on recupere le secteur et la langue associée
		$s = spip_query("SELECT id_secteur, lang FROM spip_rubriques WHERE id_rubrique = '$rubrique' ");
		if ($r = spip_fetch_array($s)) {
			$id_secteur = $r["id_secteur"];
			$lang = $r["lang"];
		}

		// L'article existe déjà, on fait donc un UPDATE, et non un INSERT
 
		$retour = spip_query('UPDATE spip_articles SET titre = ' . spip_abstract_quote($titre) .
				',	id_rubrique = ' . spip_abstract_quote($rubrique) .
				',	texte = ' . spip_abstract_quote($texte) .
				',	statut = ' . spip_abstract_quote($statut) .
				',	lang = ' . spip_abstract_quote($lang) .
				',	id_secteur = ' . spip_abstract_quote($id_secteur) .
				',	date = NOW()' .
				',	date_redac = NOW()' .
				',	date_modif = NOW()' .
			 	' WHERE id_article = ' . spip_abstract_quote($article) );

		if ($retour == 1) { $retour = '';}
		else { $retour = _T('opconfig:erreur_insertion');}
	
		// on lie l'article à l'auteur anonymous

		spip_abstract_insert('spip_auteurs', "(id_auteur,id_article)", "(
			" . spip_abstract_quote($id_anonymous) .",
			" . spip_abstract_quote($article) . "
			)");
	
		// on ajoute dans spip_op_auteur l'identitée donnée par l'utilisateur

		spip_abstract_insert('spip_op_auteurs', "(id_auteur,id_article,id_real_auteur,nom,email,group_name,phone)", "(
			" . intval($id_auteur_op) .",
			" . spip_abstract_quote($article) . ",
			" . spip_abstract_quote($id_anonymous) . ",
			" . spip_abstract_quote($nom_inscription) . ",
			" . spip_abstract_quote($mail_inscription) . ",
			" . spip_abstract_quote($group_name) . ",
			" . spip_abstract_quote($phone) . "
			)");
	
	}
	
	// construction de la page de retour

	$url_retour = $url_site . op_get_url_retour();
	$message = '<META HTTP-EQUIV="refresh" content="10; url='.$url_retour.'">' . op_get_renvoi_normal();
	$message = $message . $retour;
	return $message;
}

// si l'auteur ne valide pas ou entre pour la première fois, ou bien on effectue une action

else
{
	// statut de l'article : en préparation

	$statut="prepa";
	
	// si l'auteur demande la prévisualisation

	if($previsualiser)
	{

		// quelques petites vérifications

		if (strlen($titre) < 3){$erreur .= _T('forum_attention_trois_caracteres');}
		if(!$erreur){$bouton= _T('form_prop_confirmer_envoi');}

		// on rempli le formulaire de prévisualisation

		$formulaire_previsu = inclure_balise_dynamique(
		array('formulaires/formulaire_article_previsu', 0,
			array(
				'date_redac' => $date_redac,
				'titre' => interdire_scripts(typo($titre)),
				'texte' => propre($texte),
				'erreur' => $erreur,
				'nom_inscription' => $nom_inscription,
				'mail_inscription' => $mail_inscription,
				'group_name' => $group_name,
				'phone' => $phone
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

				$row = spip_fetch_array(spip_abstract_select('titre', 'spip_mots', "id_mot=$mot LIMIT 1"));
 				$titremot = $row['titre'];
				if (!(strcmp($titremot,"")==0)) {
					if ($mot) {

					// on lie l'article aux mots clefs choisis

					spip_abstract_insert('spip_mots_articles', "(id_mot,id_article)", "(
						" . spip_abstract_quote($mot) .",
						" . spip_abstract_quote($article) . "
					)");
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

	$flag = op_get_agenda();
	if ($flag == 'oui') {

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
	
	$flag = op_get_document();
	if ($flag == 'oui') {

		// Gestion des documents

		$bouton= "Ajouter un nouveau document";
		$formulaire_documents = inclure_balise_dynamique(
		array('formulaires/formulaire_documents',	0,
			array(
				'id_article' => $article,
				'tab_ext' => get_types_documents(),
				'bouton' => $bouton,
			)
		), false);
	}

	$flag = op_get_tagmachine();
	if ($flag == 'oui') {

		// Gestion des mot-clefs avec tag machine

		$formulaire_tagopen = inclure_balise_dynamique(
		array('formulaires/formulaire_tagopen',	0,
			array(
				'id_article' => $article,
			)
		), false);
	}

	$flag = op_get_motclefs();
	if ($flag =='oui') {

		// Gestion des mot-clefs

		$bouton= "Ajouter les nouveaux mot-clefs";
		$formulaire_motclefs = inclure_balise_dynamique(
		array('formulaires/formulaire_motclefs', 0,
			array(
				'id_article' => $article,
				'bouton' => $bouton,
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
}


?>