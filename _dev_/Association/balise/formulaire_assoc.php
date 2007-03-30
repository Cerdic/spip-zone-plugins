<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
include_spip('inc/mail');
include_spip('inc/charsets');

// Balise independante du contexte ici
function balise_FORMULAIRE_ASSOC ($p) 
{return calculer_balise_dynamique($p, 'FORMULAIRE_ASSOC', array());}

//function balise_FORMULAIRE_ASSOC_stat($args, $filtres) {
	// Si le moteur n'est pas active, pas de balise
	//if ($GLOBALS['meta']["activer_moteur"] != "oui")
		//return '';

	// filtres[0] doit etre un script (a revoir)
	//else
	  //return array($filtres[0], $args[0]);
//}
 
// Balise de traitement des données du formulaire
function balise_FORMULAIRE_ASSOC_dyn() {

//On récupère les champs
$nom=_request('nom');
$prenom=_request('prenom');
$mail=_request('mail');
$rue=_request('adresse');
$cp=_request('cp');
$ville=_request('ville');
$telephone=_request('telephone');
$case_radio=_request('case_radio');
$sujet=_request('sujet');
$text=_request('text');

$query = spip_query( " SELECT * FROM spip_asso_profil " );
while ($data = mysql_fetch_assoc($query)) {
$nomasso=$data['nom'];
$adresse=$data['mail'];
$expediteur=$nomasso.'<'.$adresse.'>';
}	

//on envoit des emails
$entete .= "Reply-To: ".$data['mail']."\n";     					 // réponse automatique à Association
$entete .= "MIME-Version: 1.0\n";
$entete .= "Content-Type: text/plain; charset=$charset\n";	// Type Mime pour un message au format HTML
$entete .= "Content-Transfer-Encoding: 8bit\n";
$entete .= "X-Mailer: PHP/" . phpversion();         			// mailer
//$entetes.= "Content-Type: text/html; charset=iso-8859-1\n"; 
//$entetes.= "X-Sender: < ".$data['mail'].">\n";   } 
//$entetes .= "X-Priority: 1\n";                							// Message urgent ! 
//$entetes .= "X-MSMail-Priority: High\n";         					// définition de la priorité
//$entetes .= "Return-Path: < webmaster@ >\n"; 					// En cas d' erreurs 
//$entetes .= "Errors-To: < webmaster@ >\n";    					// En cas d' erreurs 
//$entetes .= "cc:  \n"; 											// envoi en copie à …
//$entetes .= "bcc: \n";          										// envoi en copie cachée à …

//au webmaster
$message = "Un nouveau membre vient de s'inscrire: ".$prenom." ".$nom."\nSon email :".$mail."\nIl sera membre:".$case_radio."\nSon adresse: ".$adresse." ".$cp." ".$ville."\nSon num&eacute;ro de t&eacute;l&eacute;phone: ".$telephone."\nSon message: ".$text;
//mail("$adresse","$sujet","$message", "$headers");
envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );

//au demandeur
$adresse= $mail;
$message= "Bonjour ".$prenom."\n\n\nVous venez de demander votre inscription &agrave; l'association ".$nomasso."\nNous allons prendre contact avec vous tr&egrave;s rapidement.\n\nAvec nos remerciements. \n\n\nLe bureau de ".$nomasso."\r\n";
envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
//mail("$adresse1","$sujet","$message1","$headers");
	
//enregistrement dans la table
spip_query ( " INSERT INTO spip_asso_adherents (nom, prenom, email,  rue, cp, ville, telephone, statut, creation) VALUES ('$nom', '$prenom',  '$mail',  '$rue', '$cp', '_$ville', '$telephone','prospect', CURRENT_DATE() ) ");	

//On retourne les infos au formulaire
return array
	("formulaires/formulaire_assoc", 3600, array
		(
		'assoc' => ($lien ? $lien : generer_url_public('assoc')),
		//'recherche' => _request('recherche'),
		'lang' => $lang
		)
	);

}
?>