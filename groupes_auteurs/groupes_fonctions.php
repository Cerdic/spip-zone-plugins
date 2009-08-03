<?php 
function decouper_action($action, $param) {
	$params = explode('&amp;', substr($action, 3));
	$actions = array();
	foreach($params as $v) {
		$array = explode('=', $v);
		$actions[$array[0]] = $array[1];
	}
	if(isset($actions[$param])) {
		return $actions[$param];
	} else {
		return '';
	}
}

function filtre_tableau_contient($tableau, $valeur) {
	if(empty($tableau)) {
		spip_log('tableau vide : '.$valeur, 'groupes');
		return false;
	}
	spip_log('tableau NON VIDE : '.$valeur, 'groupes');
	foreach($tableau as $v) {
		if($v == $valeur) {
			spip_log('valeur selectionnee :'.$v, 'groupes');
			return true;
		}	
	}
	return false;
}

function table_liste_auteurs($id_groupe, $debut, $crit, $order, $id_tableau,$exc, $callback) {
	$not = '';
	if($exc)
		$not = 'NOT ';
	$where = array('id_auteur '.$not.'IN(SELECT id_auteur FROM spip_groupes_auteurs WHERE id_groupe='.$id_groupe.')');

	$pagination = 50;
	//VERIFICATIONS
	if($debut == 0) 
		$debut = 0;
	else 
		$debut = $debut;
	if($crit == '') 
		$crit = 'id_auteur';
	else
		$crit = $crit;
	if($order == '')
		$order = 'ASC';
	else
		$order = $order;
		

	//PAGINATION	
	$nb_aut = sql_countsel('spip_auteurs', $where);
	for($i=0;$i<ceil($nb_aut/$pagination);$i++) {
		$j = $i * $pagination;
		$k = $i + 1;
		if($debut!=$j) 
			$flux .= '<a href="javascript:changer_page_'.$id_tableau.'('.$j.')">'.$k.'</a>&nbsp;';
		else
			$flux .= $k.'&nbsp;';
	}
	
	
	//LISTE
	$flux .= "<script type=\"text/javascript\">
	function changer_page_$id_tableau(deb) {
	$.post('?exec=liste_auteurs', {id_groupe : ".$id_groupe.", debut:deb, crit:'".$crit."', order:'".$order."', id_tableau:'".$id_tableau."', where:'".$where[0]."', pagination:'".$pagination."'}, function(data) {
			$('#div_$id_tableau').replaceWith(data);
			$callback();
})};
	changer_page_$id_tableau(0);
	</script>
	<div id=\"div_$id_tableau\"></div>
	";
	return $flux;
}

function div_lien($i, $lien = array()) {
	$res = sql_select('*', 'spip_groupes');
	$liste_chp = array('nom', 'bio', 'email', 'nom_site', 'url_site', 'login', 'pgp');
	include_spip('ldaplus_fonctions');
	foreach(lister_champs_auteurs_elargis() as $k=>$v) {
		$liste_chp[] = $k;
	}
	$retour .= '<li id="divlien_'.$i.'">'._T('groupes:ajouter_lien');
	$retour .= '<select name="chp[]">';
	foreach($liste_chp as $v) {
		$retour .= '<option value="'.$v.'"';
		if( ($lien != array()) && ($v == $lien[0]) )
			$retour .= ' selected ';
		$retour .= '>'.$v.'</option>';
	} 
	$retour .= '</select> = <input type="text" name="valeur[]"';
	if($lien != array())
		$retour .='value="'.$lien[1];
	$retour .='"/>';
	$retour .= _T('groupes:et_groupe');
	$retour .= '<select name="groupe[]">';
	while($r = sql_fetch($res)) {
		$retour .= '<option value="'.$r['id_groupe'].'"';
		if( ($lien != array()) && ($r['id_groupe'] == $lien[2]) )
			$retour .= ' selected ';
		$retour .= '>'.$r['nom'].'</option>';
	}
	$retour .= '</select><a href="javascript:supprimer('.$i.')">'._T('groupes:suppr').'</a></li>';
	
	return $retour;
}

?>