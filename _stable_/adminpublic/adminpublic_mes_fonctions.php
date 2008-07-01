<?php

#Vous pouvez affiner ...
####################################################################
#ajout core SPIP
#pour modifier en ligne un evenement
#fichier ecrire/balise/formulaire_admin.php 
#ajouter ligne 73 dans le tableau de $env
#marche pas sur l'enregistrement..

#$env['id_evenement'] = (_request('id_evenement'));

####################################################################

#ajout sur dist/formulaires/editer_article
#permet de donner un statut ˆ un nouvel article
#donc pour ne pas casser dans l'espace privŽ
#ligne 12 aprs  <input type='hidden' name='id_article' value='#ENV{id_article}' />
/*
	[(#REM) on definit les hidden en une ligne: statut de l'article, redirect etc... si espace public ]
	 [(#SELF|match{exec}|?{'',' '}) <!-- suppose pas dans espace prive -->
	 <input type="hidden" name="statut" value="[(#ENV**{statut}|sinon{prop})]" >
	 <input type="hidden" name="redirect" value="[(#SELF|parametre_url{id_article,[(#ENV{id_article})]})]" />
	 ]
*/
	 
#####################################################################
	
	

	function cree_puce_stat($statut){	
	switch ($statut) {
		case 'prepa': return  http_img_pack("puce-blanche.gif",_T('texte_statut_en_cours_redaction'));
		case 'prop': return  http_img_pack("puce-orange.gif",_T('texte_statut_propose_evaluation'));
		case 'publie': return  http_img_pack("puce-verte.gif",_T('texte_statut_publie'));
		case 'refuse': return  http_img_pack("puce-rouge.gif",_T('texte_statut_refuse'));
		case 'poubelle': return  http_img_pack("puce-poubelle.gif",_T('texte_statut_poubelle'));
		};
	}
	
	
/*
* #IMG{fichier, alt} -> <img src='fichier' alt='alt' />
*[(#IMG{images/blocsallesom.png}|image_reduire{280}|extraire_attribut{src})]
*/
function balise_IMG($p) {

	if ($p->param && !$p->param[0][0]) {
		$p->code =  calculer_liste($p->param[0][1],
					$p->descr,
					$p->boucles,
					$p->id_boucle);
		$alt =  calculer_liste($p->param[0][2],
					$p->descr,
					$p->boucles,
					$p->id_boucle);

		// autres filtres (???)
		array_shift($p->param);
	}

	// recherche du chemin de l'image (comme #CHEMIN)
	$p->code = 'find_in_path(' . $p->code .')';
	// passage en image
	$p->code = '"<img src=\'".' . $p->code .'."\' alt=\'".'.$alt.'."\' />"';

	$p->interdire_scripts = true;
	return $p;
}


// generer_url_public('style_prive', parametres_css_prive())
// qu'il est alors possible de recuperer dans le squelette style_prive.html avec
// #SET{claire,##ENV{couleur_claire,edf3fe}}
// #SET{foncee,##ENV{couleur_foncee,3874b0}}
// #SET{left,#ENV{ltr}|choixsiegal{left,left,right}}
// #SET{right,#ENV{ltr}|choixsiegal{left,right,left}}
// http://doc.spip.org/@parametres_css_prive
function adminpublic_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_ADMINPUBLIC.'adminpublic.css" type="text/css" media="projection, screen, tv" />';
	$flux .= '<script type="text/javascript" src="'.generer_url_public('adminpublic.js').'"></script>';
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_ADMINPUBLIC.'js/jquery-fieldselection.js"></script>';
	return $flux;
}
	

?>
