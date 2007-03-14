<?php
 /**************************************************************************\
*  SPIP, Systeme de publication pour l'internet                              *
*                                                                            *
*  Copyright (c) 2001-2007                                                   *
*  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James     *
*                                                                            *
*  Ce script fait partie d'un logiciel libre distribue sous licence GNU/GPL. *
*  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.      *
 \**************************************************************************/

/*	Batir une liste de navigation
	
	$navig = new NavigationListe(array(
		'requete_liste' => requete_auteurs($tri, $statut),
		'callback_liste' => 'complement_auteur',
		'requete_comptage' => 'SELECT COUNT(*) FROM spip_auteurs',
		'requete_etapes' =>
			'SELECT DISTINCT UPPER(LEFT(nom,1)) l, COUNT(*) FROM spip_auteurs
			 GROUP BY l ORDER BY l',
		'max_par_page' => 30,
		'debut' => intval(_request('debut')),
		'fragment' => intval(_request('fragment')),
		'contenu_ligne' => 'ligne_auteur'
	));
*/

class NavigationListe
{
	// requete de base de la liste
	var $requete_liste = '';
	// un callback pour completer la liste
	var $callback_liste = '';
	// requete pour decompte total
	var $requete_comptage = '';
	// requete pour etapes comme les initiales
	var $requete_etapes = '';
	// la taille de la fenetre vue
	var $max_par_page = 30;
	// le positionnement de cette fenetre
	var $debut = 0;

	// decompte total
	var $compte = null;
	// etapes
	var $etapes = array();
	// fenetre de lignes courantes
	var $page = array();

	// l'erreur eventuelle
	var $erreur = '';
	// la dernier requete
	var $query = '';
	// son resultat
	var $result = null;
	// le dernier fetch
	var $fetch = array();
//	var $fragment' => intval(_request('fragment')),
//	var $contenu_ligne' => 'ligne_auteur'

	function NavigationListe($tabopt)
	{
		// pour l'instant interface libre
		foreach ($tabopt as $opt => $val) {
			$this->$opt = $val;
		}

		// compter la population totale ou mourir
		if ($this->requete_comptage) {
			if ($this->errQuery($this->requete_comptage, 'Compte global impossible', SPIP_NUM)) {
				return;
			}
			$this->compte = intval($this->fetch[0]);
		}

		// chercher les etapes ou mourir
		if ($this->errQuery($this->requete_etapes, 'Etapes impossibles')) {
			return;
		}
		// charger le tableau des etapes
		$cumul = 0;
		do {
			$eta = $this->fetch['etape'];
			$this->etapes[$eta]['compte'] = intval($this->fetch['compte']);
			$this->etapes[$eta]['debut'] = $cumul;
			$cumul += $this->etapes[$eta]['compte'];
		} while ($this->fetch = spip_fetch_array($this->result));

		// total des etapes == population totale ?
		if (is_null($this->compte)) {
			$this->compte = $cumul;
		} elseif ($cumul != $this->compte) {
			$this->setErreur('Comptes ???');
			return;
		}

		// demarrer la page ou mourir
		if ($this->errQuery($this->requete_liste, 'Liste impossible')) {
			return;
		}
		// charger la page
		do {
			$this->page[] = $this->fetch;
		} while ($this->fetch = spip_fetch_array($this->result));
		return;
	}

	function setErreur($msg = '')
	{
		$this->erreur = $msg ? $msg : $this->query;
	}

	function errQuery($query, $msg = '', $mode = SPIP_ASSOC)
	{
		((($this->result = spip_query($this->query = $query)) &&
		  ($this->fetch = spip_fetch_array($this->result, $mode))) ||
		 ($this->setErreur($msg)));
		return $this->erreur;
	}
}
