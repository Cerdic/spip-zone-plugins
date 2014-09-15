<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
//


//RERS      notes...
//    Les premieres définitions servent à pouvoir effectuer par exemple les tests conditionnels suivants : 
//      if ($rers_exec == 'articles_edit') 
//      if ( $id_rub == $rers_rub_offres OR  $id_rub == $rers_rub_demandes )




if (!defined("_ECRIRE_INC_VERSION")) return;
$rers_rub_offres = lire_config('rers/rers_rub_offres');
$rers_rub_demandes = lire_config('rers/rers_rub_demandes');
$rers_rub_vie = lire_config('rers/rers_rub_vie');





//   Pour récupérer le secteur , voir   http://contrib.spip.net/Personnaliser-les-champs-de-l
$id_art = $_GET['id_article'];
$id_rub = $_GET['id_rubrique'];
if ($id_rub == ''){
$row = spip_fetch_array(spip_query("SELECT id_rubrique FROM spip_articles WHERE id_article=$id_art")); $id_rub  = $row['id_rubrique'];
}


$rers_exec = $_GET['exec'];
$rers_dest = $_GET['dest'];
//if ($auteur['statut'] == '0minirezo') 



$GLOBALS[$GLOBALS['idx_lang']] = array(

//  A Classer....    
//'text_article_propose_publication_forum' => " ", // ecrire
'texte_en_cours_validation_forum' => " ",   // ecrire


//--- exec=message
'poster_message' => "Poster un message / Répondre",  // public
'form_forum_message_auto' => "(ceci est un message automatique, répondez en cliquant sur le lien ci-dessous et non par e-mail)", // spip

//--- exec/rers_aide.php
// aide RERS espace privé  
'rers_aide_titre' => "Guide du rédacteur RERS Sud de l'Aisne",

//--- icones espace privé
'icone_a_suivre' => "Accueil espace privé",//spip
'icone_edition_site' => "Rubriques", //spip
'titre_forum' => "Forum entre Adhérents", //spip
'info_forum_interne' => "Forum entre Adhérents", //ecrire
'icone_auteurs' => "Adhérents",//spip
'icone_aide_ligne' => "Aide SPIP",//spip
'icone_visiter_site' => "Basculer vers l'espace public",

//--- inc/inc-menu.html  (menu de gauche)
'rers_toute_la_vie' => "...",
'rers_autres_existent' => "D'autres articles existent. Cliquez pour les voir",

//--- article.html,    mot clé = rers_inscription     
'pass_vousinscrire' => " ",
'pass_espace_prive_bla' => " ",
'rers_inscription_etape1' => "Inscription : étape 1",
'form_forum_identifiants' => 'Identifiants personnels',
'form_forum_indiquer_nom_email' => 'Indiquez ici votre nom et votre adresse email. Votre identifiant personnel vous parviendra rapidement, par courrier &eacute;lectronique.',


// --- exec/messagerie.php        messagerie de l'espace privé
'info_tous_redacteurs' => "Annonce privée à tous les rédacteurs", // ecrire

// --- formulaires/inscription.php       dans un email automatique  NON    DESACTIVE
/*'form_forum_voici2' => 
	"Inscription : étape 1 /
	Vous trouverez ci-dessous vos identifiants pour pouvoir interagir avec les autres adhérents
	du réseau d'échange des savoirs sur
	le site '@nom_site_spip@' (@adresse_login@). /
	Vous pouvez dès à présent utiliser pour vous identifier sur le site, mais l'accès aux rubriques
	des savoirs proposés et demandés ne vous sera autorisé qu'après l'étape 2 de l'inscription. /
	L'étape 2 consiste à rencontrer un responsable de l'association pour signer avec lui la charte des 
	réseaux d'échange de savoirs. / Voici ces identifiants :", */

//--- exec/syncro
'ical_texte_prive' => "Calendrier, à usage strictement personnel, vous informe :
<ul> 
<li> de vos tâches et rendez-vous personnels,</li>
<li> des dates où de nouvelles fiches de savoirs ont été proposées.</li>
</ul>
",

'ical_texte_public' => "Ce calendrier vous permet de suivre l'activité publique de ce site (pricipalement dates où des nouveaux articles de la rubrique VIE DU RERS ont été publiés).<br />
(A ne pas confondre avec un 3ème calendrier plus adapté : 
<a href='webcal://www.rers-sud-aisne.fr/spip.php?page=ical-agenda'>le calendrier des évènements publics</a> (agenda visible sur l'espace public)  ",

//--- exec/articles_edit.php
'rers_texte_defaut_offre' => "

- nombre de participants : 
- nombre de séances : 
- temps par séance : 
- lieu : 
- matériel : 
- disponibilité : 

",
'rers_texte_defaut_demande' => "

- lieu : 
- disponibilité : 

",

//--- exec/auteurs.php (page "auteurs" de l'espace privé)
'info_auteurs' => "Les Adhérents", //ecrire
'entree_infos_perso' => "Qui êtes-vous ? (ne sera pas affiché sur l'espace public)", // ecrire
'entree_adresse_email' => // ecrire
	"Votre adresse email. 
	Elle ne sera pas divulgué aux adhérents de l'association.", 
'entree_nom_pseudo' =>  //ecrire
	"Votre prénom suivi d'au moins la première lettre de votre nom. 
	Exemple : Jean-Pierre D"

);





// +++++++++++++ OFFRES
  if ($id_rub == $rers_rub_offres) 
  {
  $GLOBALS[$GLOBALS['idx_lang']][info_articles_proposes] = "OFFRES";//spip
  $GLOBALS[$GLOBALS['idx_lang']][icone_ecrire_article] = "Écrire un nouvel article (OFFRE)";//spip
  }

// +++++++++++++ DEMANDES
  if ($id_rub == $rers_rub_demandes)  
  {
  $GLOBALS[$GLOBALS['idx_lang']][info_articles_proposes] = "DEMANDES";//spip
  $GLOBALS[$GLOBALS['idx_lang']][icone_ecrire_article] = "Écrire un nouvel article 
   (DEMANDE)";//spip
  }






if ($rers_exec == 'message_edit' AND $rers_dest !== '') 
// Remarque: dest est vide si c'est une annonce à tous les rédacteurs
{
$GLOBALS[$GLOBALS['idx_lang']][bouton_envoi_message_02] = 
"ENVOYER UN MESSAGE
<small>
<br/>
<br /> N'oubliez pas d'indiquer dans le message la fiche de savoir qui vous fait réagir.
<br/>
<br />  Ce message est strictement privé. 
 Si le destinataire a une adresse électronique, il recevra un email lui indiquant qu'il a un nouveau message à consulter sur le site RERS Sud Aisne.
Sinon, c'est à sa prochaine connexion au site qu'il lira ce message.
</small>"
;

//$GLOBALS[$GLOBALS['idx_lang']][info_texte_message_02] = //ecrire
//"Texte du message. 
//	<br /> >> Précisez par exemple le sujet de la fiche de savoir qui vous fait réagir."
//;

}


if ($rers_exec == 'articles') 
{
  // +++++++++++++ OFFRES et DEMANDES
  if ( $id_rub == $rers_rub_offres OR  $id_rub == $rers_rub_demandes )
  {
  $GLOBALS[$GLOBALS['idx_lang']][texte_statut_publie] = "Publié"; // SPIP
  }
}




if ($rers_exec == 'articles_edit') 
{
  // +++++++++++++ OFFRES et DEMANDES
  if ( $id_rub == $rers_rub_offres OR  $id_rub == $rers_rub_demandes )
  {
  $GLOBALS[$GLOBALS['idx_lang']][info_titre] = "Titre  (sujet de la fiche de savoir)";
  $GLOBALS[$GLOBALS['idx_lang']][texte_modifier_article] = "Modifier l'article (en 
	l'occurence une fiche de savoir)";
  }

}




?>
