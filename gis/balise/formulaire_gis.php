<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// includes para as funcions
include_spip('inc/texte');
include_spip('inc/lang');
include_spip('inc/mail');
include_spip('inc/date');
include_spip ("inc/meta");
include_spip ("inc/session");
include_spip ("inc/filtres");
include_spip ("inc/acces");
include_spip('base/abstract_sql');
include_spip ('inc/ajouter_documents');
spip_connect();

if(function_exists(charger_generer_url)) charger_generer_url();
/* Assurer une compat SPIP 2.1*/
else generer_url_entite();

//Le contexte indique dans quelle rubrique le visiteur peut proposer l article
function balise_FORMULAIRE_GIS ($p) {
	$p = calculer_balise_dynamique($p,'FORMULAIRE_GIS', array('id_rubrique'));
	return $p;
}

function balise_FORMULAIRE_GIS_stat($args, $filtres) {
	// Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_GIS',
					'motif' => 'RUBRIQUES')), '');
	// Verifier que les visisteurs sont autorises a proposer un article
	return ($args);
}

function balise_FORMULAIRE_GIS_dyn($id_rubrique) {
	global $_FILES, $_HTTP_POST_FILES; // ces variables sont indispensables pour rÃ©cuperer les documents joints
	global $REMOTE_ADDR, $afficher_texte, $_COOKIE, $_POST;

	$titre= stripslashes(_request('titre'));
	$chapo= stripslashes(_request('chapo'));
	$texte= stripslashes(_request('texte'));
	
	// form gis
	$lat= _request('lat');
	$lonx= _request('lonx');
	
	$commentimg= _request('commentimg');
	$commentsound= _request('commentsound');
	
	$auteur= _request('auteur');
	
	$lang = _request('var_lang');	
	$nom = 'changer_lang';
	lang_select();
	$langues = liste_options_langues($nom, $lang);
	
	// retourver le secteur et la langue de la rubrique
	$s = spip_query("SELECT id_secteur, lang FROM spip_rubriques WHERE id_rubrique = '$id_rubrique' ");
	if ($r = spip_fetch_array($s)) {
		$id_secteur = $r["id_secteur"];
		$lang = $r["lang"];
	}
	
	$valider= _request('valider');
	$media=_request('media');	
	
	$bouton= '';


	// statut de l'article, et formulaire de login en fonction de la configuration choisie
	
	if($valider)
		{
		// intégrer à la base de données
		$time=time();
		$date=date('Y-m-d H:i:s',$time);

	  	$statut= 'publie';
		
     	// ajouter le contenu de l'article
		spip_abstract_insert('spip_articles', "(surtitre, titre, soustitre, descriptif, chapo, texte, ps, statut, date, date_redac, id_rubrique, id_article)", "(
			" . spip_abstract_quote($surtitre) . ", 
    		" . spip_abstract_quote($titre) . ", 
			" . spip_abstract_quote($soustitre) . ", 
			" . spip_abstract_quote($descriptif) . ",
			" . spip_abstract_quote($chapo) . ",
			" . spip_abstract_quote($texte) . ",
			" . spip_abstract_quote($nom_inscription) . ",
			" . spip_abstract_quote($statut) . ",
			" . spip_abstract_quote($date) . ",
			" . spip_abstract_quote($date_redac) . ",
			" . intval($id_rubrique) .", 
			" . intval($id_article) ."
			)");
		
		// Insertamos nunha variable unha nova id para sustituir por id_article
		$id_novo_article = spip_insert_id();

		// insertamos as coordenadas do artigo
		spip_abstract_insert("spip_gis", "(id_article, lat, lonx)", "(" . $id_novo_article .",".$lat." ,".$lonx.")");
		
		// insertamos o autor do artigo, tomado da cookie de sesion
		spip_abstract_insert("spip_auteurs_articles", "(id_auteur, id_article)", "(" . $auteur . ", " . $id_novo_article . ")");
				
		//proba subir imaxe
		if ((isset($_FILES['commentimg'])) AND ($_FILES['commentimg']['error'] == "0")) {
    		$freshfile = $_FILES['commentimg'];
    		move_uploaded_file($freshfile['tmp_name'], _DIR_PLUGIN_GIS.$freshfile['name']) OR die ("<p>Error!</p>");
			inc_ajouter_documents_dist (_DIR_PLUGIN_GIS.$freshfile['name'], $freshfile['name'], 'article', $id_novo_article , 'document', $id_document, $documents_actifs);
			unlink (_DIR_PLUGIN_GIS.$freshfile['name']);
		} else {
			echo "no fai nada coa imaxe";
		}

		//proba subir son
		if ((isset($_FILES['commentsound'])) AND ($_FILES['commentsound']['error'] == "0")) {
    		$freshfile = $_FILES['commentsound'];
    		echo $freshfile['tmp_name'];
    		move_uploaded_file($freshfile['tmp_name'], _DIR_PLUGIN_GIS.$freshfile['name']) OR die ("<p>Error!</p>");
			inc_ajouter_documents_dist (_DIR_PLUGIN_GIS.$freshfile['name'], $freshfile['name'], 'article', $id_novo_article , 'document', $id_document, $documents_actifs);
			unlink (_DIR_PLUGIN_GIS.$freshfile['name']);
		} else {
			echo "no fai nada co son";
		}
		
		if ($r = spip_fetch_array($s)){
			$id_article = $r["id_article"];
		}
		
		return _T('form_prop_enregistre');
		echo ($id_article);
	}else{ // SI NON E if($valider), e decir, si non se lle da o boton enviar (podeselle dar o boton previsualizaar por exemplo, ou engadir imaxe)

		return array('formulaires/formulaire_gis', 0,
			array(
				'formulaire_date' => $formulaire_date,
				'url' =>  $url,
				'langues' => $langues,
				'previsu' => $previsu,
				'surtitre' => $surtitre,
				'titre' => interdire_scripts(typo($titre)),
				'soustitre' => $soustitre,
				'descriptif' => $descriptif,
				'chapo' => $chapo,
				'texte' => $texte,
				'ps' => $ps,
				'lien_titre' => $lien_titre,
				'lien_url' => $lien_url,
				'id_rubrique' => $id_rubrique,
				'id_secteur' => $id_secteur,
				'id_auteur_session' => $id_auteur_session,
				'mot' => $mot,
				'auteur' => $auteur,
				'lat' => $lat,
				'lonx' => $lonx,
				'commentimg' => $commentimg,
				'commentsound' => $commentsound
			)
		);
	} // FIN if($valider)
} // FIN function balise_FORMULAIRE_GIS_dyn($id_rubrique)
?>
