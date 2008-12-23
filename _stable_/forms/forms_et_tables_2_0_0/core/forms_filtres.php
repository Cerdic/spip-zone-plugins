<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * (c) 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

	include_spip("inc/forms");

	// Hack crade a cause des limitations du compilateur
	function _Forms_afficher_reponses_sondage($id_form) {
		return Forms_afficher_reponses_sondage($id_form);
	}

	function wrap_split($wrap){
		$wrap_start="";
		$wrap_end="";
		if (preg_match(",<([^>]*)>,Ui",$wrap,$regs)){
			array_shift($regs);
			foreach($regs as $w){
				if ($w{0}=='/'){
				 //$wrap_end .= "<$w>";
				}
				else {
					if ($w{strlen($w)-1}=='/')
						$w = strlen($w)-1;
					$wrap_start .= "<$w>";
					$w = explode(" ",$w);
					if (is_array($w)) $w = $w[0];
					$wrap_end = "</$w>" . $wrap_end;
				}
			}
		}
		return array($wrap_start,$wrap_end);
	}
	
	function wrap_champ($texte,$wrap){
		if (!strlen(trim($wrap)) || !strlen(trim($texte))) return $texte;
		if (strpos($wrap,'$1')===FALSE){
			$wrap = wrap_split($wrap);
			$texte = array_shift($wrap).$texte.array_shift($wrap);
		}
		else 
			$texte = str_replace('$1',trim($texte),$wrap);
		return $texte;
	}
	
	function forms_valeur($tableserialisee,$cle,$defaut=''){
		if (!is_array($t=$tableserialisee))
			$t = unserialize($tableserialisee);
		return isset($t[$cle])?$t[$cle]:$defaut;
	}
	
	// http://doc.spip.org/@puce_statut_article
	function forms_puce_statut_donnee($id, $statut, $id_form, $ajax = false) {
		include_spip('inc/instituer_forms_donnee');
		return puce_statut_donnee($id,$statut,$id_form,$ajax);
	}
	
?>