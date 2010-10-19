<?php 
// un profil public editable avec les crayons
// marche avec profil_adherent.html

include_spip('inc/filtres');
include_spip('inc/headers');
	
	if(!$GLOBALS['auteur_session'])
		redirige_par_entete('./');
	$id_auteur=intval($contexte_inclus['id_auteur']);
	if(!$id_auteur)
		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	$aux = "";
	$auteur = array();
	//liste d'elements a afficher
	$auteur['b.id'] = "0";
	foreach(lire_config('inscription2') as $cle => $val) {
		//Si la $cle est marqué pour être affichée mais pas modifiable
		if($val!='' and ereg("^.+_fiche$", $cle)){ 
			$aux = str_replace("_fiche", "", $cle);
			if($aux == 'username' or $aux == 'nom' or $aux == 'email')
				if($aux == 'username')
					$auteur['a.login'] = "0";
				else
					$auteur['a.'.$aux] = "0";
			else
				$auteur['b.'.$aux] = "0";
		}elseif($val!='' and ereg("^.+_fiche_mod$", $cle)){ //champ modifiable
			$aux = str_replace("_fiche_mod", "", $cle);
			if($aux == 'username' or $aux == 'nom'or $aux == 'email'){
				if($aux == 'username')
					$auteur['a.login'] = "1";
				else
					$auteur['a.'.$aux] = "1";
			}else
				$auteur['b.'.$aux] = "1";
		}
	}

	if(is_array($auteur)){
		$aux = join(', ',array_keys($auteur));
		$aux = spip_query("SELECT $aux FROM spip_auteurs a LEFT JOIN spip_auteurs_elargis b ON a.id_auteur=b.id_auteur WHERE a.id_auteur = ".$id_auteur );
		$aux = spip_fetch_array($aux);
	}
	
	if(!is_array($aux)){
		echo _T('inscription2:erreur_user_not_found')."</div>";
		redirige_par_entete('./');
		return;
	}
	
	if(is_array($aux)){
		// sur la table spip_auteurs_elargis, s'il n'existe pas on l'ajoute.
		if($aux['id'] == NULL){
			spip_query("INSERT INTO spip_auteurs_elargis (id_auteur) VALUES ($id_auteur)");
			if(is_array($auteur)){
				$aux = join(', ',array_keys($auteur));
				$aux = spip_query("SELECT $aux FROM spip_auteurs a LEFT JOIN spip_auteurs_elargis b ON a.id_auteur=b.id_auteur WHERE a.id_auteur = ".$id_auteur );
				$aux = spip_fetch_array($aux);
			}
		}foreach($aux as $cle => $val){
			if($cle != 'id'){
				echo '<div><strong>'._T('inscription2:'.$cle).' : </strong>';
				if($cle == 'login' or $cle == 'nom' or $cle == 'email'){
					if($auteur['a.'.$cle]=="1")
						echo '<span class="crayon auteur-'.$cle.'-'.$id_auteur.' ">'.sinon($val,'...').'</span></div>';  
					else 
						echo '<span>'.sinon($val,'...').'</span></div>';  
				}else{
					if( $auteur['b.pays']=="1" and $cle == 'pays')
						echo '<span class="crayon auteurs_elargi-'.$cle.'-'.$aux['id'].'">'.recuperer_fond('vues/pays', array('id'=> $val)).'</span></div>'; 
					elseif( $auteur['b.pays_pro']=="1" and $cle == 'pays_pro')
						echo '<span class="crayon auteurs_elargi-'.$cle.'-'.$aux['id'].'">'.recuperer_fond('vues/pays', array('id'=> $val)).'</span></div>'; 
					elseif($auteur['b.'.$cle]=="1")
						echo '<span class="crayon auteurs_elargi-'.$cle.'-'.$aux['id'].'">'.sinon($val,'...').'</span></div>';  
					
					else
						echo '<span>'.sinon($val,'...').'</span></div>';  
				}
			}
		}
	}
?>