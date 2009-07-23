<?php

class Jour
{
	protected $_annee = 0 ;
	protected $_mois = 0 ;
	protected $_jour = 0 ;
	protected $_ts = 0 ;
	
	public static $joursFr = array(1 => 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche') ;
	
	public function __construct($jour, $mois, $annee)
	{
		$this -> _jour = (int) $jour ;
		$this -> _mois = (int) $mois ;
		$this -> _annee = (int) $annee ;
		$this -> _ts = (int) mktime(0, 0, 0, $this -> _mois, $this -> _jour, $this -> _annee) ;
	}
	
	public function getTS() { return $this -> _ts ; }
}
