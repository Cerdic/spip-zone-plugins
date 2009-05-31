<?php

/*---------------------------------------------------------------------
    Un filtre qui prend une liste de tags en texte de balise,
    par exemple avec #ENV{tags} après un formulaire.
    Exemple d'utilisation :
    [(#ENV*{tags}|ajouter_etiquettes{#ID_ARTICLE,tags,articles,id_article,true})]
---------------------------------------------------------------------*/

function ajouter_etiquettes($texte, $id, $groupe_defaut='tags', $type, $id_type, $clear){
	
	include_spip('inc/tag-machine');
	ajouter_liste_mots($texte, $id, $groupe_defaut, $type, $id_type, $clear);
	return null;
	
}

?>
