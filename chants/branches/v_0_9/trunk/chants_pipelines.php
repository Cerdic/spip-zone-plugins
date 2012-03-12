<?php
function chants_affiche_enfants($flux) {
        if ($e = trouver_objet_exec($flux['args']['exec'])
          AND $e['type'] == 'rubrique'
          AND $e['edition'] == false) {
                 
                $id_rubrique = $flux['args']['id_rubrique'];
       
                $bouton = '';
                if (autoriser('creerchantdans','rubrique', $id_rubrique)) {
                        $bouton .= icone_verticale(_T('chant:icone_creer_chant'), generer_url_ecrire('chant_edit', "id_rubrique=$id_rubrique"), "chant-24.png", "new", 'right')
                                        . "<br class='nettoyeur' />";
                }
               
                $lister_objets = charger_fonction('lister_objets','inc');      
                $flux['data'] .= $lister_objets('chants', array('titre'=>_T('chant:titre_chants_rubrique') , 'id_rubrique'=>$id_rubrique, 'par'=>'titre'));
                $flux['data'] .= $bouton;
               
        }
        return $flux;
}

function chants_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les chants
	if ($e['type'] == 'chant' AND !$e['edition']) {
		$texte = recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']],
			#'editable'=>autoriser('associerauteurs',$e['type'],$e['id_objet'])?'oui':'non'
		));
	}

//	 chants sur les auteurs
	if ($flux['args']['exec'] == 'auteur') {
		$texte = recuperer_fond('prive/objets/liste/chants', array('id_auteur' => $flux['args']['id']));
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	
	return $flux;
}

/**
 * Afficher les interventions et objets en lien
 * avec un auteur (sur sa page)
 *
 * @param array $flux
 * @return array
 */
function chants_affiche_auteurs_interventions($flux){

	if ($id_auteur = intval($flux['args']['id_auteur'])){
		include_spip('inc/message_select');
		// Chants écrits par l'auteur
		if ($GLOBALS['meta']['messagerie_agenda'] != 'non'
		AND $id_auteur != $GLOBALS['visiteur_session']['id_auteur']
		AND autoriser('ecrire', '', '', $flux['args']['auteur'])
		) {
		  $flux['data'] .= recuperer_fond('prive/squelettes/inclure/organiseur-interventions',array('id_auteur'=>$id_auteur));
		}
	}
  return $flux;
}

?>