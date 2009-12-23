<?php

/**
 * Enregistrer la date d'inscription dans la base au traitement du formulaire d'inscription
 *
 * @param array $flux
 * @return array
 */
function date_inscription_formulaire_traiter($flux){
	if ($flux['args']['form']=='inscription'){
		$mail = _request('mail_inscription');
		if (function_exists('test_inscription'))
			$f = 'test_inscription';
		else 	$f = 'test_inscription_dist';
		$desc = $f($mode, $mail, $flux['args']['args'][0], $flux['args']['args'][2]);
		if (is_array($desc)
		  AND $mail = $desc['email']){
			include_spip('base/abstract_sql');
			sql_updateq("spip_auteurs", array("date_inscription"=>"NOW()"),"statut='nouveau' AND email=" . sql_quote($mail));
		}
	}
	return $flux;
}

?>