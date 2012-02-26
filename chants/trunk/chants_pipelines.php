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

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	
	return $flux;
}

?>