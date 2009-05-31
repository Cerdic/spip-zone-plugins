<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

//include_spip('forms_filtres');
function forms_calcule_les_valeurs($type, $id_donnee, $champ, $id_form, $separateur=" ",$etoile=false,$traduit=true){
	static $raw_vals,$raw_id=0;
	$lesvaleurs = array();
	if (strncmp($champ,'joint_',6)!=0){
		if ($raw_id!=$id_donnee){
			$raw_vals = array();
			$rows = sql_allfetsel("champ,valeur","spip_forms_donnees_champs","id_donnee=".intval($id_donnee));
			foreach($rows as $row)
				$raw_vals[$row['champ']][] = $row['valeur'];
			$raw_id = $id_donnee;
		}
		if (isset($raw_vals[$champ]))
			foreach($raw_vals[$champ] as $val)
				$lesvaleurs[] = (!$traduit)?$val:forms_calcule_valeur_en_clair($type, $id_donnee, $champ, $val, $id_form, $etoile);
		return implode($separateur,$lesvaleurs);
	}
	else 
		return forms_calcule_valeur_jointure($type, $id_donnee, $champ, $id_form, $separateur,$etoile);
}
	
	function forms_calcule_valeur_jointure($type, $id_donnee, $champ, $id_form,$separateur,$etoile=false){
		static $type_joint = array();
		static $prefixi18n = array();
		static $liste_table = array();
		if (!isset($type_joint[$id_form][$champ])){
			if ($row = sql_fetsel("extra_info","spip_forms_champs","id_form=".intval($id_form)." AND champ=".sql_quote($champ)))
				$type_joint[$id_form][$champ] = $row["extra_info"];
			else return "";
		}
		$type = $type_joint[$id_form][$champ];
		if (!isset($prefixi18n[$type]))
			$prefixi18n[$type] = forms_prefixi18n($type);
		if (!isset($liste_table[$type])){
			include_spip("base/forms_base_api_v2");
			$liste_table[$type] = forms_lister_tables($type);
		}
		include_spip("base/abstract_sql");
		$pre = $prefixi18n[$type];
		$rows = sql_allfetsel("id_donnee_liee",
		  "spip_forms_donnees_donnees AS l JOIN spip_forms_donnees AS d ON d.id_donnee=l.id_donnee_liee",
		  sql_in("d.id_form",$liste_table[$type])." AND l.id_donnee=".intval($id_donnee));
		$cpt = count($rows);
		$out = "";
		if (!$etoile){
			if ($cpt==0) $out .= "";//_T("$pre:aucune_reponse");
			elseif ($cpt>5) $out .= _T("$pre:nombre_reponses",array('nombre'=>$cpt));
			//else if ($cpt==1) $out .= _T("$pre:une_reponse");
			else {
				foreach($rows as $row){
					list(,,,$resume) = forms_informer_donnee($row['id_donnee_liee']);
					$out .= implode(" ",$resume).$separateur;
				}
			}
		}
		else {
			$out .="0";
			foreach($rows as $row)
				$out .= $separateur.$row['id_donnee_liee'];
		}
		return $out;
	}
	
	function forms_format_monnaie($valeur,$decimales,$unite=true){
		return sprintf("%.{$decimales}f",$valeur).($unite?"~EUR":"");
	}
	
	function forms_calcule_valeur_en_clair($type, $id_donnee, $champ, $valeur, $id_form, $etoile=false){
		static $structure=array();
		// s'assurer que l'on est bien sur une boucle forms, sinon retourner $valeur
		$ok = $id_donnee && $champ;
		$ok = $ok && in_array($type, array('forms_donnees_champs','forms_champs','forms_donnees'));
		// on recupere la valeur du champ si pas deja la
		if ($ok && !$valeur){
			if ($row = sql_fetsel("valeur","spip_forms_donnees_champs","id_donnee=".intval($id_donnee)." AND champ=".sql_quote($champ)))
				$valeur = $row['valeur'];
			else
				$ok = false;
		}
		// on recupere le type du champ si pas deja fait (une seule requete par table et par champ)
		if ($ok && !isset($structure[$id_form])){
			include_spip('inc/forms');
			$structure[$id_form] = forms_structure($id_form, false);
		}
		$rendu = 'typo';
		if ($ok) {
			$t = $structure[$id_form][$champ]['type'];

			switch ($t) {
				case 'select':
				case 'multiple':
					if (!isset($structure[$id_form][$champ]['choix'][$valeur])){
						$rows = sql_allfetsel("choix,titre","spip_forms_champs_choix","id_form=".intval($id_form)." AND champ=".sql_quote($champ));
						foreach($rows as $row)
							$structure[$id_form][$champ]['choix'][$row['choix']] = $row['titre'];
					}
					if (isset($structure[$id_form][$champ]['choix'][$valeur]))
						$valeur = $structure[$id_form][$champ]['choix'][$valeur];
					break;
				case 'mot':
					if (!isset($mots_s[$valeur])){
						if ($row = sql_fetsel("titre","spip_mots","id_mot=".intval($valeur)))
							$mots_s[$valeur] = $row['titre'];
						else
							$mots_s[$valeur] = $valeur;
					}
					$valeur = $mots_s[$valeur];
					break;
				case 'password':
					$rendu = "";
					$valeur="******"; # ne jamais afficher en clair un password, si on veut vraiment le faire on utilise l'etoile sur le champ
					break;
				case 'url':
					$rendu = "calculer_url";
					break;
				case 'num':
				case 'monnaie':
					if (!$etoile) {
						$valeur = forms_format_monnaie($valeur,$structure[$id_form][$champ]['taille'],$t=='monnaie');
						$valeur = "<span class='numerique'>$valeur</span>";
					}
					break;
				case 'texte':
					$rendu = 'propre';
					break;
				case 'ligne':
				case 'separateur':
				case 'textestatique':
					break;
				default :
					if (!isset($GLOBALS['forms_types_champs_etendus']))
						include_spip('inc/forms_type_champs');
					if (isset($GLOBALS['forms_types_champs_etendus'][$t])
					  && isset($GLOBALS['forms_types_champs_etendus'][$t]['formate'])
					)
						foreach($GLOBALS['forms_types_champs_etendus'][$t]['formate'] as $formate)
							$valeur = preg_replace($formate['match'],$formate['replace'],$valeur);
					break;
			}

			if (!$etoile AND $rendu)
				include_spip('inc/texte');
			$valeur = pipeline('forms_calcule_valeur_en_clair',
				array('args'=>array(
					'valeur'=>$valeur,
					'rendu'=>$rendu,
					'wrap'=>$wrap_champ[$id_form][$champ],
					'type'=>$type,
					'id_donnee'=>$id_donnee,
					'champ'=>$champ,
					'id_form'=>$id_form,
					'type_champ'=>$t,
					'etoile'=>$etoile),'data'=>wrap_champ((!$etoile AND $rendu)?$rendu($valeur):$valeur,$structure[$id_form][$champ]['html_wrap']))
				);
		}
		return $valeur;
	}
	function forms_boite_jointure($id_donnee,$champ,$id_form){
		if (!_DIR_RESTREINT && in_array(_request('exec'),$GLOBALS['forms_actif_exec'])){
			$forms_lier_donnees = charger_fonction('forms_lier_donnees','inc');
			$out = $forms_lier_donnees('donnee',"$id_donnee-$champ-$id_form",_request('exec'));
		}
		return $out;
	}

		
	//
	// Afficher le diagramme de resultats d'un sondage
	//

	function forms_afficher_reponses_sondage($id_form) {
		$r = '';
		$id_form = intval($id_form);
	
		if (!$row = sql_fetsel("*","spip_forms","id_form=".intval($id_form))) return '';
		$type_form = $row['type_form'];
	
		$r .= "<div class='spip_sondage'>\n";
		
		$res2 = sql_select("*","spip_forms_champs AS champs","id_form=".intval($id_form)." AND ".sql_in("type",array('select','multiple','mot')),"","champ");
		while ($row2 = spip_fetch_array($res2)) {
			// On recompte le nombre total de reponses reelles 
			// car les champs ne sont pas forcement obligatoires
			$row3=sql_fetsel("COUNT(DISTINCT c.id_donnee) AS num",
				"spip_forms_donnees AS r LEFT JOIN spip_forms_donnees_champs AS c ON r.id_donnee=c.id_donnee",
				"r.id_form=".intval($id_form)." AND r.confirmation='valide' AND r.statut='publie' AND c.champ=".sql_quote($row2['champ']));
			if (!$row3 OR !($total_reponses=$row3['num']))
				continue;
	
			// Construire la liste des valeurs autorisees pour le champ
			$liste = array();
			if ($row2['type'] != 'mot'){
				$rows3 = sql_allfetsel("choix,titre","spip_forms_champs_choix","id_form=".intval($id_form)." AND champ=".sql_quote($row2['champ']));
				foreach($rows3 as $row3)
					$liste[$row3['choix']] = $row3['titre'];
			}
			else {
				$id_groupe = intval($row2['extra_info']);
				$rows3 = sql_allfetsel("id_mot, titre","spip_mots","id_groupe=".intval($id_groupe),"","titre");
				foreach($rows3 as $row3)
					$liste[$row3['id_mot']] = $row3['titre'];
			}
	
			// Nombre de reponses pour chaque valeur autorisee
			$result = sql_select("c.valeur, COUNT(*) AS num",
				"spip_forms_donnees AS r LEFT JOIN spip_forms_donnees_champs AS c ON c.id_donnee=r.id_donnee",
				"r.id_form=".intval($id_form)." AND r.confirmation='valide' AND r.statut='publie' AND c.champ=".sql_quote($row2['champ']),
				"c.valeur");
			$chiffres = array();
			// Stocker pour regurgiter dans l'ordre
			while ($row = spip_fetch_array($result))
				$chiffres[$row['valeur']] = $row['num'];
			
			// Afficher les resultats
			$r .= ($t=typo($row2['titre']))?"<strong>$t :</strong>":"";
			$r .= "<br />\n";
			$r .= "<div class='sondage_table'>";
			foreach ($liste as $valeur => $nom) {
				$r .= "<div class='sondage_ligne'>";
				$n = $chiffres[$valeur];
				$taux = floor($n * 100.0 / $total_reponses);
				$r .= "<div class='ligne_nom'>".typo($nom)." </div>";
				$r .= "<div style='width: 60%;'><div class='ligne_barre' style='width: $taux%;'></div></div>";
				$r .= "<div class='ligne_chiffres'>$n ($taux&nbsp;%)</div>";
				$r .= "</div>\n";
			}
			$r .= "</div>\n";
			$r .= "<br />\n";
		}
	
		$num = sql_countsel("spip_forms_donnees","id_form=".intval($id_form)." AND confirmation='valide' AND statut='publie'");
		$r .= "<strong>"._T("forms:total_votes")." : $num</strong>";
	
		$r .= "</div>\n";
		
		return $r;
	}


	// construit une balise textarea avec la barre de raccourcis std de Spip.
	// ATTENTION: cette barre injecte un script JS que le squelette doit accepter
	// donc ce filtre doit IMPERATIVEMENT assurer la securite a sa place
	
	// http://doc.spip.org/@barre_textarea
	function forms_textarea($texte, $rows, $cols, $name, $id='', $class='forml', $lang='', $active='') {
		static $num_textarea = 0;
		if ($active=='oui')
			include_spip('inc/layer'); // definit browser_barre
		if ($id=='') {$id="textarea_$num_textarea";$num_textarea++;}
	
		//$texte = entites_html($texte);
		if (($active!='oui') || (!$GLOBALS['browser_barre']))
			return "<textarea name='$name' rows='$rows' class='$class' cols='$cols' id='$id'>$texte</textarea>";
	
		include_spip ('inc/barre');
		return afficher_barre("document.getElementById('$id')", true, $lang) .
		  "
	<textarea name='$name' rows='$rows' class='$class' cols='$cols'
	id='$id'
	onselect='storeCaret(this);'
	onclick='storeCaret(this);'
	onkeyup='storeCaret(this);'
	ondblclick='storeCaret(this);'>$texte</textarea>";
	}
	
	function forms_label_details($type){
		$out = "";
		if ($type=='email') $out = _T("forms:champ_email_details");
		if ($type=='url') $out = _T("forms:champ_url_details");
		return pipeline('forms_label_details',array('args'=>array('type'=>$type),'data'=>$out));
	}
	function forms_input_champs($texte,$id_form,$type,$champ,$extra_info,$obligatoire,$env){
		return pipeline('forms_input_champs',
			array(
				'args'=>array(
					'type'=>$type,
					'id_form'=>$id_form,
					'champ'=>$champ,
					'extra_info'=>$extra_info,
					'obligatoire'=>$obligatoire,
					'env'=>$env
					),
				'data'=>$texte
			)
		);
	}
	function forms_ajoute_styles($texte){
		return pipeline('forms_ajoute_styles',$texte);
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
?>