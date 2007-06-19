<?php 
	
function inscription2_fiche_adherent($id_adherent){	
	$aux = "";
	$toto = "";
	$var_user = array();
	$auteur = array();
	//liste d'elements a afficher
	foreach(lire_config('inscription2') as $cle => $val) {
		//Si la $cle est marqué pour être affichée mais pas modifiable
		if($val!='' and ereg("^.+_fiche.*$", $cle)){ 
			$aux = ereg_replace("_fiche.*", "", $cle);
			if($aux == 'username' or $aux == 'nom' or $aux == 'email')
				if($aux == 'username')
					$auteur[] = 'a.login';
				else
					$auteur[] = 'a.'.$aux;
			else
				$auteur[] = 'b.'.$aux;
		}
	}
	if(is_array($auteur)){
		$aux = join(', ',$auteur);
		$aux = spip_query("SELECT $aux FROM spip_auteurs a LEFT JOIN spip_auteurs_elargis b ON a.id_auteur=b.id_auteur WHERE a.id_auteur = $id_adherent" );
		$aux = spip_fetch_array($aux);
		if(is_array($aux)){
			$toto .= debut_cadre_enfonce("../"._DIR_PLUGIN_INSCRIPTION2."/images/inscription2_icone.png", true, "", bouton_block_invisible("fiche")._T('inscription2:infos_adherent'));
			$toto .= debut_block_invisible("fiche");
			foreach($aux as $cle => $val){
				$toto.= '<div><strong>'._T('inscription2:'.$cle).' : </strong>';
				if($cle == 'nom' or $cle == 'email' or $cle =='login')
					$toto.= '<span class="crayon spip_auteurs-'.$cle.'-'.$id_adherent.' ">'.$val.'</span>';
				else
					$toto.= '<span class="crayon spip_auteurs_elargis-'.$cle.'-'.$id_adherent.' ">'.$val.'</span>';
				$toto.= "\n</div>";  
			}
			$toto .= fin_block();
			$toto .= fin_cadre_enfonce(true);
		}
	}
	return $toto;
}
?>