<?php

/**
 *
 * Un plugin d'objet quelconque ayant besoin de mots cles (sous SPIP 2.1 ou 2.2, pas apres !)
 * Peut utiliser le pipeline "declarer_liaisons_mots" pour renseigner
 * son objet. Ainsi, le plugin gèrera les ajouts de formulaires et la prise en compte
 * de l'objet.
 *
 *
**/
if (!defined("_ECRIRE_INC_VERSION")) return;


class declaration_liaison_mots {

	var $nom = ''; // auteurs
	var $objet = ''; // auteur
	var $_id_objet = ''; // id_auteur
	var $exec_formulaire_liaison = ''; // auteur_infos

	var $singulier = ''; // 'auteur'
	var $pluriel = ''; // 'auteurs'
	var $libelle_objet = ''; // 'mots_objets:objet_auteurs' 	= 'Auteurs'
	var $libelle_liaisons_objets = ''; // 'mots_objets:item_mots_cles_association_auteurs'	= 'aux auteurs'
	var $titre_mot_objets = ''; // 'mots_objets:titre_mot_objets'	= 'Les auteurs lies a ce mot-clé'

	var $squelette_liste = ''; // 'auteurs' (prive/liste/auteurs) (avec plugin afficher_objets)
	
	/**
	 * Constructeur 
	 *
	 * @param $nom : le nom de la table sans son prefixe (auteurs)
	 * @param $definitions : tableaux de couples cle/valeurs d'information sur l'objet.
	 * @return 
	**/
	function declaration_liaison_mots($nom, $definitions = array()) {
		
		$this->nom = $nom;
		$this->objet = objet_type($nom);
		$this->_id_objet = id_table_objet($this->objet);
		$this->table_sql = table_objet_sql($nom);
		
		foreach ($definitions as $cle => $val) {
			if (isset($this->$cle)) $this->$cle = $val;
		}
	}
}



/**
 * Retourne la liste des liaisons de mots connues... 
 *
 * @param 
 * @return 
**/
function gouverneur_de_mots() {
	static $gouverneur_de_mots = false;
	if ($gouverneur_de_mots === false) {
		$gouverneur_de_mots = pipeline('declarer_liaison_mots', array(
		
			'auteurs' => new declaration_liaison_mots('auteurs', array(
				'exec_formulaire_liaison' => "auteur_infos",

				'singulier' => "mots_objets:info_un_auteur",
				'pluriel'   => "mots_objets:info_nombre_auteurs",
				'libelle_objet' => "mots_objets:objet_auteurs",
				'libelle_liaisons_objets' => "mots_objets:item_mots_cles_association_auteurs",
				'titre_mot_objets' => "mots_objets:titre_mot_auteurs",
			)),
			
			'documents' => new declaration_liaison_mots('documents', array(
				'exec_formulaire_liaison' => "documents_edit",

				'singulier' => "medias:un_document", //"mediatheque:un_document",
				'pluriel'   => "medias:des_documents", //"mediatheque:des_documents",
				'libelle_objet' => "medias:objet_documents",
				'libelle_liaisons_objets' => "mots_objets:item_mots_cles_association_documents",
				'titre_mot_objets' => "mots_objets:titre_mot_documents",
			))
		));

		ksort($gouverneur_de_mots);
	}
	
	return $gouverneur_de_mots;
}

// retourner le tableau pour le pipeline affiche milieu
// $ou['auteur_infos'] = gouverneur_de_mots (classe)
function mots_objets_get_affiche_milieu() {
	$ou = array();
	foreach(gouverneur_de_mots() as $gouv) {
		if ($gouv->exec_formulaire_liaison) {
			$ou[$gouv->exec_formulaire_liaison] = $gouv;
		}
	}
	return $ou;
}

?>
