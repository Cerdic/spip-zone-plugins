<?php

// Ajoute le bouton d'amin aux webmestres

if (!defined("_ECRIRE_INC_VERSION")) return;
function Inscription2_ajouter_boutons($boutons_admin){
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

		$boutons_admin['auteurs']->sousmenu['inscription2_adherents']= new Bouton(
		"../"._DIR_PLUGIN_INSCRIPTION2."images/inscription2_icone.png", // icone
		_T("inscription2:adherents") //titre
		);
	}
	return $boutons_admin;
}

function Inscription2_affiche_milieu($flux){
	switch($flux['args']['exec']) {	
			case 'auteur_infos':
				include_spip('inc/inscription2_gestion');
				$id_auteur = $flux['args']['id_auteur'];
				$flux['data'] .= inscription2_ajouts($id_auteur);
				break;
			default:
				break;
		}
	return $flux;
}

function Inscription2_header_prive($flux){
	if ((_request('exec')=='ajouter_adherent') || (_request('exec')=='inscription2_adherents')){
		$flux .= "<script type='text/javascript' src='".find_in_path('lib/jquery-validate/jquery.validate.pack.js')."'></script>\n";
		$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_INSCRIPTION2."javascript/md5_inscription2.js'></script>\n";
	}
	return $flux;
}

function inscription2_affichage_final($page){
	// regarder si la page contient le formulaire inscription2
	if (!strpos($page, 'id="inscription"'))
		return $page;
	$page = inscription2_preparer_page($page);
		return $page;
}

function inscription2_preparer_page($page) {

	$jqueryvalidate = find_in_path('lib/jquery-validate/jquery.validate.pack.js');

	$incHead = <<<EOS
<script type='text/javascript' src='$jqueryvalidate'></script>
EOS;
	return substr_replace($page, $incHead, strpos($page, '</head>'), 0);
}

// ajouter les champs I2 sur le formulaire CVT editer_auteur
function inscription2_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		include_spip('public/assembler');
		include_spip('inc/legender_auteur_supp');
		spip_log('editer_contenu_objet sur auteur='.$flux['args']['contexte']['id_auteur']);
		$inscription2 = legender_auteur_supp_saisir($flux['args']['contexte']['id_auteur']);
		$flux['data'] = preg_replace('%(<li class="editer_pgp(.*?)</li>)%is', '$1'."\n".$inscription2, $flux['data']);
	}
	return $flux;
}

// ajouter les champs inscription2 soumis lors de la soumission du formulaire CVT editer_auteur
function inscription2_post_edition($flux){
	if ($flux['args']['table']=='spip_auteurs') {
		$id_auteur = $flux['args']['id_objet'];
		spip_log('post_edition sur id_auteur='.$id_auteur);
		spip_log("editer_auteur_supp_dits","inscription2");
			spip_log("editer_auteur_supp $r");
			$echec = array();
				foreach(lire_config('inscription2',array()) as $cle => $val){
					if($val!='' and !ereg("^(accesrestreint|categories|zone|news).*$", $cle)){
						$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
						if($cle == 'nom' or $cle == 'email' or $cle == 'login')
							$var_user['a.'.$cle] = sql_quote(_request($cle));
						elseif($cle == 'statut_nouveau'){
						}
						elseif(ereg("^statut_rel.*$", $cle))
							$var_user['b.statut_relances'] =  sql_quote(_request('statut_relances'));
						else
							$var_user['b.'.$cle] = sql_quote(_request($cle));
					}
					elseif ($val!='' and $cle == 'accesrestreint'){
						$aux = sql_select("id_zone","spip_zones_auteurs","id_auteur = $id_auteur");
						while($q = sql_fetch($aux))
							$acces[]=$q['id_zone'];
						$acces_array = _request('acces');
						if(!empty($acces) and empty($acces_array))
							sql_delete("spip_zones_auteurs","id_auteur = $id_auteur");
						elseif(empty($acces) and !empty($acces_array))
							spip_query("insert into spip_zones_auteurs (id_zone, id_auteur) values (".join(", $id_auteur), (", $acces_array).", $id_auteur)");
						elseif(!empty($acces) and !empty($acces_array)){
							$diff1 = array_diff($acces_array, $acces);
							$diff2 = array_diff($acces, $acces_array);
							if (!empty($diff1))
								spip_query("insert into spip_zones_auteurs (id_zone, id_auteur) values (".join(", $id_auteur), (", $diff1).", $id_auteur)");
							if(!empty($diff2))
								foreach($diff2 as $val)
									spip_query("delete from spip_zones_auteurs where id_auteur= $id_auteur and id_zone = $val");
						}
					}
					elseif ($val!='' and $cle == 'newsletter'){
						$aux = sql_select("id_liste","spip_auteurs_listes","id_auteur = $id_auteur");
						while($q = sql_fetch($aux))
							$listes[]=$q['id_liste'];
						$listes_array = _request('news');
						if(!empty($listes) and empty($listes_array))
							sql_delete("spip_auteurs_listes","$id_auteur");
						elseif(empty($listes) and !empty($listes_array))
							spip_query("insert into spip_auteurs_listes (id_liste, id_auteur) values (".join(", $id_auteur), (", $listes_array).", $id_auteur)");
						elseif(!empty($listes) and !empty($listes_array)){
							$diff1 = array_diff($listes_array, $listes);
							$diff2 = array_diff($listes, $listes_array);
							if (!empty($diff1))
								spip_query("insert into spip_auteurs_listes (id_liste, id_auteur) values (".join(", $id_auteur), (", $diff1).", $id_auteur)");
							if(!empty($diff2))
								foreach($diff2 as $val)
									sql_delete("spip_auteurs_listes","$id_auteur and id_liste = $val");
						}
					}
				}
				if (!sql_getfetsel('id_auteur','spip_auteurs_elargis','id_auteur='.$id_auteur)){
					//insertion de l'id_auteur dans spip_auteurs_elargis sinon on peut pas proceder a l'update
					$id_elargi = sql_insertq("spip_auteurs_elargis",array('id_auteur'=> $id_auteur));
				}
				sql_update("spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur=b.id_auteur",$var_user, "b.id_auteur=$id_auteur");
			
			// Notifications, gestion des revisions, reindexation...
			pipeline('post_edition',
				array(
					'args' => array(
						'table' => 'spip_auteurs_elargis',
						'id_objet' => $id_auteur
					),
					'data' => $auteur
				)
			);
		
			$echec = $echec ? '&echec=' . join('@@@', $echec) : '';
	}
	return $flux;
}	
?>