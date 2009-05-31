<?php
#-----------------------------------------------------#
#  Plugin  : migre_static - Licence : GPL             #
#  File    : inc/migre_class - include                #
#  Authors : Chryjs, 2007 - Beurt, 2006               #
#  Contact : chryjs�@!free�.!fr                       #
#-----------------------------------------------------#

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

if (!defined("_ECRIRE_INC_VERSION")) return;
if (defined("_INC_MIGRE_CLASS")) return; else define("_INC_MIGRE_CLASS", true);

class Rubrique {
	var $type;
	var $id_rubrique_souhaite; // decrit dans le fichier
	var $id_rubrique_reel; // une fois insere dans la base
	var $id_parent_souhaite; // idem
	var $id_parent_reel; // idem
	var $id_secteur;
	var $rub_parent; // pointeur vers la rubrique parent
	var $titre;
	var $lang;
	var $liste_articles;
	var $liste_rub_enfants;
	var $statut;
	function Rubrique($id_secteur=0) {
		$this->type='rubrique';
		$this->liste_articles=array();
		$this->id_secteur = $id_secteur;
		$this->id_rubrique_souhaite = 0;
		$this->statut="publie";
		$lang = $GLOBALS['meta']['langue_site'];
		if ($GLOBALS['meta']['multi_articles'] == 'oui') {
			lang_select($GLOBALS['auteur_session']['lang']);
			if (in_array($GLOBALS['spip_lang'],
			explode(',', $GLOBALS['meta']['langues_multilingue']))) {
				$lang = $GLOBALS['spip_lang'];
			}
		}

		$this->lang=$lang;
		$this->rub_parent=NULL;
		$this->liste_rub_enfants=array();
	}
	function maj_titre() {
		if ($this->id_rubrique_souhaite) {
			$result = spip_query("SELECT titre FROM spip_rubriques WHERE id_rubrique=".$this->id_rubrique_souhaite);
			if ($row = spip_fetch_array($result)) $this->titre=$row['titre'];
		}
	}
	function maj_infos() {
		if (!empty($this->id_rubrique_souhaite)) {
			$result = spip_query("SELECT id_rubrique,id_parent,id_secteur,titre,lang FROM spip_rubriques WHERE id_rubrique=".$this->id_rubrique_souhaite);
			if ($row = spip_fetch_array($result)) {
				$this->id_rubrique_reel=$row['id_rubrique'];
				$this->id_parent_reel=$row['id_parent'];
				if (empty($this->id_parent_souhaite)) $this->id_parent_souhaite = $row['id_parent'];
				$this->id_secteur=$row['id_secteur'];
				$this->titre=$row['titre'];
				$this->lang=$row['lang'];
				return true;
			}
		}
		return false;
	}
}

class Article {
	var $type;
	var $id_rubrique_souhaite;
	var $id_rubrique_reel;
	var $id_secteur;
	var $id_article;
	var $url;
	var $titre;
	var $texte;
	var $lang;
	var $auteur;
	var $affiche;
	var $statut;
	function Article ($url,$id_rubrique_souhaite,$id_secteur) {
		$this->type='article';
		$this->url=$url;
		$this->id_rubrique_souhaite=$id_rubrique_souhaite;
		$this->id_secteur=$id_secteur;
		$this->affiche="";
		$this->statut="prepa";
		$this->auteur=$GLOBALS['auteur_session']['id_auteur'];	// [fr] id_auteur de tous les articles r�cup�r�s dans Spip
	}
	function migre_article() {
//		function migre_infos_page($adresse="",$id_rubrique=0, &$id_article_cree) {
		global $dir_lang, $migre_meta;
		$res = "";

		// [fr] On recupere la page a traiter
//echo "url : ".$this->url." \n<br>\n";
		$page_a_traiter = recuperer_page($this->url,true);
		if (strlen($page_a_traiter) < 10) { 
			$this->affiche = "<strong>"._T('migrestatic:err_page_vide')."</strong>";
			return;
		}
		// [fr] Un coup de nettoyage HTML
		// [en] Try to clean HTML
		$page_a_traiter= migre_nettoie_html($page_a_traiter);

		// [fr] On extrait les �l�ments de la page:

		// [fr] la langue [en] the language
		// [fr] d abord dans la balise HTML [en] In the HTML tag
		@preg_match("/<html.*lang\=['\"](.*)['\"].*>/iUs",$page_a_traiter,$result);
		$lang = $result[1];

		// [fr] puis dans les META [en] Then in the META tags
		if (empty($lang)) {
			unset($result);
			@preg_match("/<meta.*content-language.*content\=['\"](.*)['\"].*>/iUs",$page_a_traiter,$result);
			$lang = $result[1];
		}

		if (!empty($lang)) {
			$this->lang = $lang; // on force la langue avec celle de l'article
		}

		// [fr] le titre
		$titre = migre_chercher_titre($page_a_traiter);
		//$titre = migre_html_entity_decode($titre); //un titre intelligible !
		$titre = migre_html_to_spip($titre); // au format de SPIP
		if ( empty($titre)
			OR ($titre=="Untitled Document")
			OR ($titre=="Document sans titre")
			OR ($titre=="Page normale sans titre")
		   ) $titre=$this->url; //pas de titre ? -> le titre sera l'URL
		$this->titre=$titre;

//echo "longa:".strlen($page_a_traiter)."<br>\n";
//print_r($page_a_traiter);
		// [fr] le body
		$body=migre_chercher_body($page_a_traiter);
//echo "longb:".strlen($body)."<br>\n";
		$body=migre_filtrer_body($body);
//echo "longv:".strlen($body)."<br>\n";
		$body=migre_nettoie_url($body,$adresse);
//echo "longd:".strlen($body)."<br>\n";
		$body=migre_html_to_spip($body);
//echo "longe:".strlen($body)."<br>\n";
		$this->texte=$body;

		// [fr] Si ce n est pas un test : integration de l'article dans SPIP
		// [en] If it s not a test, load into SPIP
		$migretest = $migre_meta['migre_test'];
		$id_mot = $migre_meta['migreidmot'];

		if (!$migretest)
		{
			$this->affiche .= "\n<div $dir_lang style='float:left;width:98%;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>"
							.$this->migre_cree_article()
						//	.migre_cree_article($titre,$body,$adresse,$id_rubrique,$auteur,$id_mot,$lang,$id_article_cree)
							."\n</div>\n";
		}
		else
		{
			$this->affiche .= "\n<div $dir_lang style='float:left;width:47%;text-align:center;'>"
							._T('migrestatic:article_affiche_par_spip')."\n</div>\n"
							. "\n<div $dir_lang style='float:left;width:47%;text-align:center;'>"
							._T('migrestatic:article_edite_par_spip')."\n</div>\n<br />\n"
							. "\n<div $dir_lang style='float:left;width:47%;height:6em;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>"
							.propre($body)."<br style='clear: both;' />\n</div>\n"
							. "\n<div $dir_lang style='float:left;width:47%;height:6em;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>"
							.nl2br($body)."<br style='clear: both;' />\n</div>\n";
		}
		//return $res;
	} // migre_article
	function migre_cree_article() {
//		function migre_cree_article($titre,$texte,$url_site,$id_rub,$auteur,$id_mot,$lang,&$id_article)
		global $migre_meta;
		// [fr] Rechercher une occurence deja presente
		$id_article="";
		$url_site = addslashes(corriger_caracteres($this->url));
		$sql = "SELECT id_article,statut FROM spip_articles WHERE url_site='".$url_site."' AND statut!='poubelle' AND statut!='refuse' LIMIT 1";
		$result=spip_query($sql);
		if ($row = spip_fetch_array($result)) $id_article=$row['id_article'];

		if ($row['statut'] == 'publie') {
			$this->id_article = $id_article;
			return "<strong>"._T('migrestatic:err_article_deja_publie').$id_article."</strong>";
		}

		$titre = addslashes(corriger_caracteres($this->titre));
		$texte = addslashes(corriger_caracteres($this->texte));

		// article
		if (!$id_article) {
			$sql = "INSERT INTO spip_articles (titre, texte, id_rubrique , nom_site, url_site, statut, date, lang) VALUES ('".$titre."','".$texte."','".$this->id_rubrique_reel."','".$titre."','".$url_site."', '".$this->statut."',NOW(),'".$this->lang."')";
			$result = spip_query($sql);
			$id_article=spip_insert_id();
			$t_mess='migrestatic:insert_article_id';
		}
		else {
			$sql = "UPDATE spip_articles SET titre='$titre', url_site='$url_site', id_rubrique='$id_rub', nom_site='$titre', texte='$texte', ps='$ps' WHERE id_article=$id_article";
			$result = spip_query($sql);
			$t_mess='migrestatic:update_article_id';
		}
		$this->id_article = $id_article;

		if (empty($id_article))
		{
			return "<strong>"._T('migrestatic:err_insert_article').$titre."</strong>";
		}
		else
		{
			spip_log('migre_static : insert article #'.$id_article);

			// auteur
			$sql = "REPLACE INTO spip_auteurs_articles (id_auteur, id_article) VALUES (" . $this->auteur . ", " . $id_article . ")";
			$result = spip_query($sql);
			$id_mot =$migre_meta['migreidmot'];

			if (!empty($id_mot) AND is_array($id_mot))
			{
				// mot-cles
				reset($id_mot);
				while (list($key,$val)=each($id_mot)) {
					if (!empty($val)) {
						$sql = "REPLACE INTO spip_mots_articles (id_mot, id_article) VALUES (" . $val . ", " . $id_article . ")";
						$result = spip_query($sql);
					}
				}
			}

			return _T($t_mess) . $id_article. _T('migrestatic:insert_article_titre') . "<a href='" . generer_url_ecrire("articles","id_article=$id_article") . "'>". $titre ."</a>" ;
		}

	} // migre_cree_article

}

?>
