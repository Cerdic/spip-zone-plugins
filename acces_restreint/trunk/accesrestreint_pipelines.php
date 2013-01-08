<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Ajouter la boite des zones sur la fiche auteur
 *
 * @param string $flux
 * @return string
 */
function accesrestreint_affiche_milieu($flux){
	if ($e = trouver_objet_exec($flux['args']['exec'])
	  AND $e['type'] == 'auteur'
	  AND $e['edition'] == false) {
		
		$id_auteur = $flux['args']['id_auteur'];

		$ins = recuperer_fond('prive/squelettes/inclure/acces_auteur',array('id_auteur'=>$id_auteur));
		if (($p = strpos($flux['data'],"<!--affiche_milieu-->")) !== false)
			$flux['data'] = substr_replace($flux['data'],$ins,$p,0);
		else
			$flux['data'] .= $ins;
		
	}
	return $flux;
}

/**
 * Ajouter la boite des zones sur la fiche de rubrique
 *
 * @param string $flux
 * @return string
 */
function accesrestreint_affiche_gauche($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
	  AND $e['type'] == 'rubrique'
	  AND $e['edition'] == false
	  AND $id_rubrique = $flux['args']['id_rubrique']){
		if (autoriser('administrer', 'zone', 0)) {
			$flux['data'] .= recuperer_fond('prive/squelettes/inclure/acces_rubrique', array('id_rubrique'=>$id_rubrique));
		}
	}
	return $flux;
}

/**
 * Detecter les demande d'acces aux pages restreintes
 * et re-orienter vers une 401 si necessaire
 *
 * @param array $contexte
 * @return array
 */
function accesrestreint_page_indisponible($contexte){
	if ($contexte['status']=='404'){
		$objet = "";
		if (isset($contexte['type'])) $objet = $contexte['type'];
		elseif (isset($contexte['type-page'])) $objet = $contexte['type-page'];
		elseif(isset($contexte['fond_erreur'])) {
			include_spip('inc/urls');
			define('_DEFINIR_CONTEXTE_TYPE_PAGE',true);
			$c2 = $contexte;
			list($fond2,$c2,$url_redirect) = urls_decoder_url(nettoyer_uri(),$contexte['fond_erreur'],$c2,true);
			$objet = $c2['type-page'];
		}
		if ($objet){
			$table_sql = table_objet_sql($objet);
			$id_table_objet = id_table_objet($objet);
			if ($id = intval($contexte[$id_table_objet])){

				$publie = true;
				if (include_spip("base/objets")
				  AND function_exists("objet_test_si_publie")){
					$publie = objet_test_si_publie($objet,$id);
				}
				else {
					$trouver_table = charger_fonction('trouver_table','base');
					$desc = $trouver_table($table_sql);
					if (isset($desc['field']['statut'])){
						$statut = sql_getfetsel('statut', $table_sql, "$id_table_objet=".intval($id));
						if ($statut!='publie')
							$publie = false;
					}
				}

				include_spip('inc/autoriser');
				if ($publie AND !autoriser('voir',$objet,$id)){
					// c'est un contenu restreint
					$contexte['status'] = '401';
					$contexte['code'] = '401 Unauthorized';
					$contexte['fond'] = '401';
					$contexte['erreur'] = _T('accesrestreint:info_acces_restreint');
					$contexte['cible'] = self();
					if (!isset($contexte['objet'])){
						$contexte['objet'] = $objet;
						$contexte['id_objet'] = $id;
					}
				}
			}
		}
	}
	return $contexte;
}

?>
