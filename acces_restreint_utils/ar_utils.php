<?php
/**
 * Plugin acces_restreint_utils pour Spip 2.0
 * Des utilitaires pour faciliter l'utilisation du plugin Acces Restreint
 * Auteur : Cyril Marion
 * à intégrer au plugin de base dès que possible
 * - sur une rubrique : bouton pour créer une zone; ajoute par défaut les webmestres
 * TODO :
 * - sur une zone : bouton pour accéder à la rubrique protégée
 */


function ar_utils_affiche_gauche($flux) {

	$exec =  $flux['args']['exec'];
	
	// si on est sur la page ?exec=naviguer
	if ($exec=='naviguer'){
	
		// on récupère l'id_rubrique
		$id_rubrique = $flux['args']['id_rubrique'];
		$afficher = true;

		if ($afficher) {
			$contexte = array();
			foreach($_GET as $key=>$val)
				$contexte[$key] = $val;
				
			// on charge la petite boite
			$acces = recuperer_fond('prive/contenu/acces_rubrique',$contexte);
			$flux['data'] .= $acces;
		}
	}

	return $flux;
}


function proteger_rubrique($id_rubrique,$les_auteurs='',$la_zone=''){
	/**
	 * Protège une rubrique :
	 * - crée une zone du même nom
	 * - autorise des auteurs s'il y en a
	 * // - sinon autorise les webmestres
	 * - retourne le numero de la nouvelle zone
	 */
	 
	// Récupére le titre de la rubrique		
	$le_titre = sql_getfetsel("titre", "spip_rubriques","id_rubrique=" .$id_rubrique);	

	// Création d'une zone portant le même nom et récupération de son id
	$la_zone = sql_insertq("spip_zones", array(titre=>$le_titre,publique=>'oui',privee=>'oui'));
	
	// Création d'un couple zone/rubrique
	sql_insertq("spip_zones_rubriques", array(id_zone=>$la_zone,id_rubrique=>$id_rubrique));

	// Autorisation de certains auteurs, direct...
	if ($les_auteurs) {
		foreach ($les_auteurs as $un_auteur) {
			sql_insertq("spip_zones_auteurs", array(id_zone=>$la_zone,id_auteur=>$un_auteur));
		}
	} 
	// Si aucun auteur passé, autorise les webmestres
	else {
		sql_insertq("spip_zones_auteurs", array(id_zone=>$la_zone,id_auteur=>1));
		/*
		echo'<script type="text/javascript">alert("OK pour inserer 1 auteur dans fonction proteger_rubrique dans spipmine_fonctions")</script>';
		*/
	}
	
	// Renvoie le numéro de la nouvelle zone
	return $la_zone;
}

?>