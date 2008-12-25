<?php

// Ajoute le bouton d'amin aux webmestres

if (!defined("_ECRIRE_INC_VERSION")) return;
function inscription2_ajouter_boutons($boutons_admin){
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

		$boutons_admin['auteurs']->sousmenu['inscription2_adherents']= new Bouton(
		"../"._DIR_PLUGIN_INSCRIPTION2."images/inscription2_icone.png", // icone
		_T("inscription2:adherents") //titre
		);
	}
	return $boutons_admin;
}

function inscription2_affiche_milieu($flux){
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
			$echec = array();
				foreach(lire_config('inscription2',array()) as $cle => $val){
					if($val!='' and !ereg("^(accesrestreint|categories|zone|news).*$", $cle)){
						$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
						if($cle == 'nom' or $cle == 'email' or $cle == 'login' or $cle == 'statut_nouveau'){
						}
						else
							$var_user[$cle] = sql_quote(_request($cle));
					}
				}
				if (!sql_getfetsel('id_auteur','spip_auteurs_elargis','id_auteur='.$id_auteur)){
					//insertion de l'id_auteur dans spip_auteurs_elargis sinon on peut pas proceder a l'update
					$id_elargi = sql_insertq("spip_auteurs_elargis",array('id_auteur'=> $id_auteur));
				}
				sql_update("spip_auteurs_elargis",$var_user,"id_auteur=$id_auteur");
			
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