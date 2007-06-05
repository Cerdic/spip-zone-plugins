<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/texte');
include_spip('inc/lang');
include_spip('inc/mail');
include_spip('inc/date');
include_spip ("inc/meta");
include_spip ("inc/session");
include_spip ("inc/filtres");
include_spip ("inc/acces");
include_spip ("inc/documents");
include_spip ("inc/ajouter_documents");
include_spip ("inc/getdocument");
include_spip('inc/barre');
include_spip('base/abstract_sql');
include_spip('inc/op_actions');

spip_connect();

charger_generer_url();

function balise_FORMULAIRE_ARTICLE ($p) {

/* Cette fonction défini la balise, et en particulier les variables à récuperer dans le contexte et à passer à la fonction _stat en appelant      la fonction "calculer_balise_dynamique". On pourra ainsi récuperer l’id_article d’une boucle englobante ou la langue contenue dans l’url.      C’est un peu comme les paramètres que l’on passe à une balise INCLURE spip.

   Déclare le nom de la balise et un tableau des variables à récupérer dans le contexte.
*/

	$p = calculer_balise_dynamique($p,'FORMULAIRE_ARTICLE',array('id_rubrique'));
	return $p;
}

function balise_FORMULAIRE_ARTICLE_stat($args, $filtres) {

/* Cette fonction reçois deux tableaux :

   1. le premier contient les variables collectée avec le tableau cité plus haut ainsi que les paramètres passés directement à la balise.
   2. le second reçoit les filtres appliqués à la balise, au cas où on veuille faire un prétraitement dessus (pour récuperer des variables
      par exemple).

Elle doit retourner soit :

    * une chaîne qui représente un message d’erreur
    * un tableau qui sera passé à la balise _dyn (contenant des arguments pour la balise, en générale, le paramètre $args)
*/

	return ($args);
}

function balise_FORMULAIRE_ARTICLE_dyn($id_rubrique) {

/* c’est ici qu’on met le traitement des données (insertion en base etc).

   Elle reçoit les valeures retournées par la fonction _stat et doit retourner soit :

    * un message d’erreur
    * un tableau représentant un squelette SPIP :
         1. le nom du fond (e.g. "formulaires/formulaire_forum")
         2. le délais
         3. un tableau des paramètres à passer à ce squelette (ensuite accessible par #ENV)

   On peut acceder ici aux variables postées par le formulaire en utilisation la fonction _request('name'); et faire des traitements 
   en fonction de celles ci pour faire l’insertion en base, envoyer un mail etc...
*/


global $_FILES, $_HTTP_POST_FILES; // ces variables sont indispensables pour récuperer les documents joints

// récupération de la rubrique agenda
$rubrique_breve = op_get_rubrique_agenda();

// securite (additif spip_indy, peut-être toujour utile)
$article = (int) $article;

// on recuperer l'id de l'auteur anonymous
$connect_id_auteur =  op_get_id_auteur();

// si il n'est pas dans la base => plugins openpublishing mal installé
if(!$connect_id_auteur) {
	echo "erreur, pas d'auteur anonymous dans la base, publication impossible";
	die("veuillez verifiez votre installation du plugin OpenPublishing");
}

// securite (additif spip_indy, peut-être toujour utile)
// pour eviter qu'un article soit modifié apres avoir été publié
// remarque : en entrée de script ce test ne sert à rien
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

// récupération des variables du formulaire HTML

// données actions
$previsualiser= _request('previsualiser');
$valider= _request('valider');
$media=_request('media');
$mots=_request('mots');
$agenda=_request('agenda');
$abandonner=_request('abandonner');

// recuperation url du site
$url_site = _request('url_site');

// on recupere l'id article (sinon on créer un nouveau article à chaque prévisualisation ou ajout de document ...
$article = intval(stripslashes(_request('article')));

// on quitte et renvoie vers le sommaire
if ($abandonner) {

	// Attention, en cas d'abandon,
	// il faut supprimer les enregistrements éventuellement créer dans la table spip_mot_article
	if($article) {
		spip_query("DELETE FROM spip_mots_articles WHERE id_article = '$article'");
	}

	$url_retour = $url_site . op_get_url_abandon() ;
	$message = '<META HTTP-EQUIV="refresh" content="10; url='.$url_retour.'">' . op_get_renvoi_abandon();
	$message = $message . $retour;
	return $message;

}

// données pour formulaire document
$formulaire_documents = stripslashes(_request('formulaire_documents'));
$doc = stripslashes(_request('doc'));
$type_doc = stripslashes(_request('type'));

// données pour formulaire agenda
$formulaire_agenda = stripslashes(_request('formulaire_agenda'));
$annee = stripslashes(_request('annee'));
$mois = stripslashes(_request('mois'));
$jour = stripslashes(_request('jour'));
$heure = stripslashes(_request('heure'));
$choix_agenda = stripslashes(_request('choix_agenda'));

// données pour formulaire tagopen
$formulaire_tagopen = stripslashes(_request('formulaire_tagopen'));

// données pour formulaire motclefs
$formulaire_motclefs = stripslashes(_request('formulaire_motclefs'));
if (!empty($_POST["motschoix"])) {
	$motschoix=$_POST["motschoix"];	
}

// donnée rubrique
$rubrique= intval(stripslashes(_request('rubrique')));
if ($id_rubrique) {
	if (!$rubrique) { $rubrique=$id_rubrique;}
}

// donnée article
$titre= stripslashes(_request('titre'));
$texte= stripslashes(_request('texte'));

// donnée identification
$nom_inscription= stripslashes(_request('nom_inscription'));
$mail_inscription= stripslashes(_request('mail_inscription'));
$group_name= stripslashes(_request('group_name'));
$phone= stripslashes(_request('phone'));

//Affichage info auteur identifié
$auteur_session = $GLOBALS['auteur_session'];
if($auteur_session) {
	if (!$nom_inscription) $nom_inscription = $auteur_session['nom'];
	if (!$mail_inscription) $mail_inscription = $auteur_session['email'];
}


$mess_error= stripslashes(_request('mess_error'));

// autres données non utilisées (reliquat #FORMULAIRE_ARTICLE)
$ps= '';
$lien_titre= '';
$lien_url= '';
$surtitre= '';
$soustitre=  '';
$chapo= '';
$descriptif= '';

// déclarations de variables supplémentaires (pour la fonction ajout_document)
$documents_actifs = array();

// autres variables
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

/*
if(!$calendrier){
	$date_debut = date("Y-m-d H:i:s");
} else {
	$date_debut = date("Y-m-d H:i:s", mktime(_request('heures'),_request('minutes'),0,_request('mois'), _request('jour'), _request('annee')));
}
	
$heures = heures($date_debut);
$minutes = minutes($date_debut);
			
$choix_date_debut = afficher_jour_mois_annee_h_m($date_debut, $heures, $minutes);
$date_redac = $date_debut;
*/

// on demande un nouvel identifiant
if (($previsualiser) || ($media) || ($valider) || isset($_REQUEST['tags']) || ($mots)) {
	if (!$article) $article=op_request_new_id($connect_id_auteur);
}

	// on enregistre les mots cles (necessite le plugin tag machine)
	
	if (isset($_REQUEST['tags'])) {
		include_spip('inc/tag-machine');
		ajouter_liste_mots(_request('tags'),
			$article,
			$groupe_defaut = 'tags',
			'articles',
			'id_article',
			true);
	}
	
// l'auteur demande la publication de son article
if($valider) {

	// statut de l'article : proposé
	$statut= 'prop';

	/*
	 * vérifications et traitements des champs texte
	 */

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

	/* fonctions  anti robots inspiré par les forums
	 * a adapter pour l'openpublishing
	 */

	// Antispam : si 'nobot' a ete renseigne, ca ne peut etre qu'un bot
        //if (strlen(_request('nobot'))) {
        //    tracer_erreur_forum('champ interdit (nobot) rempli');
        //    return $retour_forum; # echec silencieux du POST
        //}

 
       // Verifier hash securite pour les forums avec previsu
       /*if ($afficher_texte <> 'non') {
           $file = forum_insert_secure(_request('alea'), _request('hash'));
           if (!$file) {
               # ne pas tracer cette erreur, peut etre due a un double POST
               # tracer_erreur_forum('session absente');
               return $retour_forum; # echec silencieux du POST
           }
   
           // antispam : si le champ au nom aleatoire verif_$hash n'est pas 'ok'
           // on meurt
           if (_request('verif_'._request('hash')) != 'ok') {
               tracer_erreur_forum('champ verif manquant');
               return $retour_forum;
           }
       }*/
	
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
	/*
	 * préparation de la mise en base de donnée
	 */

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

	if ($retour == 1){ // tout c'est bien passé
		$retour = '';
	}
	else{
		$retour = "erreur lors de l'insertion de votre article dans la base de donnée, veuillez contactez les responsables du site";
	}
	
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

	
	
	
	} // fin du else
	
	$url_retour = $url_site . op_get_url_retour();
	$message = '<META HTTP-EQUIV="refresh" content="10; url='.$url_retour.'">' . op_get_renvoi_normal();
	$message = $message . $retour;
	return $message;
}

// si l'auteur ne valide pas ou entre pour la première fois
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
			$mess_error = "erreur d'upload, le fichier temporaire est introuvable, il ce peut que vous tentiez d'uploader un fichier trop volumineux. La taille maximale autorisée est de 5 Mo";
			$erreur_document = 1;
    		}
		else {

			// verification si extention OK

			$tableau = split('[/]', $type);
			$type_ext = $tableau[1];
		
			// renomme les extensions
			if (strcmp($type_ext,"jpeg")==0) $type_ext = "jpg";

			$tab_ext = get_types_documents();

			$ok = 0;

			while ($row = mysql_fetch_array($tab_ext)) {
				if (strcmp($row[0],$type_ext)==0) {
					$ok = 1;
				}
			}

			// ajout du document
			if ($ok==1) {
				inc_ajouter_documents_dist ($tmp, $fichier, "article", $article, $type_doc, $id_document, $documents_actifs);
			}
			else {
				$mess_error = "erreur d'upload. L'extention de votre fichier n'est pas autoris&eacute;e ...";
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

	// pour le moment le flag n'est pas mis en place
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

// fonction qui affiche la zone de texte et la barre de typographie
function barre_article($texte, $rows, $cols, $lang='')
{
	static $num_formulaire = 0;
	include_ecrire('inc/layer');

	$texte = entites_html($texte);
	if (!$GLOBALS['browser_barre'])
		return "<textarea name='texte' rows='$rows' class='forml' cols='$cols'>$texte</textarea>";
	
	$num_formulaire++;
	
	return afficher_barre("document.getElementById('formulaire_$num_formulaire')", false) .
	  "<textarea name='texte' rows='$rows' class='forml' cols='$cols'
	id='formulaire_$num_formulaire'
	onselect='storeCaret(this);'
	onclick='storeCaret(this);'
	onkeyup='storeCaret(this);'
	ondbclick='storeCaret(this);'>$texte</textarea>" .
	$GLOBALS['options'];
}

// pour garder la valeur lors d'un rechargement de page
function selected_option($id_rubrique, $rubrique_boucle,$titre_rubrique)
{
	$selected = '';
	if ($id_rubrique == $rubrique_boucle) $selected = "SELECTED";
	return "[<option value='$rubrique_boucle' $selected >&nbsp;$titre_rubrique</option>]";
}

// pour garder la valeur lors d'un rechargement de page
function checkbox_agenda($choix_agenda) {

	$checked = '';
	if ($choix_agenda == "OK") $checked = "CHECKED";
	return "<input type='checkbox' name='choix_agenda' value='OK' '$checked' />&nbsp;Publier en tant que br&egrave;ve dans l'agenda<br />";
}

// pour garder la valeur lors d'un rechargement de page
function select_annee($annee) {
	$selected = "";
	if ($annee !== '') $selected = "SELECTED";
	$return = "<select name='annee'>"; // pour le moment ne prend en compte que 5 ans
	for ($i = 0;$i<5;$i++) {
		$a = 2007 + $i;
		if ($a == $annee) {
			$return = $return . "<option value='$a' '$selected' >$a</option>";
		}
		else $return = $return . "<option value='$a'>$a</option>";
	}
	return $return ."</select>";
}

// pour garder la valeur lors d'un rechargement de page
function select_mois($mois) {
	$selected = "";
	if ($mois !== '') $selected = "SELECTED";
	$return = "<select name='mois'>"; 

	if ($mois == "01") $return = $return . "<option value='01' '$selected' >janvier</option>";
	else $return = $return . "<option value='01'>janvier</option>";
	if ($mois == "02") $return = $return . "<option value='02' '$selected' >fevrier</option>";
	else $return = $return . "<option value='02'>fevrier</option>";
	if ($mois == "03") $return = $return . "<option value='03' '$selected' >mars</option>";
	else $return = $return . "<option value='03'>mars</option>";
	if ($mois == "04") $return = $return . "<option value='04' '$selected' >avril</option>";
	else $return = $return . "<option value='04'>avril</option>";
	if ($mois == "05") $return = $return . "<option value='05' '$selected' >mai</option>";
	else $return = $return . "<option value='05'>mai</option>";
	if ($mois == "06") $return = $return . "<option value='06' '$selected' >juin</option>";
	else $return = $return . "<option value='06'>juin</option>";
	if ($mois == "07") $return = $return . "<option value='07' '$selected' >juillet</option>";
	else $return = $return . "<option value='07'>juillet</option>";
	if ($mois == "08") $return = $return . "<option value='08' '$selected' >aout</option>";
	else $return = $return . "<option value='08'>aout</option>";
	if ($mois == "09") $return = $return . "<option value='09' '$selected' >septembre</option>";
	else $return = $return . "<option value='09'>septembre</option>";
	if ($mois == "10") $return = $return . "<option value='10' '$selected' >otobre</option>";
	else $return = $return . "<option value='10'>octobre</option>";
	if ($mois == "11") $return = $return . "<option value='11' '$selected' >novembre</option>";
	else $return = $return . "<option value='11'>novembre</option>";
	if ($mois == "12") $return = $return . "<option value='12' '$selected' >decembre</option>";
	else $return = $return . "<option value='12'>decembre</option>";

	return $return ."</select>";
}

// pour garder la valeur lors d'un rechargement de page
function select_jour($jour) {

	$selected = "";
	if ($jour !== '') $selected = "SELECTED";
	$return = "<select name='jour'>"; // pour le moment tous les mois ont 31 jours
	for ($i = 0;$i<31;$i++) {
		$j = 1 + $i;
		if ($j == $jour) {
			$return = $return . "<option value='$j' '$selected' >$j</option>";
		}
		else $return = $return . "<option value='$j'>$j</option>";
	}
	return $return . '</select>';
}

// pour garder la valeur lors d'un rechargement de page
function select_heure($heure) {

	$selected = "";
	if ($heure !== '') $selected = "SELECTED";
	$return = "<select name='heure'>"; // pour le moment tous les mois ont 31 jours

	if ($heure == "06:00") $return = $return . "<option value='06:00' '$selected' >06:00</option>";
	else $return = $return . "<option value='06:00' >06:00</option>";
	if ($heure == "07:00") $return = $return . "<option value='07:00' '$selected' >07:00</option>";
	else $return = $return . "<option value='07:00' >07:00</option>";
	if ($heure == "08:00") $return = $return . "<option value='08:00' '$selected' >08:00</option>";
	else $return = $return . "<option value='08:00' >08:00</option>";
	if ($heure == "09:00") $return = $return . "<option value='09:00' '$selected' >09:00</option>";
	else $return = $return . "<option value='09:00' >09:00</option>";
	if ($heure == "10:00") $return = $return . "<option value='10:00' '$selected' >10:00</option>";
	else $return = $return . "<option value='10:00' >10:00</option>";
	if ($heure == "11:00") $return = $return . "<option value='11:00' '$selected' >11:00</option>";
	else $return = $return . "<option value='11:00' >11:00</option>";
	if ($heure == "12:00") $return = $return . "<option value='12:00' '$selected' >12:00</option>";
	else $return = $return . "<option value='12:00' >12:00</option>";
	if ($heure == "13:00") $return = $return . "<option value='13:00' '$selected' >13:00</option>";
	else $return = $return . "<option value='13:00' >13:00</option>";
	if ($heure == "14:00") $return = $return . "<option value='14:00' '$selected' >14:00</option>";
	else $return = $return . "<option value='14:00' >14:00</option>";
	if ($heure == "15:00") $return = $return . "<option value='15:00' '$selected' >15:00</option>";
	else $return = $return . "<option value='15:00' >15:00</option>";
	if ($heure == "16:00") $return = $return . "<option value='16:00' '$selected' >16:00</option>";
	else $return = $return . "<option value='16:00' >16:00</option>";
	if ($heure == "17:00") $return = $return . "<option value='17:00' '$selected' >17:00</option>";
	else $return = $return . "<option value='17:00' >17:00</option>";
	if ($heure == "18:00") $return = $return . "<option value='18:00' '$selected' >18:00</option>";
	else $return = $return . "<option value='18:00' >18:00</option>";
	if ($heure == "19:00") $return = $return . "<option value='19:00' '$selected' >19:00</option>";
	else $return = $return . "<option value='19:00' >19:00</option>";
	if ($heure == "20:00") $return = $return . "<option value='20:00' '$selected' >20:00</option>";
	else $return = $return . "<option value='20:00' >20:00</option>";
	if ($heure == "21:00") $return = $return . "<option value='21:00' '$selected' >21:00</option>";
	else $return = $return . "<option value='21:00' >21:00</option>";
	if ($heure == "22:00") $return = $return . "<option value='22:00' '$selected' >22:00</option>";
	else $return = $return . "<option value='22:00' >22:00</option>";
	if ($heure == "23:00") $return = $return . "<option value='23:00' '$selected' >23:00</option>";
	else $return = $return . "<option value='23:00' >23:00</option>";
	if ($heure == "00:00") $return = $return . "<option value='00:00' '$selected' >00:00</option>";
	else $return = $return . "<option value='00:00' >00:00</option>";
	if ($heure == "01:00") $return = $return . "<option value='01:00' '$selected' >01:00</option>";
	else $return = $return . "<option value='01:00' >01:00</option>";
	if ($heure == "02:00") $return = $return . "<option value='02:00' '$selected' >02:00</option>";
	else $return = $return . "<option value='02:00' >02:00</option>";
	if ($heure == "03:00") $return = $return . "<option value='03:00' '$selected' >03:00</option>";
	else $return = $return . "<option value='03:00' >03:00</option>";
	if ($heure == "04:00") $return = $return . "<option value='04:00' '$selected' >04:00</option>";
	else $return = $return . "<option value='04:00' >04:00</option>";
	if ($heure == "05:00") $return = $return . "<option value='05:00' '$selected' >05:00</option>";
	else $return = $return . "<option value='05:00' >05:00</option>";
		
	return $return . '</select>';
}

// fonction qui demande à la base un nouvel id_article
function op_request_new_id($connect_id_auteur)
{
	$statut_nouv='prepa';
	$forums_publics = substr(lire_meta('forums_publics'),0,3);
	spip_query("INSERT INTO spip_articles (statut, date, accepter_forum) VALUES ( 'prepa', NOW(), '$forums_publics')");
	$article = mysql_insert_id();
	spip_query("DELETE FROM spip_auteurs_articles WHERE id_article = $article");
	spip_query("INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($connect_id_auteur, $article)");
	// lors de la demande d'un nouvel id article, il faut supprimer les relations éventuelles avec la table mots_articles
	spip_query("DELETE FROM spip_mots_articles WHERE id_article = '$article'");
	return $article;
}

// fonction qui liste les documents
function op_liste_vignette($article)
{


	$result = spip_query("SELECT * FROM spip_documents_articles WHERE id_article = $article");

	if (mysql_num_rows($result) > 0 ) {
		echo '<div id="block-center">';
		echo '<div id="block-center-titre"><b>&nbsp;&nbsp;Vos documents</b></div>';
		echo '<div id="block-content"><small>';
		echo '<center><p>Pour inclure directement une image ou un document dans votre article, recopiez dans votre texte le code figurant sous la vignette. Vous pouvez aussi ne rien faire, dans ce cas, vos documents apparaitrons dans une liste sous votre article.</p></center></small>';
	}
	else return;

	echo '<center><table><tr>';
			
	while($row=mysql_fetch_array($result)){
		$id_doc = $row[0];
		$result2 = spip_query("SELECT fichier, mode FROM spip_documents WHERE id_document = $id_doc");
		while($row2=mysql_fetch_array($result2)){
			$empla = $row2['fichier'];
			$mode = $row2['mode'];
			
			// ajout du code inclusion
			if ($mode == "vignette") {
				echo '<td align="center"><img src="'.$empla.'" width="100" height="100" \><br />';
				echo '<code>&lt;img'.$id_doc.'|right&gt;</code><br />';
				echo '<code>&lt;img'.$id_doc.'|center&gt;</code><br />';
				echo '<code>&lt;img'.$id_doc.'|left&gt;</code><br />';
			}
			else {
				$tableau = split('[.]', $empla);
				$ext = $tableau[1];
				// ajout pour utiliser les vignettes spip pour documents
				list($fic, $largeur, $hauteur) = vignette_par_defaut($ext);
 				$image = "<img src='$fic'\n\theight='$hauteur' width='$largeur' />";
// 				$empla = $url_site . 'squelettes/images/icones/'.$ext.'-dist.png';
//				echo '<td align="center"><img src="'.$empla.'" width="50" height="50" \><br />';
				echo '<td align="center">'.$image.'<br />';
				echo '<code>&lt;doc'.$id_doc.'|right&gt;</code><br />';
				echo '<code>&lt;doc'.$id_doc.'|center&gt;</code><br />';
				echo '<code>&lt;doc'.$id_doc.'|left&gt;</code><br />';
			}
			echo '</td>';
		}
	}
	echo '</tr></table>';

	echo '<small><p>';
	echo 'Conseil de mise en page :<br /> Placez vos images inclues dans un tableau (|||) afin d\'&eacute;viter qu\'elles ne "bousculent" votre mise en page. <br />';
	echo '<div style="width : 400px; background-color: #eee; margin: 10px;">';
	echo '&nbsp;ceci est le texte au dessus de mon image<br />';
	echo ' | <code>&lt;imgxxxx|center&gt;</code> | ceci est le texte &agrave; c&ocirc;t&eacute; de l\'image&nbsp;|<br />';
	echo '&nbsp;ceci est le texte en dessous de l\'image<br />';
	echo '</div>';
	echo '</p></small>';
	echo '</center>';
	echo '</div></div><br />';
}

// renvoie sous forme de tableau la liste des extensions autorisée par spip

function get_types_documents() {
	$query = "SELECT extension FROM spip_types_documents";
	return spip_query($query);
}
	
// affichage du tableau extension

function afficher_tab($tab_ext) {

	while ($ext = mysql_fetch_array($tab_ext)) {
		$message = $message . $ext[0] .', ';
	}
	return $message;
}

// reliquat spipindy, fonction qui coupe les trop gros textes.

function coupe_trop_long($texte){    // utile pour les textes > 32ko
    if (strlen($texte) > 28*1024) {
        $texte = str_replace("\r\n","\n",$texte);
        $pos = strpos($texte, "\n\n", 28*1024);    // coupe para > 28 ko
        if ($pos > 0 and $pos < 32 * 1024) {
            $debut = substr($texte, 0, $pos)."\n\n<!--SPIP-->\n";
            $suite = substr($texte, $pos + 2);
        } else {
            $pos = strpos($texte, " ", 28*1024);    // sinon coupe espace
            if (!($pos > 0 and $pos < 32 * 1024)) {
                $pos = 28*1024;    // au pire (pas d'espace trouv'e)
                $decalage = 0; // si y'a pas d'espace, il ne faut pas perdre le caract`ere
            } else {
                $decalage = 1;
            }
            $debut = substr($texte,0,$pos + $decalage); // Il faut conserver l'espace s'il y en a un
            $suite = substr($texte,$pos + $decalage);
       }
  return (array($debut,$suite));
 }
 else return (array($texte,''));
}

?>