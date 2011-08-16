<?php

//	  inc_tag-machine.php
//
//    Librairies pour ajouter des mots clefs sur les objets spip à partir
//    d'un simple champ texte.
//    Distribué sans garantie sous licence GPL.
//
//    Authors  BoOz, Pierre ANDREWS, RastaPopoulos (réécriture nouvelle API)
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


if (!defined("_ECRIRE_INC_VERSION")) exit;

function entableau($tag) {
	return array('groupe' => $tag->type, 'tag' => $tag->titre);
}


/*
	Ajoute les mots clefs dans la liste passée en paramètre au bon objet.
	Si le mot clef n'existe pas, on le crée
	Si le groupe n'existe pas, on le crée
	
	Paramètres :
	$tags: tableau de tag ('groupe' => groupe, 'tag' => tag)
	$id: id de l'objet sur lequel ajouter les mots clefs
	[$groupe_defaut]: groupe par défaut pour les mots qui n'ont pas de groupe dans la chaîne
	[$nom_objet]: type d'objet sur lequel ajouter les mots clefs (une table: spip_mots_$nom_objet doit exister)
	[$id_objet]: colonne de la table de cet objet qui contient les ids
	
	Retourne:
	rien
*/
function ajouter_liste_mots($tags,
							$id,
							$groupe_defaut='',
							$nom_objet='documents',
							$id_objet='id_document',
							$clear = false) {
	$tags = new ListeTags($tags,$groupe_defaut);
	$tags->ajouter($id, $nom_objet, $id_objet,$clear);
}
function ajouter_mots($liste_tags,
					  $id,
					  $groupe_defaut='',
					  $nom_objet='documents',
					  $id_objet='id_document',
					  $clear = false) {
	$tags = new ListeTags($liste_tags,$groupe_defaut);
	$tags->ajouter($id, $nom_objet, $id_objet,$clear);
}


/**
	Enleve les mots clefs passé en paramètre
	
	Paramètres :
	$tags: tableau de tag ('groupe' => groupe, 'tag' => tag)
	$id: id de l'objet sur lequel supprimer les mots clefs
	[groupe_defaut]: groupe par défaut pour les mots qui n'ont pas de groupe dans la chaîne
	[$nom_objet]: type d'objet sur lequel supprimer les mots clefs (une table: spip_mots_$nom_objet doit exister)
	[$id_objet]: colonne de la table de cet objet qui contient les ids
	
	Retourne:
	rien
*/
function retirer_liste_mots($tags,
							$id,
							$groupe_defaut='',
							$nom_objet='documents',
							$id_objet='id_document') {
	$tags = new ListeTags($tags,$groupe_defaut);
	$tags->retirer($id, $nom_objet, $id_objet);
}
function retirer_mots($liste_tags,
					  $id,
					  $groupe_defaut='',
					  $nom_objet='documents',
					  $id_objet='id_document') {
	$tags = new ListeTags($liste_tags,$groupe_defaut);
	$tags->retirer($id, $nom_objet, $id_objet);
}


function parser_liste($liste_tags) {
	$tags = new ListeTags($liste_tags,'');
	return array_map('entableau',$tags->getTags());
}


// Objet Tag
class Tag {
	
	// Propriétés
	var $titre;
	var $type;
	var $id_mot;
	var $id_groupe;
	
	// Constructeur
	function Tag($titre, $type='', $id_groupe='') {
		$this->titre = $titre;
		$this->id_groupe = $id_groupe;
		$this->type = $type;
	}
	
	// Accesseurs
	function getID() {return $this->id_mot;} // public
	function getIDGroupe() {return $this->id_groupe;} // public
	function getTitre() {return $this->titre;} // public
	function getType() {return $this->type;} // public
	function getTitreEchappe() { // public
		return (strpos($this->titre,' ') || strpos($this->titre,':') || strpos($this->titre,',')) ? '"'.$this->titre.'"' : $this->titre;
	}
	function getTypeEchappe() { // public
		return (strpos($this->type,' ') || strpos($this->type,':') || strpos($this->type,',')) ? '"'.$this->type.'"' : $this->type;
	}
	
	// Renvoie une chaîne 'groupe:titre'
	function echapper() { // public
		$cgroupe = $this->type;
		$ctag = $this->titre;
		
		$cgroupe = (strpos($cgroupe,' ') || strpos($cgroupe,':') || strpos($cgroupe,','))?'"'.$cgroupe.'"':$cgroupe;
		$ctag = (strpos($ctag,' ') || strpos($ctag,':') || strpos($ctag,','))?'"'.$ctag.'"':$ctag;
		
		return (($cgroupe)? ($cgroupe.':'):'').$ctag;
	}
	
	
	/* Fonctions de vérification */
	/* ------------------------- */
	
	
	// Vérifie le groupe
	// Renvoie un tableau avec les infos : (id_groupe,unseul,titre_du_groupe)
	function verifier_groupe() { // private
		
		include_spip ('base/abstract_sql');
		
		// On va garder en mémoire les groupes vérifiés
		static $groupes_verifie;
		static $groupes_verifie_id;
		
		if($this->type) {
			
			if(!isset($groupes_verifie[$this->type])) {
				$select_groupe = sql_select(
					array('id_groupe','unseul'),
					'spip_groupes_mots',
					array(array('=', 'titre', _q($this->type)))
				);
				
				if($groupe_ligne = sql_fetch($select_groupe)) {
					$id = $groupe_ligne['id_groupe']; 
					$unseul = $groupe_ligne['unseul'];
					$groupes_verifie[$this->type] = array($id,$unseul,$this->type);
				}
				if ($select_groupe) sql_free($select_groupe);
			}
			return $groupes_verifie[$this->type];
			
		}
		else if($this->id_groupe) {
			
			if(!isset($groupes_verifie_id[$this->id_groupe])) {
				
				$select_groupe = sql_select(
					array('titre','unseul'), 
					'spip_groupes_mots', 
					array(array('=', 'id_groupe', $this->id_groupe))
				);
				
				if($groupe_ligne = sql_fetch($select_groupe)) {
					$type = $groupe_ligne['titre']; 
					$unseul = $groupe_ligne['unseul'];
					$groupes_verifie_id[$this->id_groupe] = array($this->id_groupe,$unseul,$type);
				}
				if ($select_groupe) sql_free($select_groupe);
			}
			return $groupes_verifie_id[$this->id_groupe];
			
		}
		
		// si pas de groupe
		return array(0,'');
		
	}
	
	// Vérifie si on peut bien ajouter un mot de ce groupe
	// Crée le groupe du mot s'il n'existe pas
	// Retourne l'id_groupe si c'est ok, false sinon
	function verifier($nom_objet) { // private
	
		include_spip('base/abstract_sql');
		
		list($id_groupe,$unseul,$titre) = $this->verifier_groupe();
		
		if($id_groupe > 0) {
			
			// si le groupe n'accepte qu'un mot actif
			if ($unseul == 'oui') {
				// on verifie qu'il y a pas déjà un mot associé
				$celcount = sql_select(
					'count(id_mot) as tot', 
					array(
						'spip_mots as mots',
						"spip_mots_$nom_objet as objets"
					), 
					array(
						"mots.id_groupe = $id_groupe",
						"mots.id_mot = objets.id_mot"
					),
					'mots.id_groupe'
				);
				// mot déjà utilisé, on arrête
				if($numrow = sql_fetch($celcount) && $numrow['tot'] > 0)
					return false;
				if ($celcount) sql_free($celcount);
			}
			
		}
		else if($this->type) {
			
			spip_log("création du groupe ".$this->type);
			
			// on rajoute une option pour le type d'objet dans spip_groupes_mots
			if(!lire_meta("tag-machine:colonne_.$nom_objet")) {
				sql_alter("TABLE spip_groupes_mots ADD $nom_objet CHAR( 3 ) NOT NULL DEFAULT 'non';");
				ecrire_meta("tag-machine:colonne_.$nom_objet",1);
			}
			
			$id_groupe = sql_insertq(
				"spip_groupes_mots",
				array(
					'titre' => $this->type,
					$nom_objet => 'oui',
					'minirezo' => 'oui'
				)
			);
			
		}
		return $id_groupe;
	}
	
	
	/* Fonctions de modification */
	/* ------------------------- */
	
	
	// Teste si le mot existe sinon crée le mot
	// Renvoie l'id_mot
	function creer($nom_objet) { // private
		
		include_spip ('base/abstract_sql');
		
		if(!$this->id_mot && ($this->id_groupe = $this->verifier($nom_objet)) > 0) {
			
			$where = array(array('=', 'titre', _q($this->titre)));
			
			if($this->type)
				$where[] = array('=', 'type', _q($this->type));
			else if($this->id_groupe)
				$where[] = array('=', 'id_groupe', $this->id_groupe);
			
			$result = sql_select('id_mot', 'spip_mots', $where);
			
			if ($row = sql_fetch($result))
				$this->id_mot = $row['id_mot'];
			else if($this->id_groupe) {
				spip_log("Creer le mot $this->type:$this->titre ($this->id_mot)");
				
				$this->id_mot = sql_insertq(
					'spip_mots',
					array(
						'id_groupe' => $this->id_groupe,
						'type' => $this->type,
						'titre' => $this->titre
					)
				);
			}
			if ($result) sql_free($result);	
			 
		}
		return $this->id_mot;
		
	}
	
	// Ajoute le mot à un objet quelconque
	// Sauf s'il est déjà associé
	function ajouter($id, $nom_objet, $id_objet) { // public
		
		include_spip ('base/abstract_sql');
		
		if($id) {
			// on vérifie que le mot est bien créé
			if(!$this->id_mot) {
				$this->creer($nom_objet);
			}
			
			$where = array(
				array('=', 'id_mot', $this->id_mot),
				array('=', $id_objet, $id)
			);
			
			$result = sql_select('id_mot', "spip_mots_$nom_objet", $where);
			
			// on crée une liaison seulement si c'est pas déjà le cas
			if (sql_count($result) == 0) {
				sql_insertq(
					"spip_mots_$nom_objet",
					array(
						'id_mot' => $this->id_mot,
						$id_objet => $id
					)
				);
			}
			if ($result) sql_free($result);	 
		}
		else spip_log("id_objet non défini");
		
	}
	
	// Retire le mot d'un objet quelconque
	function retirer($id, $nom_objet, $id_objet) { // public
		
		include_spip ('base/abstract_sql');
		
		if ($this->id_mot){
			sql_delete(
				"spip_mots_$nom_objet",
				"id_mot = ".intval($this->id_mot)." and ".$id_objet." = ".intval($id)
			);
		}
		
	}
	
}


// Objet composé d'une liste d'objet Tag
class ListeTags {
	
	// Propriétés
	var $tags = array(); // private, la liste des mots
	var $groupe_defaut; // le groupe par défaut si les mots n'ont pas la forme groupe:titre
	var $id_groupe;
	var $id_objet; // clé primaire de l'objet auquel on veut lier la liste
	
	// Constructeur
	function ListeTags($liste_tags,	$groupe_defaut='', $id_groupe='') { // public
		
		if(!$groupe_defaut && !$id_groupe)
			$groupe_defaut = $this->creer_groupe_defaut();
		
		$this->groupe_defaut = $groupe_defaut;
		$this->id_groupe = $id_groupe;
		
		// si la liste est une chaîne, il faut extraire
		if(!is_array($liste_tags))
			$this->tags = $this->parser_liste($liste_tags);
		else
			$this->tags = $liste_tags;
		
	}
	
	// Accesseurs
	function getTags() {return $this->tags;} // public
	
	// Retourne les id_mot des mots reconnus sans les créer
	// (ne retourne que ce qui existe déjà dans la base)
	function getTagsIDs() {
		
		//?? Aller chercher les tags dans la boite
		//?? pour faire plus generique : se baser sur id_$objet et/ou url_propre
		//?? car " dans l'url arrive ici sous la forme &quot; (#ENV{tags} et non #ENV*{tags})
		
		include_spip ('base/abstract_sql');
		$ids_mot = array();
		
		foreach ($this->tags as $mot) {
			if (strlen($mot->titre)) {
				$where = array(array('=', 'titre', _q($mot->titre)));
				
				if(strlen($mot->type))
					$where[] = array('=', 'type', _q($mot->type));
				
				$results = sql_select('id_mot',	'spip_mots', $where); //+ url_propre ? id_objet ?
				
				list($id) = sql_fetch($results,SPIP_NUM);
				if ($id) $ids_mot[] = $id;
				if ($results) sql_free($results);
			}
		}
		return $ids_mot;
		
	}
	
	// Retourne un tableau de chaînes groupe:titre pour chaque mot
	function toStringArray() {
		
		$retour = array();
		foreach($this->tags as $tag) {
			$retour[] = $tag->echapper();
		}
		return $retour;
		
	}
	
	
	/* Fonctions de modification */
	/* ------------------------- */
	
	
	// Ajouter tous les mots de la liste à un objet quelconque, et supprimer les anciens si ya l'option clear
	function ajouter($id, $nom_objet='documents', $id_objet='id_document', $clear=false) { // public
		
		include_spip ('base/abstract_sql');
		
		if($id) {
			
			// si il y a l'option clear, on efface les anciennes liaisons avant
			if ($clear) {
				$result = sql_select(
					'id_mot',
					'spip_mots as mots',
					"mots.type = "._q($this->groupe_defaut)." OR mots.id_groupe = "._q($this->id_groupe)
				);
				
				$mots_a_effacer = array('0');
				
				while ($row = sql_fetch($result)) {
					$mots_a_effacer[] = $row['id_mot']; 
				}
				if ($result) sql_free($result);
				
				spip_log("Enleve les mots: (".join(',',$mots_a_effacer).") à (".$id_objet.", ".intval($id).")");
				sql_delete(
					"spip_mots_$nom_objet",
					$id_objet." = ".intval($id)." and id_mot in (".join(',', $mots_a_effacer).")"
				);
			}
			
			// ensuite on ajoute chaque mot
			foreach($this->tags as $mot) {
				if (trim($mot->titre) != "")
				$mot->ajouter($id,$nom_objet,$id_objet);
			}
			
		}
		
	}
	
	// Retirer les mots de la liste d'un objet quelconque
	function retirer($id, $nom_objet='documents', $id_objet='id_document') {
		
		include_spip ('base/abstract_sql');
		
		if($id) {
			foreach($this->tags as $mot) {
				$mot->retirer($id,$nom_objet,$id_objet);
			}
		}
		
	}
	
	
	/* Fonctions statiques */
	/* ------------------- */
	
	// Prend un chaîne et fabrique un tableau d'objets Tag
	function parser_liste($liste_tags) { // private static
		
		$liste_tags = trim($liste_tags);
		
		// supprimer les tab (notre separateur final)
		$liste_tags = strtr($liste_tags, "\t", " ");
		
		// doubler les caracteres qui peuvent faire office de separateur
		$liste_tags = ' '.preg_replace('/[[:space:],]/', '\\0\\0', $liste_tags).' ';
		
		// trouver les tags et les separer par \t
		$liste_tags = preg_replace('/[[:space:],]+("?)(.*?)\\1[[:space:],]/', "\t\\2", $liste_tags);
		
		// remettre les caracteres doubles en simple
		$liste_tags = preg_replace('/([[:space:],])\\1/', '\\1', $liste_tags);
		
		// exploser selon les tab
		$tags = explode("\t", substr($liste_tags,1));
		
		// recuperer les groupes sous la forme  <groupe:mot>
		foreach ($tags as $i => $tag) {
			$tag = str_replace('"', '', $tag);
			preg_match('/((.*):)?(.*)/', $tag, $regs);
			$groupe = $regs[2];
			if(!$groupe)
				$groupe = $this->groupe_defaut;
			$tags[$i] = new Tag($regs[3], $groupe, $this->id_groupe);
		}
		
		return $tags;
		
	}
	
	function containsSeparateur($char) {
		return (strpos($char,' ') || strpos($char,':') || strpos($char,','));
	}
	
	// Crée le groupe de mots "tags"
	function creer_groupe_defaut() {
		
		include_spip('base/abstract_sql');
		
		$s = sql_select(
			array('id_groupe', 'titre'),
			'spip_groupes_mots',
			array(array('=', 'titre', 'tags'))
		);
		
		if ($t=sql_fetch($s))
			return $this->id_groupe = $t['id_groupe'];
		else {
			return $this->id_groupe =  sql_insertq(
				'spip_groupes_mots',
				array(
					'titre' => 'tags'
				)
			);
		}
		
	}
	
}

?>
