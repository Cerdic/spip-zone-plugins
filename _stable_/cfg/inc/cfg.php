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


// charger la class cfg_formulaire
include_spip('inc/cfg_formulaire');

// la classe cfg represente une page de configuration
class cfg extends cfg_formulaire
{
	function cfg($nom, $cfg_id = '', $opt = array())
	{
		parent::cfg_formulaire($nom, $cfg_id, $opt);
	}

	function sortie($contexte = array())
	{
		// appeler au prealable formulaire() car il recupere les <!-- machin=...->
		// machin = titre, boite, descriptif ou autre ...
		$formulaire = $this->formulaire($contexte);
		($this->titre && $this->boite)
		 ||	($this->titre && ($this->boite = $this->titre) && !($this->titre = ''))
		 || $this->boite
		 || ($this->boite = _T('icone_configuration_site') . ' ' . $this->nom);

		if (!$this->autoriser()) {
			include_spip('inc/minipres');
			echo minipres(_T('info_acces_refuse'),
				$this->refus
					? $this->refus
					: " (cfg {$config->nom} - {$config->vue} - {$config->cfg_id})"
				);
			exit;
		}

		include_spip("inc/presentation");

		$debut = $this->debut_page();

		// Page appellee sans formulaire valable
		if (!$formulaire) {
			$formulaire = 
			"<img src='"._DIR_PLUGIN_CFG.'cfg.png'."' style='float:right' alt='' />\n";
			$formulaire .= "<h3>" . _T("cfg:choisir_module_a_configurer") . "</h3>";;
		}
		
		else
		// Mettre un cadre_trait_couleur autour du formulaire, sauf si demande
		// express de ne pas le faire
		if ($this->presentation == 'auto') {
			$formulaire = 
				debut_cadre_trait_couleur('', true, '', $this->boite)
				.$formulaire
				.fin_cadre_trait_couleur(true);
		}

		return
			$debut
			. $formulaire
			. $this->fin_page();
	}


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
		foreach ($this->liens as $lien) {
			$nom = _T($lien);
			$lien =  array_pop(explode(':',$lien)); // ne garder que la derniere partie de la chaine de langue
			$return .= ($l = $this->boite_liens($lien, $nom)) ? "<li>$l</li>\n" : "";
		}
		// liens multiples
		foreach ($this->liens_multi as $lien) {
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
		return    "<form method='post' action='$this->base_url'><div>\n"
				. "<h4>$nom</h4>\n"
				. "<input type='hidden' name='exec' value='cfg' />\n"
				. "<input type='hidden' name='cfg' value='$lien' />\n"
				. "<label for='$lien" . "_'>" . _T('cfg:nouveau') . "</label>\n"
				. "<input type='image' id='$lien" . "_' name='nouveau' value='1' "
				. "src='../dist/images/creer.gif' style='vertical-align: text-top;'/></p>\n" 
				. $dedans
				. "\n</div></form>\n";
	
	}
	
	
	
	function debut_page()
	{
		include_spip('inc/presentation');
		$nom = _request('cfg'); // this->xxx

		pipeline('exec_init',array('args'=>array('exec'=>'cfg'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($this->boite, 'cfg', $this->nom);
		
		echo "<br /><br /><br />\n";

		echo gros_titre(sinon($this->titre, _T('cfg:configuration_modules')), '', false);

		//echo barre_onglets("configuration", "cfg");

		echo $this->barre_onglets_cfg();


		echo debut_gauche('', true);

		if ($nom)
			echo	debut_boite_info(true) .
					propre($this->descriptif) .
					fin_boite_info(true);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'cfg'),'data'=>''));
		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'cfg'),'data'=>''));

		echo
			(($this->message && $this->afficher_messages)? 
				debut_boite_info(true) .
				propre($this->message) .
				fin_boite_info(true)
			: '') .
		
			$this->lier() .
		
			debut_droite("", true);
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
				$tmp = & new cfg($fonds, '');

				if ($tmp->autoriser() && $tmp->onglet=='oui') {
					$args['afficher'] = true;
					$args['url'] = generer_url_ecrire(_request('exec'), 'cfg='.$fonds);
					
					$path = dirname(dirname($cfg));
					
					// titre
					if ($tmp->titre)
						$args['titre'] = $tmp->titre;
					else
						$args['titre'] = $fonds;
						
					// icone		
					$args['icone'] = '';
					if ($tmp->icone)
						$args['icone'] = $path.'/'.$tmp->icone;
					else if (file_exists($path.'/plugin.xml'))
						$args['icone'] = 'plugin-24.gif';
					else
						$args['icone'] = _DIR_PLUGIN_CFG.'cfg-doc-22.png';
					
					// onglet actif		
					$args['actif'] = ($fonds == _request('cfg'));
				}
				
				// rendre actif un parent si l'enfant est actif (onglet=nom_du_parent
				// (/!\ ne pas le desactiver s'il a deja ete mis actif)
				if ($tmp->autoriser() && $tmp->onglet && $tmp->onglet!='oui' && $tmp->onglet!='non'){
					if (!isset($onglets[$tmp->onglet])) 
						$onglets[$tmp->onglet]=array();
					
					if (!isset($onglets[$tmp->onglet]['enfant_actif'])) 
						$onglets[$tmp->onglet]['enfant_actif']=false;
						
					$onglets[$tmp->onglet]['enfant_actif'] = 
						($onglets[$tmp->onglet]['enfant_actif'] 
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
	
	
	function fin_page()
	{
		return fin_gauche() . fin_page();
	}
}

?>
