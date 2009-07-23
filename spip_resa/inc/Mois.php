<?php

require_once 'Jour.php' ;

class MoisException extends Exception
{}

class Mois
{
	protected $_annee = 0 ;
	protected $_mois = 0 ;
	protected $_ts = 0 ;
	protected $_premierJour = 0 ;
	protected $_nbJours = 0 ;
	protected $_jours = array() ;
	public static $minJour = 7 ;
	public static $maxJour = 0 ;
	
	public static $moisFr = array(1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre') ;
	
	public function __construct($mois, $annee)
	{
		$this -> _annee = (int) $annee ;
		$this -> _mois = (int) $mois ;
		
		$this -> _init() ;
	}
	
	protected function _init()
	{
		if( $this -> _annee > 0 && $this -> _mois > 0 && $this -> _mois <= 12 )
		{
			$this -> _ts = mktime(0, 0, 0, $this -> _mois, 1, $this -> _annee) ;
			$this -> _premierJour = (int) date('N', $this -> _ts) ;
			$this -> _nbJours = (int) date('t', $this -> _ts) ;
			
			self::$minJour = self::$minJour < $this -> _premierJour ? self::$minJour : $this -> _premierJour ;
			self::$maxJour = self::$maxJour > $this -> _premierJour + $this -> _nbJours ? self::$maxJour : $this -> _premierJour + $this -> _nbJours ;
			for( $i = 1 ; $i <= $this -> _nbJours ; $i++ )
			{
				$this -> _jours[] = new Jour($i, $this -> _mois, $this -> _annee) ;
			}
		} else throw new MoisException('Le format de l\'année / du mois n\'est pas valide.') ;
	}
	
	public function jour($num)
	{
		if(
			date('n', strtotime('+' . $num - $this -> _premierJour . ' days', $this -> _ts)) === date('n', $this -> _ts) 
			&& $num >= $this -> _premierJour
		)
			return $this -> _jours[$num - $this -> _premierJour] ;
		else return null ;
	}
	
	public function __toString()
	{
		return self::$moisFr[date('n', $this -> _ts)] ;
	}
}
