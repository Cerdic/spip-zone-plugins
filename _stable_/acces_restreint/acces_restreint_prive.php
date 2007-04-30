<?php

// * Acces restreint, plugin pour SPIP * //

if (!defined("_ECRIRE_INC_VERSION")) return;


function AccesRestreint_body_prive($flux) {
	$exec = _request('exec');

	// arreter tout si on a pas acces
	if (in_array($exec,array('naviguer','rubriques_edit','articles','articles_edit','breves_voir','breves_edit','articles_versions')	)){
		include_spip('inc/acces_restreint');
		$liste_acces_interdit = AccesRestreint_liste_rubriques_exclues();
		global $id_rubrique;
		if ($exec == 'articles' || $exec=='articles_edit' || $exec=='articles_versions'){
			global $id_article;
 			$row = spip_fetch_array(spip_query("SELECT statut, titre, id_rubrique FROM spip_articles WHERE id_article=$id_article"));
 			if ($row) {
				$id_rubrique = $row['id_rubrique'];
			}
		}
		if ($exec == 'breves_voir' || $exec=='breves_edit' ){
			global $id_breve;
			$result = spip_query("SELECT * FROM spip_breves WHERE id_breve='$id_breve'");
			if ($row = spip_fetch_array($result)) {
				$id_rubrique=$row['id_rubrique'];
			}
		}
		
		if (!in_array($id_rubrique,$liste_acces_interdit))
			return $flux; // c'est bon on contine
			
		// arreter tout si on a pas acces

		redirige_par_entete(generer_url_ecrire(''));
		exit();
	}
	return $flux; // c'est bon on contine
}
?>