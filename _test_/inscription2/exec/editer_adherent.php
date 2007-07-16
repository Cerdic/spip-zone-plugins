<?php

function exec_editer_adherent(){
	
	global $connect_statut;
	global $connect_toutes_rubriques;
	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	$id = _request('id');
	$act = _request('act');

	if(!isset($id))
		return _T('inscription2:erreur');
		
	if($act=='val'){
		foreach(lire_config('inscription2') as $cle => $val){
			if($val!='' and !ereg("^(accesrestreint|categories|zone|news).*$", $cle) and $cle != 'statut'){
				$cle = ereg_replace("^username.*$", "login", $cle);
				$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
				if($cle == 'nom' or $cle == 'email' or $cle == 'login' )
					$var_user['a.'.$cle] =  '`'.$cle.'` = \''.$_POST[$cle].'\'';
				elseif(ereg("^statut_int.*$", $cle))
					$var_user['b.statut_interne'] =  '`statut_interne` = \''.$_POST['statut_interne'].'\'';
				else
					$var_user['b.'.$cle] =  '`'.$cle.'` = \''.$_POST[$cle].'\'';
			}
			elseif ($val!='' and $cle == 'accesrestreint'){
				$aux = spip_query("select id_zone from spip_zones_auteurs where id_auteur = $id");
				while($q = spip_fetch_array($aux))
					$acces[]=$q['id_zone'];
				$acces_array = $_POST['acces'];
				if(!empty($acces) and empty($acces_array))
					spip_query("delete from spip_zones_auteurs where id_auteur = $id");
				elseif(empty($acces) and !empty($acces_array))
					spip_query("insert into spip_zones_auteurs (id_zone, id_auteur) values (".join(", $id), (", $acces_array).", $id)");
				elseif(!empty($acces) and !empty($acces_array)){
					$diff1 = array_diff($acces_array, $acces);
					$diff2 = array_diff($acces, $acces_array);
					if (!empty($diff1))
						spip_query("insert into spip_zones_auteurs (id_zone, id_auteur) values (".join(", $id), (", $diff1).", $id)");
					if(!empty($diff2))
						foreach($diff2 as $val)
							spip_query("delete from spip_zones_auteurs where id_auteur= $id and id_zone = $val");
				}
			}elseif ($val!='' and $cle == 'newsletter'){
				$aux = spip_query("select id_liste from spip_auteurs_listes where id_auteur = $id");
				while($q = spip_fetch_array($aux))
					$listes[]=$q['id_liste'];
				$listes_array = $_POST['news'];
				if(!empty($listes) and empty($listes_array))
					spip_query("delete from spip_auteurs_listes where id_auteur = $id");
				elseif(empty($listes) and !empty($listes_array))
					spip_query("insert into spip_auteurs_listes (id_liste, id_auteur) values (".join(", $id), (", $listes_array).", $id)");
				elseif(!empty($listes) and !empty($listes_array)){
					$diff1 = array_diff($listes_array, $listes);
					$diff2 = array_diff($listes, $listes_array);
					if (!empty($diff1))
						spip_query("insert into spip_auteurs_listes (id_liste, id_auteur) values (".join(", $id), (", $diff1).", $id)");
					if(!empty($diff2))
						foreach($diff2 as $val)
							spip_query("delete from spip_auteurs_listes where id_auteur= $id and id_liste = $val");
				}
			}
		}
		$q1 = spip_query("update spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur=b.id_auteur set ".join(', ', $var_user)." where a.`id_auteur`='$id'");
		if($q1)
			$message = "adherent mis à jour";
	}

	$var_user['b.id'] = '0';
	foreach(lire_config('inscription2') as $cle => $val){
		if($val!='' and !ereg("^(accesrestreint|categories|zone|news).*$", $cle) and $cle != 'statut'){
			$cle = ereg_replace("^username.*$", "login", $cle);
			$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
			if($cle == 'nom' or $cle == 'email' or $cle == 'login')
				$var_user['a.'.$cle] = '0';
			elseif(ereg("^statut_int.*$", $cle))
				$var_user['b.statut_interne'] = '1';
			elseif($cle == 'pays'){
				$var_user['c.pays'] = '1';
				$var_user['c.id as id_pays'] = '1';
			}else 
				$var_user['b.'.$cle] = '1';
		}
		elseif($cle=='accesrestreint' and $val != ''){
			$aux1=array();
			$aux2=array();
			$zones = spip_query("select id_zone, titre from spip_zones");
			$acces = spip_query("select id_zone from spip_zones_auteurs where id_auteur = $id");
			while($q = spip_fetch_array($acces))
				$aux1[]=$q['id_zone'];
			while($q = spip_fetch_array($zones))
				$aux2[] = $q;
		}
		elseif($cle=='newsletter' and $val != ''){
			$aux3=array();
			$aux4=array();
			$news = spip_query("select id_liste, titre from spip_listes");
			$listes = spip_query("select id_liste from spip_auteurs_listes where id_auteur = $id");
			while($q = spip_fetch_array($listes))
				$aux3[]=$q['id_liste'];
			while($q = spip_fetch_array($news))
				$aux4[] = $q;
		}
	}
	
	if($var_user['c.pays'])
		$query = spip_query('select '.join(', ', array_keys($var_user))." from spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur left join spip_pays c on b.pays=c.id where a.id_auteur= $id");
	else
		$query = spip_query('select '.join(', ', array_keys($var_user))." from spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur where a.id_auteur= $id");
	$query = spip_fetch_array($query);
	
	if($query['id'] == NULL)
			$id_elargi =spip_query("INSERT INTO spip_auteurs_elargis (id_auteur) VALUES ($id)");
	
	debut_page(_T('inscription2:editer_adherent'), "", "");
	debut_gauche();
	echo '<div style="width:125pt; text-align: left; border-width:1; border-color:black; border-style :groove; background-color : #E0E0E0 ;" >
	<strong>'._T('inscription2:raccourcis').'</strong>
	<ul style="text-align: left; ">
		<li><a href="?exec=cfg&cfg=inscription2">'._T('inscription2:configuration').'</a></li>
		<li><a href="?exec=inscription2_adherents">'._T('inscription2:liste_adherents').'</a></li>
	</ul>
	</div>';
	debut_droite();
	echo "<form name='adherent' method='post' action='?exec=editer_adherent&act=val&id=$id'>";
	echo "<table>";
	foreach ($query as $cle => $val){
		if($cle == 'id' or $cle == 'id_pays')
			continue;
		if($cle == 'pays'){
			echo "<tr><td><strong>"._T('inscription2:'.$cle)."</strong></td>"
				. "<td><select name='$cle' id='$cle' >";
			include(_DIR_PLUGIN_INSCRIPTION2."/inc/pays.php");
			foreach($liste_pays as $cle=> $val){
				if ($cle == $query['id_pays'])
					echo "<option value='$cle' selected>$val</option>";
				else 
					 echo "<option value='$cle'>$val</option>";
			}
			echo "</select></td></tr>";
		}elseif($cle=='publication'){
			if ($val == 'on')	
				$val = 'checked';
			else
				$val ='';
			
			echo "<tr><td><strong>"._T('inscription2:'.$cle)."</strong></td>";
			echo "<td><input type='checkbox' name='$cle' $val><br /></td><tr/>"; 	
		}elseif ($cle != 'statut'){
			echo "<tr><td><strong>"._T('inscription2:'.$cle)."</strong></td>";
			if($cle == 'adresse' OR $cle == 'divers')
				echo "<td><textarea name='$cle'>$val</textarea><br /></td><tr/>"; 
			else
				echo "<td><input type='text' name='$cle' value='$val'><br /></td><tr/>"; 
		}
	}
	if($news){
		echo "<tr><td><strong>"._T('inscription2:newsletter')."</strong></td><td>";
		echo "<select name='news[]' id='news' multiple>";
		foreach($aux4 as $val){
			if (in_array($val['id_liste'], $aux3))
				echo "<option value='".$val['id_liste']."' selected>".$val['titre']."</option>";
			else 
				echo "<option value='".$val['id_liste']."'>".$val['titre']."</option>";
		}
		echo "</select><br/><a onclick=\"$('#news').find('option').attr('selected', false);\">"._T('inscription2:deselect_listes')."</a> </small><br /></td></tr>";
	}
	if($zones){
		echo "<tr><td><strong>"._T('inscription2:accesrestreint')."</strong></td><td>";
		echo "<select name='acces[]' id='acces' multiple>";
		foreach($aux2 as $val){
			if (in_array($val['id_zone'], $aux1))
				echo "<option value='".$val['id_zone']."' selected>".$val['titre']."</option>";
			else 
				echo "<option value='".$val['id_zone']."'>".$val['titre']."</option>";
		}
		echo "</select><br/><a onclick=\"$('#acces').find('option').attr('selected', false);\">"._T('inscription2:deselect_listes')."</a> </small><br /></td></tr>";
	}
	echo "</table>";
	echo "<input type='submit' value='Valider'></form>";
	fin_page();
}
?>