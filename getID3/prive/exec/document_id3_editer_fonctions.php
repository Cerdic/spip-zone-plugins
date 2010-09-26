<?php 
include_spip('inc/presentation');

if(!function_exists('lien_objet')){
	function lien_objet($id,$type,$longueur=80,$connect=NULL){
		include_spip('inc/liens');
		$titre = traiter_raccourci_titre($id, $type, $connect);
		$titre = typo($titre['titre']);
		if (!strlen($titre))
			$titre = _T('info_sans_titre');
		$url = generer_url_entite($id,$type);
		return "<a href='$url' class='$type'>".couper($titre,$longueur)."</a>";
	}
}
?>