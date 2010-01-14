<?php
/**
 * Formulaire d'Inscription Ardesi
 * Apsulis (http://demo.apsulis.com) - XDjuj
 * Septembre 2009
 *
 */

function formulaires_inscriptions_3_charger_dist($env){
	$valeurs = array(
		'formulaire2'=>unserialize($env),
		'navette'=>'',
		'soiree'=>'',
		'dejeuner'=>'',
		'reglement'=>'',
		'adresse_facturation'=>''
	);
	return $valeurs;
}


function formulaires_inscriptions_3_verifier_dist($env){
	include_spip('inc/validations');
	$erreurs = array();
	
	/* VERIF SUR LES CHAMPS OBLIGATOIRES */
	// $champs_obligatoires = array(
	// 	'soiree'=>'',
	// 	'dejeuner'=>''
	// );
	// foreach($champs_obligatoires as $obligatoire => $valeur){
	// 	if (!_request($obligatoire)) $erreurs[$obligatoire] = '*Ce champ est obligatoire';
	// }

	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	return $erreurs;
}

function formulaires_inscriptions_3_traiter_dist($env){
	include_spip('base/abstract_sql');

	/* Préparation des informations */
	$formulaire = unserialize($env);
	$informations = array();

	$message_ok = 'Votre saisie a bien été enregistrée.<br />
	Un message de confirmation vous a été envoyé ainsi qu\'à l\'organisation.';

	$n = sql_insertq(
		'spip_ardesi_inscriptions',
		array(
			'id' => '',
			'civilite' => corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['civilite'],'utf-8'))),
			'nom' => corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['nom'],'utf-8'))),
			'prenom' => corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['prenom'],'utf-8'))),
			'fonction' => corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['fonction'],'utf-8'))),
			'organisme' => corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['organisme'],'utf-8'))),
			'adresse1' => corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['adresse1'],'utf-8'))),
			'adresse2' => corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['adresse2'],'utf-8'))),
			'cp' => corriger_caracteres(textebrut($formulaire['formulaire1']['cp'])),
			'ville' => corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['ville'],'utf-8'))),
			'telephone' => corriger_caracteres(textebrut($formulaire['formulaire1']['telephone'])),
			'telephone_portable' => corriger_caracteres(textebrut($formulaire['formulaire1']['telephone_portable'])),
			'email' => corriger_caracteres(textebrut($formulaire['formulaire1']['email'])),
			'site_internet' => corriger_caracteres(textebrut($formulaire['formulaire1']['site_internet'])),
			'presence' => presence($formulaire['presence']),
			'atelier_17_matin' => ateliers_matin($formulaire['atelier_17_matin']),
			'atelier_17_apresmidi' => ateliers_pm($formulaire['atelier_17_apresmidi']),
			'navette' => navette(_request('navette')),
			'soiree' => corriger_caracteres(textebrut(_request('soiree'))),
			'dejeuner' => corriger_caracteres(textebrut(_request('dejeuner'))),
			'reglement' => corriger_caracteres(textebrut(_request('reglement'))),
			'adresse_facturation' => corriger_caracteres(textebrut(_request('adresse_facturation'))),
			'date' => date('Y-m-d H:i:s')
		)
	);
	if (!$n) return _T('titre_probleme_technique');
	
	$tarif = tarif(corriger_caracteres(textebrut(_request('soiree'))),corriger_caracteres(textebrut(_request('dejeuner'))));

	/* Donnees du premier formulaire */
	$informations[] = 'Civilité : '.corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['civilite'],'utf-8')));
	$informations[] = 'Nom : '.corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['nom'],'utf-8')));
	$informations[] = 'Prénom : '.corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['prenom'],'utf-8')));
	$informations[] = 'Fonction : '.corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['fonction'],'utf-8')));
	$informations[] = 'Organisme : '.corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['organisme'],'utf-8')));
	$informations[] = 'Adresse : '.corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['adresse1'],'utf-8')));
	$informations[] = 'Adresse (suite) : '.corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['adresse2'],'utf-8')));
	$informations[] = 'Code Postal : '.corriger_caracteres(textebrut($formulaire['formulaire1']['cp']));
	$informations[] = 'Ville : '.corriger_caracteres(textebrut(mb_strtoupper($formulaire['formulaire1']['ville'],'utf-8')));
	$informations[] = 'Téléphone : '.corriger_caracteres(textebrut($formulaire['formulaire1']['telephone']));
	$informations[] = 'Téléphone portable : '.corriger_caracteres(textebrut($formulaire['formulaire1']['telephone_portable']));
	$informations[] = 'Courriel : '.corriger_caracteres(textebrut($formulaire['formulaire1']['email']));
	$informations[] = 'Site Internet : '.corriger_caracteres(textebrut($formulaire['formulaire1']['site_internet']));
	
	/* Données du second formulaire */
	$informations[] = 'Présence : '.presence($formulaire['presence']);
	$informations[] = 'Atelier du 17 au matin : '.ateliers_matin($formulaire['atelier_17_matin']);
	$informations[] = 'Atelier du 17 après-midi : '.ateliers_pm($formulaire['atelier_17_apresmidi']);
    
	/* Données du dernier formulaire */
	$informations[] = 'Navette : '.navette(_request('navette'));
	$informations[] = 'Soirée d\'anniversaire : '.corriger_caracteres(textebrut(_request('soiree')));
	$informations[] = 'Déjeuner de remise de trophée : '.corriger_caracteres(textebrut(_request('dejeuner')));
	$informations[] = 'Montant du paiement : '.$tarif;
	$informations[] = 'Moyen de paiement : '.corriger_caracteres(textebrut(_request('reglement')));
	$informations[] = 'Adresse de facturation différente : '.corriger_caracteres(textebrut(_request('adresse_facturation')));
	
	
	echo('<script type="text/javascript">alert("Montant variante : '.$montant_variante.'")</script>');
	
	// On adresse un mail de confirmation
	$email = corriger_caracteres(textebrut($formulaire['formulaire1']['email']));
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$email_to = "cyril@cym.fr";// $GLOBALS['meta']['email_webmaster'];
	$email_from = $email;
	$reply = $email;
	
	$sujet = "TEST INSCRIPTION";
	$message = "<table width='575' border='0' cellspacing='0' cellpadding='0'>
	  <tr>
	    <td colspan='3'><img src='http://www.etourisme-ardesi.fr/squelettes/includes/img/mail-tete.jpg' width='575' height='129' /></td>
	  </tr>
	  <tr>
	    <td width='20'>&nbsp;</td>
	    <td width='535'>&nbsp;</td>
	    <td width='20'>&nbsp;</td>
	  </tr>
	  <tr>
	    <td width='20'>&nbsp;</td>
	    <td width='535'><p>Bonjour,</p>
<p>Nous avons bien pris en compte votre inscription pour les 5èmes Rencontres Nationales du etourisme institutionnel qui se dérouleront à Toulouse-Labège.</p>
<p>Nous vous attendons donc les 16 et 17 novembre pour fêter les 5 ans de ce rendez-vous !</p>
<p>Retrouvez l'actu des Rencontres sur : http://www.etourisme-ardesi.fr et les réseaux sociaux.</p>
<p>Voici les éléments que vous avez saisis :</p>
<ul>";
	foreach($informations as $champ => $valeur){
		if($valeur != '') $message .= "<li>".$valeur."</li>\n";
	}
	$message .= "</ul>
<p><strong>Règlement (déjeuner et /ou soirée) :</strong></p>
<ul>
	<li>par chèque (à l’ordre d’Ardesi) - A envoyer à : Ardesi – 9 place Alfonse Jourdain – 31000 Toulouse</li>
	<li>par virement (Banque : 30056 / Guichet : 00350 / N° de compte : 03506171184 / Clé RIB : 66 / IBAN : FR76 3005 6003 5003 5061 7118 466 / BIC : CCFRFRPP / Domiciliation : HSBC FR Toulouse.</li>
</ul>
<p>Au plaisir de vous accueillir !</p>
		</td>
	    <td width='20'>&nbsp;</td>
	  </tr>
	</table>";

	include_spip('inc/envoi_mail');
	
	envoyer_mail_html('jfaudiguier@ardesi.fr',$sujet,$email_from,$reply,$message);
	envoyer_mail_html('secretariat@ardesi.fr',$sujet,$email_from,$reply,$message);
	envoyer_mail_html('ldublanchet@ardesi.fr',$sujet,$email_from,$reply,$message);
	$envoi = envoyer_mail_html($email_to,$sujet,$email_from,$reply,$message);
	envoyer_mail_html($email,$sujet,$email_from,$reply,$message);
	
	return array('message_ok'=>$message_ok);
}


function tarif($soiree,$dej){
	$tarif = 0;
	if($soiree == 'oui') $tarif += 25;
	if($dej == 'oui') $tarif += 15;
	
	if($tarif == 0) return false;
	
	return $tarif.' euros';
}
function presence($pres){
	if($pres == 'a') return 'Les deux jours';
	if($pres == 'b') return 'Le lundi 16 novembre';
	if($pres == 'c') return 'Le mardi 17 novembre';
	return false;
}
function ateliers_matin($atelier){
	if($atelier == 'a') return 'Atelier 1 - « Expériences du 2.0 : pourquoi, pour qui, comment, combien ? »';
	if($atelier == 'b') return 'Atelier 2 - « Les applications sur smartphones, nouvel eldorado ? »';
	if($atelier == 'c') return 'Atelier 3 - « Internet et web 2.0 : quelles ressources, quelle organisation ? »';
	if($atelier == 'd') return 'Atelier 4 - « Les outils 2.0 pour débutants motivés et curieux »';
	if($atelier == 'e') return 'Atelier 5 - « Les communautés de voyageurs en ligne »';
	if($atelier == 'f') return 'Atelier 6 - « Le devenir des Systèmes d’Information Touristiques »';
	return false;
}
function ateliers_pm($atelier){
	if($atelier == 'a') return 'Atelier 7 - « Les outils 2.0 pour les geeks »';
	if($atelier == 'b') return 'Atelier 8 - « Créer ou refondre mon site Internet »';
	if($atelier == 'c') return 'Forums dédiés à la commercialisation en ligne ';
	return false;
}
function navette($nav){
	$lesnavs = '/ ';
	if($nav){
		foreach($nav as $champ => $valeur){
			$lesnavs .= $valeur." / ";
		}
	}
	return $lesnavs;
}



?>