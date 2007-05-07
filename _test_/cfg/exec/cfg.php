<?php
/*
 * Plugin cfg : ecrire/?exec=cfg&cfg=xxxx
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */
// la fonction appelee par le core, une simple "factory" de la classe cfg
function exec_cfg_dist($class = null)
{
	// classe standard ?
	if (((!$class && ($class = 'cfg')) || $class == 'cfg') && !class_exists($class)) {
	    class cfg extends cfg_dist { }
	} 

	$config = new $class(
		($nom = _request('cfg'))? $nom : 'cfg',
		($vue = _request('vue'))? $vue : $nom,
		($cfg_id = _request('cfg_id'))? $cfg_id : ''
		);

	if ($message = lire_meta('cfg_message_'.$GLOBALS['auteur_session']['id_auteur'])) {
		include_spip('inc/meta');
		effacer_meta('cfg_message_'.$GLOBALS['auteur_session']['id_auteur']);
		ecrire_metas();
		$config->message = $message;
	}

	$config->traiter();
	echo $config->sortie();

	return;
}

include_spip('inc/cfg_formulaire');

// la classe cfg represente une page de configuration
class cfg_dist extends cfg_formulaire
{
	function cfg_dist($nom, $vue = '', $cfg_id = '', $opt = array())
	{
		parent::cfg_formulaire($nom, $vue, $cfg_id, $opt);
	}

	function sortie($contexte = array())
	{
		// appeler au prealable formulaire() car il recupere les <!-- machin=...->
		// machin = titre, boite, descriptif ou autre ...
		$formulaire = $this->formulaire($contexte);
		($this->titre && $this->boite)
		 ||	($this->titre && ($this->boite = $this->titre) && !($this->titre = ''))
		 || $this->boite
		 || ($this->boite = _L('Configuration') . ' ' . $this->nom);

		if (!$this->permise || !$this->autoriser()) {
			include_spip('inc/minipres');
			echo minipres(_T('info_acces_refuse'),
				$this->refus
					? $this->refus
					: " (cfg {$config->nom} - {$config->vue} - {$config->cfg_id})"
				);
			exit;
		}

		$debut = $this->debut_page();

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

	function lier()
	{
		$return = '';
		foreach ($this->liens as $lien) {
			$return .= $this->boite_liens($lien);
		}
		return $return;
	}

	function boite_liens($lien)
	{
		$dedans = $simple = '';
		if (($exi = lire_config($lien))) {
			foreach ($exi as $compte => $info) {
				// config simple ?
				if (!is_array($info)) {
					$dedans = '';
					break;
				}
				$dedans .= '
<p><label for="' . $lien . '_' . $compte . '">' . $compte . '</label>
<input type="image" id="' . $lien . '_' . $compte . '" name="cfg_id" value="' . $compte . '" src="../dist/images/triangle.gif" style="vertical-align: text-top;"/></p>';
			}
		}
		if ($dedans) {
			$dedans = '
<p><label for="' . $lien . '_">' . _T('cfg:nouveau') . '</label>
<input type="image" id="' . $lien . '_" name="nouveau" value="1" src="../dist/images/creer.gif" style="vertical-align: text-top;"/></p>' . $dedans;
		} else {
			$simple = '
<input type="image" id="' . $lien . '" name="cfg_id" value="" src="../dist/images/triangle.gif" style="vertical-align: text-top;"/>';
		}
		return debut_boite_info(true) .	'
<form method="post" action="' . $this->base_url . '">
<h4>' . _L($lien) . '
<input type="hidden" name="exec" value="cfg" />
<input type="hidden" name="cfg" value="' . $lien . '" />' . $simple . '</h4><div>' .
			$dedans . '</div></form>' . fin_boite_info(true);
	}
	function debut_page()
	{
		include_spip('inc/presentation');

		$commencer_page = charger_fonction('commencer_page', 'inc');
		
		return $commencer_page($this->boite, 'cfg', $this->nom) .
		
			debut_gauche("accueil", true) .
		
			debut_boite_info(true) .
			propre($this->descriptif) .
			fin_boite_info(true) .
		
			($this->message ? 
				debut_boite_info(true) .
				propre($this->message) .
				fin_boite_info(true)
			: '') .
		
			$this->lier() .
		
			debut_droite("", true) .
			
			($this->titre ? gros_titre($this->titre, '', false) : '');
	}

	function fin_page()
	{
		return fin_gauche() . fin_page();
	}
}
?>
