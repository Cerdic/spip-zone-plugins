<?php

function formslies_forms_types_champs($flux){
	$flux['formslies']=_T('formslies:type_formslies');
	return $flux;
}

function formslies_forms_bloc_edition_champ($flux){
	$row = $flux['args']['row'];
	$type = $row['type'];

	if ($type=='formslies'){
		$action_link = $flux['args']['action_link'];
		$redirect = $flux['args']['redirect'];
		$idbloc = $flux['args']['idbloc'];
	
		$id_form = $row['id_form'];
		$champ = $row['champ'];
		$titre = $row['titre'];
		$obligatoire = $row['obligatoire'];
		$extra_info = intval($row['extra_info']);
		$specifiant = $row['specifiant'];
		$public = $row['public'];
		$aide = $row['aide'];
		$html_wrap = $row['html_wrap'];
		
		$out = $flux['data'];
		$out .= "<label for=\"".$champ."_id_form\">"._T('formslies:formslies_type_label')."</label> : \n";
		$contexte=array('id_form'=>$extra_info,'champ'=>$champ.'_id_form');
		include_spip('public/assembler');
		$out .= recuperer_fond('formslies_forms_selecteur',$contexte);
		$out .= "<br />\n";		
		$flux['data'] = $out;
	}
	return $flux;
}

function formslies_forms_update_edition_champ($flux){
	$extra_info = $flux['args']['data'];
	$row = $flux['args']['row'];
	$type = $row['type'];
	$champ = $row['champ'];
	$r=intval($extra_info);
	if ($type=='formslies'){
		$flux['data']=_request($champ."_id_form");;
	}
	return $flux;
}

function formslies_forms_input_champs($flux){
	static $vu=array();
	$type = $flux['args']['type'];
	if ($type=='formslies') {
		$id_form = $flux['args']['id_form'];
		$champ = $flux['args']['champ'];
		$extra_info = $flux['args']['extra_info'];
		$id=extraire_attribut($flux['data'],'id');
		$val=extraire_attribut($flux['data'],'value');
		if((_DIR_RESTREINT AND $GLOBALS['formslies_public']!=false) 
			OR (!_DIR_RESTREINT AND _request('exec')!=='forms_edit')) {
			$vu[$id_form][$type]=array(
				'id'=>$id,
				//'name'=>extraire_attribut($flux['data'],'name'),
				'value'=>$val,
				'options'=>$extra_info);
		
		}
		if (intval($extra_info)){
			$contexte = array();
			$contexte['id'] = $id;
			$contexte['value'] = $val;
			$contexte['id_form'] = $extra_info;
			$contexte['champ'] = $champ;
			include_spip('public/assembler');
			$flux['data'] = recuperer_fond('formslies_selecteur',$contexte);
		}
	}
	return $flux;
}
function formslies_forms_calcule_valeur_en_clair($flux){
	if ($flux['args']['type_champ']=='formslies') {
			$contexte = array();
			$contexte['id_donnee'] = intval($flux['data']);
			include_spip('public/assembler');
			$flux['data'] = recuperer_fond('formslies_valeur',$contexte);

	}
	return $flux;
}
?>