<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_TEXTES_LEGAUX($p) {
   return calculer_balise_dynamique($p,TEXTES_LEGAUX,array());
}

function balise_TEXTES_LEGAUX_dyn($chaine='', $who='', $separator='<br />') {
	include_spip('inc/presentation');
	include_spip('spip_proprio_fonctions');
	$conf = spip_proprio_recuperer_config();
	$conf['nom_site'] = $GLOBALS['meta']['nom_site'];
	$conf['url_site'] = $GLOBALS['meta']['adresse_site'];
	$conf['descriptif_site'] = textebrut($GLOBALS['meta']['descriptif_site']);
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$logo_site = $chercher_logo(0, 'id_syndic', 'on');
	$conf['logo_site'] = $logo_site[0];

	$entries = array('proprietaire', 'hebergeur', 'createur');
	foreach($entries as $entry){
		if( $conf[$entry.'_legal_forme'] AND strlen($conf[$entry.'_legal_forme']) ){
			$article = ($conf[$entry.'_legal_genre'] == 'fem') ? _T('spip_proprio:la') : _T('spip_proprio:le');
			$conf[$entry.'_forme']	= 
				($conf[$entry.'_legal_abbrev'] AND strlen($conf[$entry.'_legal_abbrev'])) ? 
					apostrophe($conf[$entry.'_legal_abbrev'], $article) : 
						apostrophe($conf[$entry.'_legal_forme'], $article);
		}
		else $conf[$entry.'_forme']	= '';

		$conf[$entry.'_web'] = 
			( $conf[$entry.'_site_web'] AND strlen($conf[$entry.'_site_web'])>7 ) ? 
				_T('textes_legaux:reportez_vous_au_site', array('site'=>$conf[$entry.'_site_web'])) : '';

		$conf[$entry.'_mail_texte'] = 
			( $conf[$entry.'_mail'] AND strlen($conf[$entry.'_mail']) ) ? 
				$separator._T('textes_legaux:pour_les_contacter', array('mail'=>$conf[$entry.'_mail'])) : '';

		$conf[$entry.'_fonction_responsable_texte'] = 
			( $conf[$entry.'_fonction_responsable'] AND strlen($conf[$entry.'_fonction_responsable']) ) ? 
				' ('.$conf[$entry.'_fonction_responsable'].')' : '';
	}

	$conf['type_serveur_texte'] = 
		( $conf['type_serveur'] AND strlen($conf['type_serveur']) ) ? 
			_T('textes_legaux:sur_un_serveur', array('serveur'=>$conf['type_serveur'])) : '';

	$conf['os_serveur_texte'] = 
		( $conf['os_serveur'] AND strlen($conf['os_serveur']) ) ? 
			_T('textes_legaux:os_du_serveur', array(
				'os_serveur'=> ($conf['os_serveur_web'] AND strlen($conf['os_serveur_web'])) ?
					'<a href="'.$conf['os_serveur_web'].'" class="spip_out">'.$conf['os_serveur'].'</a>' : $conf['os_serveur']
			)) : '';

	$conf['cnil_texte'] = 
		( $conf['numero_cnil'] AND strlen($conf['numero_cnil']) ) ? 
			"<br />"._T('textes_legaux:mention_cnil', $conf) : '';

	$conf['createur_administrateur_texte'] = 
		( $conf['createur_administrateur'] AND $conf['createur_administrateur'] == 'oui' ) ? 
			_T('textes_legaux:egalement_administrateur') : '';

//	$div = propre( _T('textes_legaux:'.$chaine, $conf) );
	$div = propre( _T('proprietaire:'.$chaine, $conf) );
	echo $div;
}
?>