<?php

// pour verification des dates
function verifier_input_date(&$erreur, $input){
	$err = '';
	$data = _request($input);
	if (!$data)
		$err = _T('etat_civil:info_obligatoire');
	else {
		$tab = recup_date($data);
		if(!checkdate($tab[1],$tab[2],$tab[0]) or !$tab)
			$err = _T('etat_civil:date_incorrecte');
	}

	if ($err) $erreur[$input] = $err;
}
		

// pour verif des input texte
function verifier_input_texte(&$erreur, $input, $nb_car){
	$err = '';
	$data = _request($input);
	$data = preg_replace('`\W`', '', $data);
	if (!$data)
		$err = _T('etat_civil:info_obligatoire');
	elseif (strlen($data) < $nb_car)
		$err = $nb_car.' '._T('etat_civil:caracteres_requis');
	
	if ($err) $erreur[$input] = $err;
}

// pour verif des input code postal ou téléphone
function verifier_input_code_tel(&$erreur, $input, $nb_car){
	$err = '';
	$data= _request($input);
	$data = preg_replace('`\D`', '', $data);
	if (!$data)
		$err = _T('etat_civil:info_obligatoire');
	elseif (strlen($data) < $nb_car)
		$err = $nb_car.' '._T('etat_civil:chiffres_requis');
	
	if ($err) $erreur[$input] = $err;
}

// pour verif des email
function verifier_input_email(&$erreur, $input){
	$err = '';
	$data= _request($input);
	if (!$data)
		$err = _T('etat_civil:info_obligatoire');
	elseif (!email_valide($data))
		$err = _T('etat_civil:email_non_valide');
	
	if ($err) $erreur[$input] = $err;
}

?>