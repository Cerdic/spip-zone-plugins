<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;

function ldaplus_champs_auteurs($flux) {
	$selections = unserialize(lire_meta('ldaplus_chp_auteur'));
	if(empty($GLOBALS['liste_chp_auteur'])) {
		return $flux;
	}
	foreach($GLOBALS['liste_chp_auteur'] as $v) {
		$flux .= "<tr><td>".$v."</td>\n
		<td>
		<select name=\"chp_aut[]\">
			<option value=\"\"></option>
			".faire_opt($selections[$v])."
		</select></td>\n";
	}
	return $flux;
}

function ldaplus_champs_auteurs_elargis($flux) {
	$selections = unserialize(lire_meta('ldaplus_chp_elargis'));	
	if(empty($GLOBALS['liste_chp_auteur_elargi'])) {
		return $flux;
	}
	foreach($GLOBALS['liste_chp_auteur_elargi'] as $k=>$v) {
		$flux .= "<tr><td>".$k."</td>\n
		<td>
		<select name=\"chp_elargis[]\">
			<option value=\"\"></option>
			".faire_opt($selections[$k])."
		</select></td>\n";
	}
	return $flux;
}

function ldaplus_memberof($flux) {
	$result = sql_select('*', 'spip_groupes');
	$memberof = unserialize(lire_meta('ldaplus_memberof'));
	$i=0;
	$flux .= '<ul id="li_liens">';

	if((count($memberof)==0) || !is_array($memberof)) {
		$flux .= '<li id="memberOf_0">';
		$flux .= $GLOBALS[$GLOBALS['idx_lang']]['lien_ldap'];
		$flux .= '<input type="text" name="memberOf_0"/>';
		$flux .= $GLOBALS[$GLOBALS['idx_lang']]['et_groupe'];
		$flux .='<select name="grp_0[]" multiple>';
		while($r = sql_fetch($result)) {
			$flux .= '<option value="'.$r['id_groupe'].'">'.$r['nom'].'</option>';
		}
		$flux .= '</select>';
		$flux .= '<a href="javascript:supprimer(0)">';
		$flux .= $GLOBALS[$GLOBALS['idx_lang']]['supprimer']; 
		$flux .= '</a></li>';
		$i+=1;
	} else {
	foreach($memberof as $k => $v) {
		$flux .= '<li id="memberOf_'.$i.'">';
		$flux .= $GLOBALS[$GLOBALS['idx_lang']]['lien_ldap'];
		$flux .= '<input type="text" name="memberOf_'.$i.'" value="'.$k.'"/>';
		$flux .= $GLOBALS[$GLOBALS['idx_lang']]['et_groupe'];
		$flux .='<select name="grp_'.$i.'[]" multiple>';
		while($r = sql_fetch($result)) {
			$flux .= '<option value="'.$r['id_groupe'].'"';
			if(in_array($r['id_groupe'], $v))
				$flux.= ' selected';
			$flux .= '>'.$r['nom'].'</option>';
		}
		$flux .= '</select>';
		$flux .= '<a href="javascript:supprimer('.$i.')">';
		$flux .= $GLOBALS[$GLOBALS['idx_lang']]['supprimer']; 
		$flux .= '</a></li>';
		$i+=1;
	}
	}
	$flux .= '</ul>';
	$flux .= '<input type="hidden" name="i_max" id="i_max" value="'.$i.'"/>';
	return $flux;
}

?>