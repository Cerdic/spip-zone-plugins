<?php

function vignettes_docs_auto_editer_contenu_objet($flux){
// ou utilisation extra https://contrib.spip.net/Modifier-un-formulaire-SPIP

/*    if ($flux['args']['type']=='illustrer_document') {
//    print_r($flux['args']['contexte']);
         $id_document = $flux["args"]["id_document"];
        $contexte = array('id_document'=>$id_document);
        $ajout = recuperer_fond('prive/squelettes/navigation/vignettes_docs_auto', $flux['args']['contexte']);
//        $flux['data'] = preg_replace('%(<li class="editer_email(.*?)</li>)%is', '$1'."\n".$openid, $flux['data']);
//        $flux['data'] = $ajout.$flux['data'];
        $flux['data'] = preg_replace('</div></form>', 
        $ajout."\n".'</div></form>',
        $flux['data']);
        }
        */
    return $flux;
}

// bouton sur les tableau de edition/mediatheque
//[(#PIPELINE{document_desc_actions,#ARRAY{args,#ARRAY{id_document,#ID_DOCUMENT,position,galerie},data,''}})]
function vignettes_docs_auto_document_desc_actions($flux){
$si_pdf = sql_select('extension', "spip_documents", "id_document=".$flux['args']['id_document']." and extension='pdf'");
if ($si_pdf and sql_count($si_pdf)>0){
		$texte = recuperer_fond(
				'prive/squelettes/navigation/vignettes_docs_auto',
				array(
					'id_document'=>$flux["args"]["id_document"]
				)
				
		);
$flux['data'] .= $texte;

}
	return $flux;
}


// bouton sur formulaire document_edit
function vignettes_docs_auto_affiche_milieu($flux){
// adapté de plugin mots/mots_pipeline
$en_cours = trouver_objet_exec($flux['args']['exec']);
//print_r($en_cours);
//print_r($flux['args']);

if ($en_cours = trouver_objet_exec($flux['args']['exec'])
		AND $en_cours['edition']!==true // page visu
		AND $en_cours['type']="document"
		AND $en_cours['id_table_objet']="id_document"
				AND $flux['args']['exec']="document_edit"
	/*	AND ($id = intval($flux['args'][$id_table_objet]))*/
		){
if ($flux['args']['id_document']>0)
$si_pdf = sql_select('extension', "spip_documents", "id_document=".$flux['args']['id_document']." and extension='pdf'");


if ($si_pdf and sql_count($si_pdf)>0){
		$texte = recuperer_fond(
				'prive/squelettes/navigation/vignettes_docs_auto',
				array(
					'id_document'=>$flux["args"]["id_document"]
				)
				
		);

		if ($p=strpos($flux['data'],"<!--affiche_milieu-->")){
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		}else{
			$flux['data'] .= $texte;}
}}
	return $flux;
}


function vignettes_docs_auto_affiche_gauche($flux) {
    $exec = $flux["args"]["exec"];
    
    if ($exec == "document_edit") {
        $id_document = $flux["args"]["id_document"];
        $contexte = array('id_document'=>$id_document);
        $ret = "<div id='pave_selection'>";
        $ret .= recuperer_fond("prive/squelettes/navigation/vignettes_docs_auto", $contexte);
        $ret .= "</div>";
        $flux["data"] .= $ret;
    }

    return $flux;
}


/*function vignettes_docs_auto_post_insertion($flux) {

	if (isset($flux['args']['action']) and $flux['args']['action'] == 'ajouter_document') {}

    if ($flux['args']['table'] == 'spip_articles') {
        include_spip('action/editer_liens');
        objet_associer(
            array('mot' => 248),
            array('article' => $flux['args']['id_objet'])
        );
    }
    return $flux;
}*/

/*
//copie de medias/fonctions filtre_vignette
function filtre_vignette_dist($extension = 'defaut', $get_chemin = false) {
	static $vignette = false;
	static $balise_img = false;

	if (!$vignette) {
		$vignette = charger_fonction('vignette', 'inc');
		$balise_img = charger_filtre('balise_img');
	}

	$fichier = $vignette($extension, false);
	// retourne simplement le chemin du fichier
	if ($get_chemin) {
		return $fichier;
	}

	// retourne une balise <img ... />
	return $balise_img($fichier);
	}
*/