<?php

function exec_editer_adherent(){
	$id = _request('id');
	$act = _request('act');

	if(!isset($id))
		return _T('inscription2:erreur');
		
	if($act=='val'){
		foreach(lire_config('inscription2') as $cle => $val){
			if($val!='' and !ereg("^(accesrestreint|domaine|categories|zone|news).*$", $cle)){
				$cle = ereg_replace("^username.*$", "login", $cle);
				$cle = ereg_replace("_(fiche|table).*$", "", $cle);
				if($cle == 'nom' or $cle == 'email' or $cle == 'login')
					$var_user['a.'.$cle] =  '`'.$cle.'` = \''.$_POST[$cle].'\'';
				elseif(ereg("^statut_rel.*$", $cle))
					$var_user['b.statut_relances'] =  '`statut_relances` = \''.$_POST['statut_relances'].'\'';
				else
					$var_user['b.'.$cle] =  '`'.$cle.'` = \''.$_POST[$cle].'\'';
			}
		}
		$q1 = spip_query("update spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur=b.id_auteur set ".join(', ', $var_user)." where a.`id_auteur`='$id'");
		if($q1)
			$message = "adherent mis à jour";
	}


	foreach(lire_config('inscription2') as $cle => $val){
		if($val!='' and !ereg("^(accesrestreint|domaine|categories|zone|news).*$", $cle)){
			$cle = ereg_replace("^username.*$", "login", $cle);
			$cle = ereg_replace("_(fiche|table).*$", "", $cle);
			if($cle == 'nom' or $cle == 'email' or $cle == 'login')
				$var_user['a.'.$cle] = '0';
			elseif(ereg("^statut_rel.*$", $cle))
				$var_user['b.statut_relances'] = '1';
			else 
				$var_user['b.'.$cle] = '1';
		}
		elseif($cle=='accesrestreint'){
			$acces = spip_query("select id_zone from spip_zones_auteurs where id_auteur = $id");
		}
		elseif($cle=='newsletter'){
			$news = spip_query("select id_liste from spip_auteurs_listes where id_auteur = $id");
		}
	}

	$query = spip_query('select '.join(', ', array_keys($var_user))." from spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur = b.id_auteur where a.id_auteur= $id");

	$query = spip_fetch_array($query);
	
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
		echo "<tr><td><strong>"._T('inscription2:'.$cle)."</strong></td>";
		echo "<td><input type='text' name='$cle' value='$val'><br /></td><tr/>"; 
	}
	echo "</table>";
	echo "<input type='submit' value='Valider'></form>";
	fin_page();
}
?>