<?php


class auteur {
	
	public $id_auteur;
	public $nom = "Nouvel auteur";
	public $bio;
	public $email;
	public $nom_site;
	public $url_site;
	public $login;
	public $pass;
	public $low_sec;
	public $statut = "1comite";
	public $maj;
	public $pgp;
	public $htpass;
	public $en_ligne;
	public $imessage;
	public $messagerie;
	public $alea_actuel;
	public $alea_futur;
	public $prefs;
	public $cookie_oubli;
	public $source ="spip";
	public $lang ;
	public $extra;


	public function __construct($id_auteur = NULL) {
		if($id_auteur) {
			$this->id_auteur = $id_auteur;
			/* On fait un select est on met a jour les valeurs */
		}
	}
		
	/* Creer un auteur SPIP */	
	public function add() {
		$add_sql = "INSERT INTO `".$GLOBALS['table_prefix']."_auteurs` 
		(`id_auteur` ,`nom` ,`bio` ,`email` ,`nom_site` ,`url_site` ,`login` ,`pass` ,`low_sec` ,`statut` ,`maj` ,`pgp` ,`htpass` ,`en_ligne` ,`imessage` ,`messagerie` ,`alea_actuel` ,`alea_futur` ,`prefs` ,`cookie_oubli` ,`source` ,`lang` ,`extra`) 
		VALUES (NULL , "._q($this->nom).", "._q($this->bio).", "._q($this->email).", "._q($this->nom_site).", "._q($this->url_site).", "._q($this->login).", "._q($this->pass).", "._q($this->low_sec).", "._q($this->statut).", CURRENT_TIMESTAMP , "._q($this->pgp).", "._q($this->htpass).", '0000-00-00 00:00:00', "._q($this->imessage)." , "._q($this->messagerie)." , "._q($this->alea_actuel)." , "._q($this->alea_futur)." , "._q($this->prefs)." , "._q($this->cookie_oubli)." , "._q($this->source).", "._q($this->lang).", "._q($this->extra).")";
		$result = spip_query($add_sql);
		$this->id_auteur = mysql_insert_id();
		return $result;
	}
	
	public function setPass($pass) {
		include_spip('inc/acces');
		$this->pass = md5($pass);
		$this->alea_actuel = "";
		$this->alea_futur = creer_uniqid();
		$this->htpass = generer_htpass($pass);
	}
	
	public function setZone($id_zone) {
		return spip_query("INSERT INTO `".$GLOBALS['table_prefix']."_zones_auteurs` (`id_zone` ,`id_auteur`) VALUES ('".$id_zone."', '".$this->id_auteur."')");
	}

		
}


?>