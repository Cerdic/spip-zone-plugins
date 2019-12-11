<?php
/**
 * Utilisations de pipelines par Pensebete
 *
 * @plugin     Pensebetes
 * @copyright  2019
 * @author     Vincent CALLIES
 * @licence    GNU/GPL
 * @package    SPIP\Pensebete\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function pensebetes_affiche_gauche($flux){
    if ($flux['args']['exec'] == 'auteur'){
        $flux['data'] .= debut_cadre_relief('pensebete-24.png',true,'',_T('pensebete:titre_pensebetes')); 
        $flux['data'] .= recuperer_fond('prive/squelettes/inclure/pensebetes_donnes',array('id_auteur'=>$flux['args']['id_auteur']));
        $flux['data'] .= recuperer_fond('prive/squelettes/inclure/pensebetes_recus',array('id_auteur'=>$flux['args']['id_auteur']));
        $flux['data'] .= fin_cadre_relief(true);
    }
    return $flux;
}


/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function pensebetes_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

/*	// pensebetes sur les articles
	if ($e['type'] == 'article' AND !$e['edition']) {
		$texte = recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'pensebetes',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']],
			'id_donneur' => $GLOBALS['visiteur_session']['id_auteur'],
			#'editable'=>autoriser('associerpensebetes',$e['type'],$e['id_objet'])?'oui':'non'
		));
	}

	// pensebetes sur les rubriques
	if ($e['type'] == 'rubrique' AND !$e['edition']) {
		$texte = recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'pensebetes',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']],
			'id_donneur' => $GLOBALS['visiteur_session']['id_auteur'],
			#'editable'=>autoriser('associerpensebetes',$e['type'],$e['id_objet'])?'oui':'non'
		));
	}
*/
	// pensebetes sur le mur d'accueil de l'auteur
	if ($flux['args']['exec']=='accueil'){
		$ajout= recuperer_fond('prive/squelettes/inclure/pensebete_accueil',
				array(
					'titre' => _T('responsable:titre_fourmis'),
					'id_receveur' => $GLOBALS['visiteur_session']['id_auteur']
					#'editable'=>autoriser('associerpensebetes',$e['type'],$e['id_objet'])?'oui':'non'
					)
				);
				$flux['data']=$ajout.$flux['data'];
		}
	if ($texte) {
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}


