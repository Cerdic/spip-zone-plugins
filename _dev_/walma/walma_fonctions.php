<?php
/*<!--version walma3.4  pour spip 1.9.2 CopID libre non marchand (c) février 2007 Alm & Walk Galerie WALMA -->*/
// se référer à http://doc.spip.org/@afficher_documents_colonne
function afficher_documents_walma($id, $type="article",$script=NULL) {
	include_spip('inc/autoriser');
	// il faut avoir les droits de modif sur l'article pour pouvoir uploader !
	if (!autoriser('joindredocument',$type,$id))
		return "";
		
	include_spip('inc/minipres'); //pour l'aide quand on appelle afficher_documents_colonne depuis un squelette
	include_spip('inc/presentation'); //pour l'aide quand on appelle afficher_documents_colonne depuis un squelette
	// seuls cas connus : article, breve ou rubrique
	if ($script==NULL){
		$script = $type.'s_edit';
		if (_DIR_RESTREINT)
			$script = parametre_url(self(),"show_docs",'');
	}

	$joindre = charger_fonction('joindre', 'inc');

	if ($GLOBALS['meta']["documents_" . $type] == 'oui') {
		$titre_cadre = "<strong>"._T('bouton_ajouter_document')."</strong>".aide("ins_doc"); 
		$ret .= $titre_cadre;
		$ret .= $joindre($script, "id_$type=$id", $id, _T('info_telecharger_ordinateur'), 'document',$type,'',0,generer_url_ecrire("documents_colonne","id=$id&type=$type",true));
	}
    
	return $ret;
}
?>