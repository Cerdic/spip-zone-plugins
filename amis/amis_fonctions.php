<?php
/*
 * Plugin amis / gestion des amis
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * critere {amis} pour la boucle AUTEURS
 * utilise la syntaxe {amis #ENV{id_auteur}}
 * recherche dans la table spip_amis les id des visiteurs lies
 * a celui passe en argument
 * utilise la fonction amis_lister() plutot qu'une jointure sql pour tenir compte
 * des amis connus par reseau social et non presents en bdd
 *
 * @param string $idb
 * @param array $boucles
 * @param array $crit
 */
function critere_AUTEURS_amis_dist($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];
	$_id_table = $boucle->id_table;
	$_qui = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	$boucle->hash = "
	\$qui = intval($_qui);
	include_spip('inc/amis');
	\$liste = amis_lister(\$qui);
	\$liste = array_map('intval',array_keys(\$liste));
	\$liste[] = 0;
	" . $boucle->hash;
	$boucle->select[]="auteurs.id_auteur as ami";
	$boucle->where[]= "'auteurs.id_auteur IN ('.implode(',',\$liste).')'";
}

/**
 * permet d'utiliser #AMI pour acceder a l'id du visiteur ami dans une boucle utilisant le critere {ami}
 * obosolete : seule la boucle AUTEURS est concernee, et dans ce cas
 * id_auteur et ami sont identiques
 *
 * @param array $p
 * @return array
 */
function balise_AMI_dist($p) {
	return rindex_pile($p, 'ami', 'amis');
}

/**
 * test si un visiteur peut en inviter un autre
 * utilisee pour afficher le picto "demander a etre ami"
 * la fonction etant appelee plusieurs fois dans un meme calcul de page
 * elle recherche en une unique requete tous les amis du visiteur concerne
 * et stocke le resultat
 * elle renvoie true lorsque les deux visiteurs concernes ne sont pas deja amis
 *
 * @param int $id_auteur
 * @param int $id_ami
 * @return bool
 */
function amis_peut_inviter($id_auteur,$id_ami){
	include_spip('base/abstract_sql');
	static $non_invitables = array();
	if (!$id_auteur = intval($id_auteur)
	OR !$id_ami = intval($id_ami)) return false; // pas d'ami pour googlebot
	
	if (!isset($non_invitables[$id_auteur])){
		$non_invitables[$id_auteur][$id_auteur] = true; // on ne peut s'inviter soi meme !
		$res = sql_select("(id_auteur+id_ami-".intval($id_auteur).") as ami",'spip_amis as amis',
		"(amis.id_auteur=".intval($id_auteur).") OR (amis.id_ami=".intval($id_auteur)." AND amis.statut='publie')"
		);
		while ($row = sql_fetch($res)){
			$non_invitables[$id_auteur][$row['ami']] = true;
		}
	}
	return (!isset($non_invitables[$id_auteur][$id_ami]));
}

?>