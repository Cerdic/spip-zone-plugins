<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

function exec_liste_auteurs_dist() {
	// si pas autorise : message d'erreur
	if (!autoriser('voir', 'nom')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$id_groupe=_request('id_groupe');
	$id_tableau=_request('id_tableau');
	$where=_request('where');
	$crit=_request('crit');
	$order=_request('order');
	$debut=_request('debut');
	$pagination=_request('pagination');
	
	
	$s = sql_select('*', 'spip_auteurs', array($where), array(), array($crit.' '.$order), $debut.', '.$pagination);
	
	$retour.='<table id="'.$id_tableau.'"><thead>
	<tr>
		<th><a href="?exec=groupes&page=liste_auteurs_groupe&id_groupe='.$id_groupe.'&debut='.$debut.'&crit=id_auteur&order=';
	if($crit == 'id_auteur' && $order == 'ASC')
		$retour .= 'DESC';
	else 
		$retour .= 'ASC';
	$retour .='">'._T('id').'</a></th>
		<th><a href="?exec=groupes&page=liste_auteurs_groupe&id_groupe='.$id_groupe.'&debut='.$debut.'&crit=nom&order=';
	if($crit == 'nom' && $order == 'ASC')
		$retour .= 'DESC';
	else 
		$retour .= 'ASC';
	
	$retour .='">'._T('nom').'</th>
		<th><a href="?exec=groupes&page=liste_auteurs_groupe&id_groupe='.$id_groupe.'&debut='.$debut.'&crit=login&order=';
	if($crit == 'login' && $order == 'ASC')
		$retour .= 'DESC';
	else 
		$retour .= 'ASC';
		
	$retour .='">'._T('login').'</th>
		<th><a href="?exec=groupes&page=liste_auteurs_groupe&id_groupe='.$id_groupe.'&debut='.$debut.'&crit=email&order=';
	if($crit == 'email' && $order == 'ASC')
		$retour .= 'DESC';
	else 
		$retour .= 'ASC';
	$retour .='">'._T('email').'</th>
	</tr></thead><tbody>';
	while($r = sql_fetch($s)) {
		$retour.= "
	<tr>
		<td>".$r['id_auteur']."</td>
		<td>".$r['nom']."</td>
		<td>".$r['login']."</td>
		<td>".$r['email']."</td>
	</tr>";
	}
	$retour.='</tbody></table>';
	
	echo $retour;
} 
?>