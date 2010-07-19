<?php
// autoriser($faire, $type, $id, $qui, $opt)
//
// $faire : action
// $type : type d'objet
// $id : id objet
// $qui : auteur
// $opt : options (tableau)

if (!defined("_ECRIRE_INC_VERSION")) return;

function galuse_autoriser(){}

function autoriser_rubrique_joindregaluse_dist($faire, $type, $id, $qui, $opt){
	// Verifions qu'on a le droit ici
	if(! lire_config('galuse/autoriser_rubrique') ) return false;

	// Verifions qui a le droit
	// 1 : webmestre
	// 2 : admin complet
	// 4 : admin restreint
	// 8 : redacteur
	// 16 : visiteur enregirstré
	// 32 : le reste du monde
	if (( lire_config(galuse/statut_auteur) & 1)
		&& autoriser('webmestre', $type, $id, $qui, $opt))
			return true;		
	if ((lire_config(galuse/statut_auteur) & 2)
		&& ($qui['statut'] == '0minirezo')
		&& (!$qui['restreint']))
			return true;
	if ((lire_config(galuse/statut_auteur) & 4)	
		&& ($qui['statut'] == '0minirezo')
		&& ($qui['restreint'] AND $id AND in_array($id, $qui['restreint'])))
			return true;
    if ((lire_config(galuse/statut_auteur) & 8)
		&& ($qui['statut'] == '1comite')
        && (!$qui['restreint']))
            return true;
	if ((lire_config(galuse/statut_auteur) & 8)
		&& ($qui['statut'] == '1comite')
		&& ($qui['restreint'] AND $id AND in_array($id, $qui['restreint'])))
			return true;
	if ((lire_config(galuse/statut_auteur) & 16)
		&& ($qui['statut'] == '6forum'))
			return true;
	if ((lire_config(galuse/statut_auteur) & 32))
			return true;
}

function autoriser_auteur_joindregaluse_dist($faire, $type, $id, $qui, $opt){
	// Verifions qu'on a le droit ici
	if(! lire_config('galuse/autoriser_rubrique') ) return false;

	// Verifions qui a le droit
	if (( lire_config(galuse/statut_auteur) & 1)
		&& autoriser('webmestre', $type, $id, $qui, $opt))
			return true;		
	if ((lire_config(galuse/statut_auteur) & 2)
		&& ($qui['statut'] == '0minirezo')
		&& (!$qui['restreint']))
			return true;
	if ((lire_config(galuse/statut_auteur) & 4)	
		&& ($qui['statut'] == '0minirezo'))
			return true;
	if ((lire_config(galuse/statut_auteur) & 8)
		&& ($qui['statut'] == '1comite'))
			return true;
	if ((lire_config(galuse/statut_auteur) & 16)
		&& ($qui['statut'] == '6forum'))
			return true;
	if ((lire_config(galuse/statut_auteur) & 32))
			return true;
}

function autoriser_article_joindregaluse_dist($faire, $type, $id, $qui, $opt){
	// Verifions qu'on a le droit de joindre une image ici
	if(! lire_config('galuse/autoriser_article') ) return false;

    // récupération de l'id de la rubrique contenant l'article
    $id_rubrique=sql_getfetsel("id_rubrique","spip_article","id_article=".intval($id));

	// Verifions qui a le droit
	// 1 : webmestre
	// 2 : admin complet
	// 4 : admin restreint
	// 8 : redacteur
	// 16 : visiteur enregirstré
	// 32 : le reste du monde
	if (( lire_config(galuse/statut_auteur) & 1)
		&& autoriser('webmestre', $type, $id, $qui, $opt))
			return true;		
	if ((lire_config(galuse/statut_auteur) & 2)
		&& ($qui['statut'] == '0minirezo')
		&& (!$qui['restreint']))
			return true;
	if ((lire_config(galuse/statut_auteur) & 4)	
		&& ($qui['statut'] == '0minirezo')
		&& ($qui['restreint'] AND $id_rubrique AND in_array($id_rubrique, $qui['restreint'])))
			return true;
    if ((lire_config(galuse/statut_auteur) & 8)
		&& ($qui['statut'] == '1comite')
        && (!$qui['restreint']))
            return true;
	if ((lire_config(galuse/statut_auteur) & 8)
		&& ($qui['statut'] == '1comite')
		&& ($qui['restreint'] AND $id_rubrique AND in_array($id_rubrique, $qui['restreint'])))
			return true;
	if ((lire_config(galuse/statut_auteur) & 16)
		&& ($qui['statut'] == '6forum'))
			return true;
	if ((lire_config(galuse/statut_auteur) & 32))
			return true;
    return false;
}

function autoriser_publiergaluse_dist($faire,$type,$id,$qui,$opt){
    if( lire_config('galuse/moderation')) return true;
    
    $id_rubrique=sql_getfetsel("id_rubrique","spip_article","id_article=".intval($id));
    
    if((autoriser('webmestre', $type, $id, $qui, $opt)))
			return true;
	if (($qui['statut'] == '0minirezo')
		&& (!$qui['restreint']))
			return true;
    if (($qui['statut'] == '0minirezo')
	&& ($qui['restreint'] AND $id AND in_array($id, $qui['restreint']))
        && ($type == 'rubrique'))
			return true;
    if (($qui['statut'] == '0minirezo')
	&& ($qui['restreint'] AND $id_rubrique AND in_array($id_rubrique, $qui['restreint']))
        && ($type == 'article'))
			return true;
    if(($qui['statut'] == '1forum' )
        && (in_array($qui['id_auteur'],sql_allfetsel('id_auteur','spip_auteurs_articles','id_article=' . intval($id))))
        && ($type == 'article'))
            return true;
    return false;
}
?>