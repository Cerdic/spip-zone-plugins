<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function bible_insert_head($flux){
	if (lire_config("bible/police_hbo")){
		return $flux.'<link rel="stylesheet" href="'.timestamp(produire_fond_statique('bible.css')).'" type="text/css" media="all" />';
	}
	else {
		return $flux;}

}

function bible_affiche_droite($flux){

    
    /* on n'affiche le presse-papier bible que si on est sur une page d'édition*/
    if (preg_match('/edit/',$flux['args']['exec'])==false){
            return $flux;
    }

    $type = str_replace('_edit','',$flux['args']['exec']);
    if ($type="site"){//vieille exception historique: l'objet s'appelle site, mais on a id_syndic
			$id = "id_syndic";
		}
		else{
			$id   = 'id_'.$type;
		};
		$param = array();
		if (array_key_exists($id,$flux['args'])){
			$param[$id] = $flux['args'][$id];
		}
    $fond = recuperer_fond('fonds/bible_presse_papier',$param);
    $flux['data'] .= $fond;
    return $flux;
}

?>
