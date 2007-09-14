<?php

include_spip("inc/ftcaptcha_functions");

/* Ajout du champ dans la liste en mode création formulaire*/
function ftcaptcha_forms_types_champs($flux){
	$flux['captcha']=_T('ftcaptcha:anti_spam');
	return $flux;
}



function ftcaptcha_forms_update_edition_champ($flux){
	$row = $flux['args']['row'];
	$type = $row['type'];
	$champ = $row['champ'];
	$id_form = $row['id_form'];
	if (in_array($type,array('captcha'))){
		if ($s = _request("captcha_$champ")){
					if (!$structure){
						include_spip("inc/forms");
						$structure = Forms_structure($id_form);
					}
					$flux['data'] = "";
					foreach($structure as $champliste=>$infos){
						if($s == $champliste) $flux['data'] = $s;
					}
		}
	}
	return $flux;
}


function ftcaptcha_forms_input_champs($flux){
	static $vu=array();
	$type = $flux['args']['type'];
	if (in_array($type,array('captcha')) AND (_DIR_RESTREINT OR _request('exec')!=='forms_edit')) {
		$id_form = $flux['args']['id_form'];
		$champ = $flux['args']['champ'];
		$extra_info = $flux['args']['extra_info'];
		$vu[$id_form][$type]=array(
			'id'=>extraire_attribut($flux['data'],'id'),
			'name'=>extraire_attribut($flux['data'],'name'),
			'value'=>extraire_attribut($flux['data'],'value'),
			'syst'=>$extra_info);
			
			$flux['data']="";

			$GLOBALS['captcha_level'] = lire_config('ftcaptcha/captcha_niveau_access');
			
		  $flux['data'].= question();
		  $flux['data'].= field($id_form,$champ);

	}
	return $flux;
}

?>