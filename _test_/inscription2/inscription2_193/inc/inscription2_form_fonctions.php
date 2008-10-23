<?php
/*
 *  ! brief Determine les champs de formulaire à traiter
 */
function inscription2_champs_formulaire() {

	//charge les valeurs de chaque champs proposés dans le formulaire   
	foreach (lire_config('inscription2/') as $clef => $valeur) {
		/* Il faut retrouver les noms des champ, 
		* par défaut inscription2 propose pour chaque champ le cas champ_obligatoire
		*  On retrouve donc les chaines de type champ_obligatoire
		*  Ensuite on verifie que le champ est proposé dans le formulaire
		*  Remplissage de $valeurs[]
		*/
		//decoupe la clef sous le forme $resultat[0] = $resultat[1] ."_obligatoire"
		//?: permet de rechercher la chaine sans etre retournée dans les résultats
		preg_match('/^(.*)(?:_obligatoire)/i', $clef, $resultat);
	
		if ((!empty($resultat[1])) && (lire_config('inscription2/'.$resultat[1]) == 'on')) {
			$valeurs[] = $resultat[1];
			$valeur = _request($resultat[1]);
			$valeurs[$resultat[1]] = $valeur;
		}
	}
	return $valeurs;
}
/*
// http://doc.spip.org/@test_inscription_dist
function test_inscription_dist($mode, $mail, $nom, $id=0) {

    include_spip('inc/filtres');
    $nom = trim(corriger_caracteres($nom));
    if (!$nom || strlen($nom) > 64)
        return _T('ecrire:info_login_trop_court');
    if (!$r = email_valide($mail)) return _T('info_email_invalide');
    return array('email' => $r, 'nom' => $nom, 'bio' => $mode);
}
*/

function inscription2_valide_cp($cp){
	if(!$cp){
		return;
	}
	else{
		if(preg_match('/^[A-Z]{1,2}[-|\s][0-9]{3,6}$|^[0-9]{3,6}$|^[0-9|A-Z]{2,5}[-|\s][0-9|A-Z]{2,4}$|^[A-Z]{1,2} [0-9|A-Z]{2,5}[-|\s][0-9|A-Z]{2,4}/i',$cp)){
			return;
		}
		else{
			return _T('inscription2:cp_valide');
		}
	}
}

function inscription2_valide_numero($numero){
	if(!$numero){
		return;
	}
	else{
		if(preg_match('/^[0-9\+\. \-]+$/',$numero)){
			return;
		}
		else{
			return _T('inscription2:numero_tva_valide');
		}
	}
}

function inscription2_valid_login($login,$nom) {
	if(!isset($login))
		$login=$nom;
		
	$n = sql_countsel("spip_auteurs","login='$login'");
	if ($n==0){
		return $login;
	}
	$login = $login.($n+1);
	return $login;
}
?>