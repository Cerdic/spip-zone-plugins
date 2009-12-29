<?php
/**
 * Expressions Régulières de Validations diverses 
 * Apsulis (http://demo.apsulis.com) - XDjuj
 * Juin 2009
 *
 */

/**
 * Changement de la RegExp d'origine
 * Non respect des RFC beaucoup trop souples à mon sens
 * On INTERDIT les mails dont les domaines ne sont pas "valides"
 * On INTERDIT les adresses qui comportent autre chose que des tirets / tirets bas / point
 * (même si les autres caractères sont autorisés, tant pis, ils sont trop rares)
 */
function verif_email_apsulis($adresses){
	// Si c'est un spammeur autant arreter tout de suite
	if (preg_match(",[\n\r].*(MIME|multipart|Content-),i", $adresses)) {
		spip_log("Tentative d'injection de mail : $adresses");
		return false;
	}
	foreach (explode(',', $adresses) as $v) {
		// nettoyer certains formats
		// "Marie Toto <Marie@toto.com>"
		$adresse = trim(preg_replace(",^[^<>\"]*<([^<>\"]+)>$,i", "\\1", $v));
		// NE RESPECTANT PLUS RFC 822
		if (!preg_match('/^([A-Za-z0-9]){1}([A-Za-z0-9]|-|_|\.)*@[A-Za-z0-9]([A-Za-z0-9]|-|\.){1,}\.[A-Za-z]{2,4}$/', $adresse))
			return false;
	}
	return $adresse;
}

/**
 * Vérifications que le courriel utilisé n'est pas
 * déjà présent en base SPIP_AUTEURS
 */
function verif_email_dispo($adresse){
	include_spip('base/abstract_sql'); /* Correctif pour 209 */
	$emailDejaUtilise = sql_getfetsel("id_auteur", "spip_auteurs", "email='".$adresse."'");
	if($emailDejaUtilise) return false;
	return $adresse;
}
/**
 * Vérifications que le courriel utilisé n'est pas
 * déjà présent en base SPIP_AUTEURS
 * déjà présent en base
 * on n'affiche pas l'erreur si c'est celui de l'auteur
 */
function verif_email_dispo2($adresse,$fipki){
	include_spip('base/abstract_sql'); /* Correctif pour 209 */
	$emailDejaUtilise = sql_getfetsel("id_auteur", "spip_auteurs", "email='".$adresse."'");
	$emailDejaUtilise2 = sql_getfetsel("FIPlanetK_Identifiant", "spip_ardesi_inscriptions", "KPEmail1='".$adresse."' AND NOT (FIPlanetK_Identifiant='".$fipki."')");
	if($emailDejaUtilise || $emailDejaUtilise2) return false;
	return $adresse;
}
function verif_email_dispo3($adresse,$id_auteur){
	include_spip('base/abstract_sql'); /* Correctif pour 209 */
	$emailDejaUtilise = sql_getfetsel("id_auteur", "spip_auteurs", "email='".$adresse."' AND NOT (id_auteur='".$id_auteur."')");
	$emailDejaUtilise2 = sql_getfetsel("id_auteur", "spip_ardesi_inscriptions", "KPEmail1='".$adresse."' AND NOT (id_auteur='".$id_auteur."')");
	if($emailDejaUtilise || $emailDejaUtilise2) return false;
	return $adresse;
}

/**
 * Vérification que le login de PRE-inscription (envoyé par courrier)
 * n'a pas déjà été utilisé.
 * S'il a déjà été utilisé, alors il existe un id_auteur, sinon id_auteur = 0
 */
function verif_login_karcher($login){
	include_spip('base/abstract_sql'); /* Correctif pour 209 */
	$loginDejaUtilise = sql_getfetsel("id_auteur", "spip_ardesi_inscriptions", "FIPlanetK_Identifiant='".$login."'");
	if(!$loginDejaUtilise) return false;
	
	return $loginDejaUtilise;
}

/**
 * Vérification que le pass de PRE-inscription
 * correspond bien au login de PRE-inscription
 * (envoyés par courrier)
 */
function verif_pass_karcher($login,$pass){
	include_spip('base/abstract_sql'); /* Correctif pour 209 */
	$pass_ok = sql_getfetsel("Mot_de_passe", "spip_ardesi_inscriptions", "FIPlanetK_Identifiant='".$login."'");
	if(!$pass_ok || !$pass || ($pass != $pass_ok)) return false;
	return $pass_ok;
}

/**
 * SPIP n'accepte pas de mots de passes de moins de 6 caractères
 */
function verif_taille_motdepasse($pass){
	if(strlen($pass) <= 5) return '*Mot de passe trop court (minimum 6 caractères).';
	return false;
}

/**
 * Un code postal est composé de 5 chiffres
 */
function verif_cp($cp){
	if(!preg_match('/^[0-9]{5}$/',$cp)) return false;
	return $cp;
}

/**
 * Une ville n'est pas composée de chiffres
 */
function verif_ville($ville){
	if(preg_match("/[0-9]/",$ville)) return false;
	return $ville;
}

/**
 * Un téléphone est composé de 10 chiffres
 * Il peut contenir des points, des espaces, des slashs ou des tirets
 * (qui ne sont donc pas pris en compte)
 * en fonction des notations
 */
function verif_tel($tel){
	if(!preg_match("/^[0-9]{10}$/",$tel)) return false;
	return $tel;
}

/**
 * 1/ Un SIREN comporte STRICTEMENT 9 caractères
 * 1b/ Un SIRET comporte strictement 14 caractères
 * 2/ Un siren/siret utilise une clef de controle "1-2"
 *    Un siren/siret est donc valide si la somme des chiffres paires
 *    + la somme du double de tous les chiffres impairs (16 = 1+6 = 7) est un multiple de 10
 */
function verif_siren($siren){
	// Si pas 9 caractère, c'est déjà foiré !
	if(!preg_match('/^[0-9]{9}$/',$siren)) return false;
	
	// On vérifie la clef de controle "1-2"
	$somme = 0;
	$i = 0; // Les impaires
	while($i < 9){ $somme += $siren[$i]; $i+=2; }
	$i = 1; // Les paires !
	while($i < 9){ if((2*$siren[$i])>9) $somme += (2*$siren[$i])-9; else $somme += 2*$siren[$i]; $i+=2; }
	
	if($somme % 10) return false;
	
	return $siren;
}
function verif_siret($siret){
	// Si pas 14 caractère, c'est déjà foiré !
	if(!preg_match('/^[0-9]{14}$/',$siret)) return false;
	if(preg_match('/[0]{8}/',$siret)) return false;

	// On vérifie la clef de controle "1-2" avec les impaires *2 (vs pairs*2 pour SIREN, parce qu'on part de la fin)
	$somme = 0;
	$i = 1; // Les paires
	while($i < 14){ $somme += $siret[$i]; $i+=2; }
	$i = 0; // Les impaires !
	while($i < 14){ if((2*$siret[$i])>9) $somme += (2*$siret[$i])-9; else $somme += 2*$siret[$i]; $i+=2; }
	
	if($somme % 10) return false;
	
	return $siret;
}

/**
 * Un nom n'est pas composé de chiffres
 * (on pourrait être plus fermes encore et restreindre les /#...)
 */
function verif_nom($nom){
	if(preg_match('/[0-9]/',$nom)) return false;
	return $nom;
}

/**
 * Un nombre est composé de chiffres
 */
function verif_si_numerique($valeur){
	if(!preg_match('/^[0-9]*$/',$valeur)) return false;
	return $valeur;
}
/**
 * Un nombre est composé de chiffres
 * et peut être nul
 */
function verif_si_numerique_ou_nul($valeur){
	if($valeur == '0') return "zéro";
	if(!preg_match('/^[0-9]*$/',$valeur)) return false;
	return $valeur;
}

/**
 * Une date au format JJ/MM/AAAA
 * On pourrait faire mieux, genre vérifier les jours en fonction du mois
 * Mais c'est pas très important, on reste simple
 */
function verif_date($date){
	if(!preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#',$date)) return false;
	list($jour,$mois,$annee) = split('/',$date);
	if(($jour > 31)|| ($jour < 1) || ($mois > 12) || ($mois < 1) || ($annee < 1950)) return false;
	return $date;
}

/**
 * Les dates sont récupérées en JJ/MM/AAAA
 * Elles sont restituées sous forme AAAA-MM-JJ
 */
function formater_date_karcher($date){
	list($jour,$mois,$annee) = split('/',$date);
	$date = $annee.'-'.$mois.'-'.$jour;
	return $date;
}
/**
 * Les dates sont récupérées en AAAA-MM-JJ
 * Elles sont restituées sous forme JJ/MM/AAAA
 */
function restaurer_date($date){
	list($annee,$mois,$jour) = split('-',$date);
	$date = $jour.'/'.$mois.'/'.$annee;
	return $date;
}

?>