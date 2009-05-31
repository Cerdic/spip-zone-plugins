<?php
/* Ajout du champ dans la liste en mode création formulaire*/
function ctrlforms_forms_types_champs($flux){
	$flux['ctrlpass']=_T('ctrlforms:ctrl_pass');
	return $flux;
}


function ctrlforms_forms_bloc_edition_champ($flux){
	$row = $flux['args']['row'];
	$type = $row['type'];

	if (in_array($type,array('ctrlpass'))){

		$action_link = $flux['args']['action_link'];
		$redirect = $flux['args']['redirect'];
		$idbloc = $flux['args']['idbloc'];
		$id_form = $row['id_form'];
		$champ = $row['champ'];
		$titre = $row['titre'];
		$obligatoire = $row['obligatoire'];
		$extra_info = $row['extra_info'];
		$specifiant = $row['specifiant'];
		$public = $row['public'];
		$aide = $row['aide'];
		$html_wrap = $row['html_wrap'];
		
		
		if (!$structure){
			include_spip("inc/forms");
			$structure = Forms_structure($id_form);
		}
		
		$out = $flux['data'];
		
		$out .= "<label for='controle_$champ'>"._T('ctrlforms:sel_ctrlfield')."</label> :";
		$out .= " &nbsp;<select name='controle_$champ' id='controle_$champ' class='fondo verdana2'>\n";
		$out .= "<option value=''>"._T('ctrlforms:sel_ctrlfielddefault')."</option>\n";
		foreach($structure as $champliste=>$infos){
			$type = $infos['type'];
			if($type!='ctrlpass'){	
				$selected = ($champliste == $row['extra_info']) ? " selected='selected'": "";
				$out .= "<option value='$champliste'$selected>".$type."</option>\n";
			}
		}
		$out .= "</select>";
		$out .= "<br />\n";
		
		$flux['data'] = $out;
	}
	return $flux;
}


function ctrlforms_forms_update_edition_champ($flux){
	$row = $flux['args']['row'];
	$type = $row['type'];
	$champ = $row['champ'];
	$id_form = $row['id_form'];
	if (in_array($type,array('ctrlpass'))){
		if ($s = _request("controle_$champ")){
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


function ctrlforms_forms_input_champs($flux){
	static $vu=array();
	$type = $flux['args']['type'];
	if (in_array($type,array('ctrlpass')) AND (_DIR_RESTREINT OR _request('exec')!=='forms_edit')) {
		$id_form = $flux['args']['id_form'];
		$champ = $flux['args']['champ'];
		$extra_info = $flux['args']['extra_info'];
		$vu[$id_form][$type]=array(
			'id'=>extraire_attribut($flux['data'],'id'),
			'name'=>extraire_attribut($flux['data'],'name'),
			'value'=>extraire_attribut($flux['data'],'value'),
			'syst'=>$extra_info);
			
			$flux['data']="";
			
			$typefield = substr($vu[$id_form][$type][syst],0,8);
			
			if(strcmp($typefield,"password")==0){
				$flux['data'].="<input type=\"password\" name='".$champ."' id='input-".$id_form."-".$champ."' value=\""._request($champ, $c)."\" class='password formo' size='40' />";
			}
			else{
				$flux['data'].="<input type=\"text\" name='".$champ."' id='input-".$id_form."-".$champ."' value=\""._request($champ, $c)."\" class='password formo' size='40' />";
			}
			
	}
	return $flux;
}

function ctrlforms_forms_valide_conformite_champ($flux){
    $type = $flux['args']['type'];
    $info = $flux['args']['info'];
    $extrainfo = $infos['extra_info'];
    $champ = $flux['args']['champ'];
    $erreur=$flux['data'];
    if ($type == 'ctrlpass') {
					if($extrainfo){
						if( _request($extrainfo)!=_request($champ)) $erreur[$champ] = 'Le contenu des deux champs est diff&eacute;rent.';
					}
			}
    return $erreur;
}
?>