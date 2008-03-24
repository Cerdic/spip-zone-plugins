<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;



// Renvoie la liste des configurations disponibles dans le path
function liste_cfg() {
	// Faire la liste des éléments qui ont un cfg ; ca peut etre des plugins
	// mais aussi des squelettes ou n'importe quoi
	$liste = array();
	foreach (creer_chemin() as $dir) {
		if (basename($dir) != 'cfg')
			$liste =
				array_merge($liste, preg_files($dir.'fonds/', '/cfg_.*html$'));
	}

	if ($liste) {
		$l = array();
		foreach($liste as $cfg) {
			$fonds = substr(basename($cfg,'.html'),4);
			$l[$fonds] = $cfg;
		}
		ksort($l);
		return $l;
	}
}

// Renvoie une icone avec un lien vers la page de configuration d'un repertoire
// donne
function icone_lien_cfg($dir) {
	$ret = '';
// si ce n'est pas l'adresse du plugin cfg : 
	if (basename($dir) != basename(str_replace('/inc','',dirname(__FILE__))))
	if ($l = preg_files($dir.'/fonds/', '/cfg_.*html$')) {
		foreach($l as $cfg) {
			$fonds = substr(basename($cfg,'.html'),4);
			$ret .= '<a href="'.generer_url_ecrire('cfg', 'cfg='.$fonds).'">'
				.'<img src="'._DIR_PLUGIN_CFG.'cfg-16.png"
					width="16" height="16"
					alt="'._T('icone_configuration_site').' '.$fonds.'"
					title="'._T('icone_configuration_site').' '.$fonds.'"
				/></a>';
		}
	}

	return $ret;
}


// la classe cfg represente une page de configuration
class cfg_dist
{
	var $form; // la classe cfg_formulaire
	
	function cfg_dist($nom, $cfg_id = '', $opt = array()) {
		$cfg_formulaire = cfg_charger_classe('cfg_formulaire','inc');
		$this->form = &new $cfg_formulaire($nom, $cfg_id, $opt);
	}

	function autoriser()  {return $this->form->autoriser(); }
	function formulaire() {return $this->form->formulaire();	}
	


	/*
	 * Affiche la boite d'info
	 * des liens vers les autres fonds CFG
	 * definis par la variable liens
	 * <!-- liens*=moncfg -->
	 * s'il y a une chaine de langue 'moncfg', le texte sera traduit
	 * 
	 * Ou
	 * <!-- liens*=prefixe_plugin:moncfg -->
	 * pour utiliser la chaine de langue de prefixe_plugin
	 * 
	 */	
	function lier()
	{
		$return = '';
		// liens simples
		foreach ($this->form->param->liens as $lien) {
			$nom = _T($lien);
			$lien =  array_pop(explode(':',$lien)); // ne garder que la derniere partie de la chaine de langue
			$return .= ($l = $this->boite_liens($lien, $nom)) ? "<li>$l</li>\n" : "";
		}
		// liens multiples
		foreach ($this->form->param->liens_multi as $lien) {
			$nom = _T($lien);
			$lien =  array_pop(explode(':',$lien)); // ne garder que la derniere partie de la chaine de langue
			$return .= ($l = $this->boite_liens_multi($lien, $nom)) ? "<li>$l</li>\n" : "";
		}		
		return ($return)?
			debut_boite_info(true) . "<ul>$return</ul>" . fin_boite_info(true)
			:'';
	}

	/*
	 * Affiche un lien vers le fond dont le nom ($lien)
	 * est passe en parametre
	 * a condition que le fichier fonds/cfg_$lien.html existe
	 */
	function boite_liens($lien, $nom='')
	{
		// nom est une chaine, pas une cle de tableau.
		if (empty($nom) OR !is_string($nom)) $nom = $lien;
		if (!find_in_path('fonds/cfg_'.$lien.'.html')) return "";
		
		// si c'est le lien actif, pas de <a>
		if (_request('cfg') == $lien) 
			return "$nom\n";
		else
			return "<a href='" . generer_url_ecrire("cfg","cfg=$lien") . "'>$nom</a>\n"; // &cfg_id= <-- a ajouter ?
	}
	
	
	/*
	 * Les liens multi sont appelles par 
	 * liens_multi*=nom_du_fond
	 * a condition que le fichier fonds/cfg_$lien.html existe
	 * 
	 */
	function boite_liens_multi($lien, $nom=''){
		// nom est une chaine, pas une cle de tableau.
		if (empty($nom) OR !is_string($nom)) $nom = $lien;
		if (!find_in_path('fonds/cfg_'.$lien.'.html')) return "";
		
		$dedans = '';
		if (($exi = lire_config($lien)) && is_array($exi)) {
			foreach ($exi as $compte => $info) {
				$lid = $lien . "_" . $compte;
				$dedans .= "\n<p><label for='$lid'>$compte</label>\n"
						.  "<input type='image' id='$lid' name='cfg_id' value='$compte' "
						.  "src='../dist/images/triangle.gif' style='vertical-align: text-top;'/></p>\n";
			}
		}
		// On ajoute un bouton 'nouveau'
		return    "<form method='get' action='".generer_url_ecrire('')."'><div>\n"
				. "<h4>$nom</h4>\n"
				. "<input type='hidden' name='exec' value='cfg' />\n"
				. "<input type='hidden' name='cfg' value='$lien' />\n"
				. "<label for='$lien" . "_'>" . _T('cfg:nouveau') . "</label>\n"
				. "<input type='image' id='$lien" . "_' name='nouveau' value='1' "
				. "src='../dist/images/creer.gif' style='vertical-align: text-top;'/></p>\n" 
				. $dedans
				. "\n</div></form>\n";
	
	}
	

	/*
	 * Affiche la liste des onglets de CFG
	 * 
	 * Recupere les fonds CFG et analyse ceux-ci
	 * - si onglet=oui : affiche l'onglet (valeur par defaut)
	 * - si onglet=non : n'affiche pas l'onglet
	 * - si onglet=fond_cfg_parent : n'affiche pas l'onglet, mais 'exposera' 
	 * l'element parent indique (sous entendu que
	 * le parent n'a pas 'onglet=non' sinon rien ne sera expose...
	 * 
	 */
	function barre_onglets_cfg(){
		$onglets = array();
		
		// scruter les onglets affichables ainsi que l'onglet 'expose'
		if ($l = liste_cfg()) {
			foreach($l as $fonds => $cfg) {

				if (!isset($onglets[$fonds])) 
					$onglets[$fonds] = array();
				$args = array();
				$args['afficher'] = false;
				
				// On va chercher la config cible
				// et on regarde ses donnees pour faire l'onglet
				// seulement si l'onglet doit etre affiche
				$_cfg = cfg_charger_classe('cfg','inc');
				$tmp = new $_cfg($fonds);

				if ($tmp->autoriser() && $tmp->form->param->onglet=='oui') {
					$args['afficher'] = true;
					$args['url'] = generer_url_ecrire(_request('exec'), 'cfg='.$fonds);
					
					$path = dirname(dirname($cfg));
	
					// titre
					if ($tmp->form->param->titre)
						$args['titre'] = $tmp->form->param->titre;
					else
						$args['titre'] = $fonds;

					// icone		
					$args['icone'] = '';
					if ($tmp->form->param->icone)
						$args['icone'] = $path.'/'.$tmp->form->param->icone;
					else if (file_exists($path.'/plugin.xml'))
						$args['icone'] = 'plugin-24.gif';
					else
						$args['icone'] = _DIR_PLUGIN_CFG.'cfg-doc-22.png';
					
					// onglet actif		
					$args['actif'] = ($fonds == _request('cfg'));
				}
				
				// rendre actif un parent si l'enfant est actif (onglet=nom_du_parent
				// (/!\ ne pas le desactiver s'il a deja ete mis actif)
				$o = $tmp->form->param->onglet;
				if ($tmp->autoriser() && $o && $o!='oui' && $o!='non'){
					if (!isset($onglets[$o])) 
						$onglets[$o]=array();
					
					if (!isset($onglets[$o]['enfant_actif'])) 
						$onglets[$o]['enfant_actif']=false;
						
					$onglets[$o]['enfant_actif'] = 
						($onglets[$o]['enfant_actif'] 
						|| $fonds == _request('cfg'));
				}
				
				$onglets[$fonds] = array_merge($args, $onglets[$fonds]); // conserver les donnees deja presentes ('enfant_actif')
			}
		}
		
		// retourner le code des onglets selectionnes
		$res = "";
		if ($onglets) {
			$res = debut_onglet();
			$n = -1;
			foreach ($onglets as $titre=>$args){
				if ($args['afficher']){
					// Faire des lignes s'il y en a effectivement plus de 6
					if (!(++$n%6) && ($n>0))
						$res .= fin_onglet().debut_onglet();
						
					$res .= onglet(
							$args['titre'], 
							$args['url'], 
							'cfg', 
							($args['actif'] || $args['enfant_actif']), 
							$args['icone']);
				}	
			}
			
			$res .= fin_onglet();
			
		}
		return $res;
	}
	

}

?>
