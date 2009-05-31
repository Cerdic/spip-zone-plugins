<?php

  //	  inc_tag-machine.php
  //    Librairies pour ajouter des mots clefs sur les objets spip à partir
  //    d'un simple champ texte.
  //    Distribué sans garantie sous licence GPL.
  //
  //    Authors  BoOz, Pierre ANDREWS
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


/**
 Ajoute les mots clefs dans la liste passée en paramètre au bon objet.
 Si le mot clef n'existe pas, on le crée,
 Si le groupe n'existe pas, on le crée.

 IN:
 $tags: tableau de tag ('groupe' => groupe, 'tag' => tag),
 $id: id de l'objet sur lequel ajouter les mots clefs,
 [groupe_defaut]: groupe par défaut pour les mots qui n'ont pas de groupe dans la chaîne
 [$nom_objet]: type d'objet sur lequel ajouter les mots clefs (une table: spip_mots_$nom_objet doit exister dans la base de donnée),
 [$id_objet]: colone de la table de cet objet qui contient les ids.
 OUT: rien;
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
 enleve les mots clefs passé en paramètre

 IN:
 $tags: tableau de tag ('groupe' => groupe, 'tag' => tag),
 $id: id de l'objet sur lequel ajouter les mots clefs,
 [groupe_defaut]: groupe par défaut pour les mots qui n'ont pas de groupe dans la chaîne
 [$nom_objet]: type d'objet sur lequel ajouter les mots clefs (une table: spip_mots_$nom_objet doit exister dans la base de donnée),
 [$id_objet]: colone de la table de cet objet qui contient les ids.
 OUT: rien;
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


/*des objets*/

class Tag {
  var $titre;
  var $type;
  var $id_mot;
  var $id_groupe;

  function Tag($titre, $type='', $id_groupe='') {
	$this->titre = $titre;
	$this->id_groupe = $id_groupe;
	$this->type = $type;
  }

  /*  function Tag($id_mot,$id_groupe) {
   $this->id_mot = $id_mot;
   $this->id_groupe = $id_groupe;
   }*/

  function getID() {
	return $this->id_mot;
  }

  /*public*/ function getIDGroupe() {
	return $this->id_groupe;
  }

  /*public*/ function getTitre() {
	return $this->titre;
  }

  /*public*/ function getType() {
	return $this->type;
  }

  /*public*/ function getTitreEchappe() {
	return (strpos($this->titre,' ') || strpos($this->titre,':') || strpos($this->titre,','))?'"'.$this->titre.'"':$this->titre;
  }

  /*public*/ function getTypeEchappe() {
	return (strpos($this->type,' ') || strpos($this->type,':') || strpos($this->type,','))?'"'.$this->type.'"':$this->type;
  }


  function echapper() {
	$cgroupe = $this->type;
	$ctag = $this->titre;
   
	$cgroupe = (strpos($cgroupe,' ') || strpos($cgroupe,':') || strpos($cgroupe,','))?'"'.$cgroupe.'"':$cgroupe;
	$ctag = (strpos($ctag,' ') || strpos($ctag,':') || strpos($ctag,','))?'"'.$ctag.'"':$ctag;
   
	return (($cgroupe)? ($cgroupe.':'):'').$ctag;
  }
  
  //----------------------------------------------------------------------

  /*private*/ function verifier($nom_objet) {
	include_spip('base/abstract_sql');
	list($id_groupe,$unseul,$titre) = $this->verifier_groupe();
	if($id_groupe > 0) {
	  if ($unseul == 'oui') {
		// on verifie qu'il y a pas déjà un mot associé
		$celcount = spip_abstract_select(array('count(id_mot) as tot'), 
										 array('spip_mots as mots',
											   "spip_mots_$nom_objet as objets"), 
										 array("mots.id_groupe = $id_groupe",
											   "mots.id_mot = objets.id_mot"),
										 'mots.id_groupe');
		if($numrow = spip_abstract_fetch($celcount) && $numrow['tot'] > 0) {
		  return false;
		}
		spip_abstract_free($celcount);
	  }
	} else if($this->type) {
	  spip_log("création du groupe $this->type");
	  global $table_prefix;
	  if(!lire_meta("tag-machine:colonne_.$nom_objet")) {
		spip_query("ALTER TABLE `".$table_prefix."_groupes_mots` ADD `".$nom_objet."` CHAR( 3 ) NOT NULL DEFAULT 'non';");
		ecrire_meta("tag-machine:colonne_.$nom_objet");
		ecrire_meta();
	  }

	  $id_groupe = spip_abstract_insert("spip_groupes_mots",
										"(titre, $nom_objet, minirezo)",
										"('".addslashes($this->type)."','oui','oui')");
	}	
	
	return $id_groupe;
  }
  
  /*private*/ function verifier_groupe() {
	include_spip ('base/abstract_sql');
	static $groupes_verifie;
	static $groupes_verifie_id;
	if($this->type) {
	  if(!isset($groupes_verifie[$this->type])) {
		$select_groupe = spip_abstract_select(array('id_groupe','unseul'), 
											  array('spip_groupes_mots'), 
											  array("titre = '".addslashes($this->type)."'"));
		if($groupe_row = spip_abstract_fetch($select_groupe)) {
		  $id = $groupe_row['id_groupe']; 
		  $unseul = $groupe_row['unseul'];
		  $groupes_verifie[$this->type] = array($id,$unseul,$this->type);
		}
	   
		spip_abstract_free($select_groupe);
	  }
	  return $groupes_verifie[$this->type];
	} else 	if($this->id_groupe) {
	  if(!isset($groupes_verifie_id[$this->id_groupe])) {
		$select_groupe = spip_abstract_select(array('titre','unseul'), 
											  array('spip_groupes_mots'), 
											  array("id_groupe = $this->id_groupe"));
		if($groupe_row = spip_abstract_fetch($select_groupe)) {
		  $type = $groupe_row['titre']; 
		  $unseul = $groupe_row['unseul'];
		  $groupes_verifie_id[$this->id_groupe] = array($this->id_groupe,$unseul,$type);
		}
	   
		spip_abstract_free($select_groupe);
	  }
	  return $groupes_verifie_id[$this->id_groupe];
	}
	return array(0,'');
  }

  //----------------------------------------------------------------------

  function creer($nom_objet) {
	include_spip ('base/abstract_sql');
	if(!$this->id_mot && ($this->id_groupe = $this->verifier($nom_objet)) > 0) {
	  $select = array('id_mot');
	  $from = array('spip_mots');
	  $where = array("titre = '".addslashes($this->titre)."'");
	  if($this->type) {$where[] = "type='".addslashes($this->type)."'";}
	  else if($this->id_groupe) {$where[] = "id_groupe='".$this->id_groupe."'";}
	  $result = spip_abstract_select($select,$from,$where);
	  if ($row = spip_fetch_array($result)) {
		$this->id_mot = $row['id_mot'];
	  } else {
		if($this->id_groupe) {
		  spip_log("Creer le mot $this->type:$this->titre ($this->id_mot)");
		  $this->id_mot = spip_abstract_insert("spip_mots",
											   '(id_groupe, type, titre)', 
											   "('".$this->id_groupe."','".addslashes($this->type)."','".addslashes($this->titre)."')");		   
		}
	  }
	  spip_abstract_free($result);	 
	}
	return $this->id_mot;
  }

  function ajouter($id, $nom_objet, $id_objet) {
	include_spip ('base/abstract_sql');
	if($id) {
	  if(!$this->id_mot) {
		$this->creer($nom_objet);
	  }  
	  $select = array('id_mot');
	  $from = array("spip_mots_$nom_objet");
	  $where = array("id_mot = '$this->id_mot'",
					 "$id_objet = '$id'");
	  $result = spip_abstract_select($select,$from,$where);
	  if (spip_abstract_count($result) == 0) {
		spip_abstract_insert("spip_mots_$nom_objet",
							 "(id_mot,$id_objet)",
							 '('.$this->id_mot.",$id)");
	  }
	  spip_abstract_free($result);	 
	} else
	  spip_log("id_objet non défini");
  }

  function retirer($id, $nom_objet, $id_objet) {
	include_spip ('base/abstract_sql');
	if ($this->id_mot){
	  spip_query("DELETE FROM spip_mots_$nom_objet WHERE id_mot=".$this->id_mot." AND $id_objet=$id");
	}
  }


}

class ListeTags {

  //------------------------------- Variables -------------------------------------

  /*private*/ var $tags = array();
  var $groupe_defaut;
  var $id_objet;

  //------------------------------- Constructeurs ---------------------------------

  /*public*/ function ListeTags($liste_tags,
								$groupe_defaut='',
								$id_groupe='') {
	if(!$groupe_defaut && !$id_groupe)
	  $groupe_defaut = $this->creer_groupe_defaut();

	$this->groupe_defaut = $groupe_defaut;
	$this->id_groupe = $id_groupe;
	  
	if(!is_array($liste_tags)) {
	  $this->tags = $this->parser_liste($liste_tags);
	} else {
	  $this->tags = $liste_tags;
	}
  }


  /*public*/ /*function ListeTags($id,
			  $groupe_defaut='',
			  $nom_objet='documents',
			  $id_objet='id_document') {
			  $result = spip_abstract_select(array('titre','type'),
			  array("spip_mots_$nom_objet",'spip_mots'),
			  array("spip_mots_$nom_objet.id_mot=spip_mots.id_mot",
			  "spip_mots_$nom_objet.$id_objet = $id"));
	
			  while ($row = spip_abstract_fetch($result)) {
			  $this->tags[] = new Tag($row['titre'],$row['type']);
			  }
			  spip_abstract_free($result);
			  $this->groupe_defaut = $groupe_defaut;
			  }
			 */

  //------------------------------- Info -----------------------------------------

  function getTags() {
	return $this->tags;
  }
  
  // prend une liste de tags et retourne les id_mot reconnus (sans en creer)
  function getTagsIDs() {
	//?? Aller chercher les tags dans la boite
	//?? pour faire plus generique : se baser sur id_$objet et/ou url_propre
	//?? car " dans l'url arrive ici sous la forme &quot; (#ENV{tags} et non #ENV*{tags})
	
	include_spip ('base/abstract_sql');
	$ids_mot = array();
	foreach ($this->tags as $mot) {
	  if (strlen($mot->titre)) {
		$where = array(" titre='".addslashes($mot->titre)."'");
		if(strlen($mot->type)) {
		  $where[] = 'type=\''.addslashes($mot->type).'\'';
		}
		$results = spip_abstract_select(array('id_mot'),
										array('spip_mots'),
										$where
										); //+ url_propre ? id_objet ?
		list($id) = spip_fetch_array($results,SPIP_NUM);
		if ($id) 
		  $ids_mot[] = $id;
		spip_abstract_free($results);
	  }
	}
	return $ids_mot;
  }
  
  function toStringArray() {
	$to_ret = array();
	foreach($this->tags as $tag) {
	  $to_ret[] = $tag->echapper();
	}
	return $to_ret;
  }

  //--------------------------------- Modif DB -----------------------------------
  
  function ajouter($id,
				   $nom_objet='documents',
				   $id_objet='id_document',
				   $clear=false) {
	include_spip ('base/abstract_sql');
	if($id) {
	  if ($clear) {
		$result = spip_abstract_select(array('id_mot'),
									   array('spip_mots'),
									   array("spip_mots.type = '".addslashes($this->groupe_defaut)."' OR spip_mots.id_groupe = '".addslashes($this->id_groupe)."'")
									   );
		$motsaeffacer = array('0');
		while ($row = spip_abstract_fetch($result)) {
		  $motsaeffacer[] = $row['id_mot']; 
		}
		spip_abstract_free($result);
		spip_log("Enleve les mots: ".join(',',$motsaeffacer));
		spip_query("DELETE FROM spip_mots_$nom_objet WHERE $id_objet=$id AND id_mot IN (".join(',',$motsaeffacer).")");
	  }
	  foreach($this->tags as $mot) {
	  	if (trim($mot->titre) != "")
			$mot->ajouter($id,$nom_objet,$id_objet);
	  }
	}
  }

  function retirer($id,
				   $nom_objet='documents',
				   $id_objet='id_document') {
	
	include_spip ('base/abstract_sql');
	if($id) {
	  foreach($this->tags as $mot) {
		$mot->retirer($id,$nom_objet,$id_objet);
	  }
	}
  }


  //-------------------------------- Statique ------------------------------------
  
  //-- privé
  
  /*private*/ /*static*/ function parser_liste($liste_tags) {
	
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
	$tags = split("\t", substr($liste_tags,1));
	
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
 

  function creer_groupe_defaut() {
	$s = spip_query("SELECT id_groupe FROM spip_groupes_mots
			WHERE titre='tags'");
	if ($t=spip_fetch_array($s))
	  return $this->groupe_defaut = $t['id_groupe'];
	else {
	  spip_query("INSERT spip_groupes_mots (titre) VALUES ('tags')");
	  return $this->groupe_defaut = spip_insert_id();
	}
  }
}

?>
