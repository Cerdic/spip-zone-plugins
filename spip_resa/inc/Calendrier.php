<?php

define('CAL_NB_MOIS', 5) ;

require_once 'Mois.php' ;

class CalendrierException extends Exception
{}

class Calendrier
{
	protected $_id = 0 ;
	protected $_moisDebut = 0 ;
	protected $_anneeDebut = 0 ;
	protected $_nbMois = 0 ;
	protected $_mois = array() ;
	protected $_cbReserve = null ;
	protected $_cbFormat = null ;
	
	public function __construct($id, $mois=0, $annee=0, $nbMois=0)
	{
		$this -> _id = (int) $id ;
		$this -> _moisDebut = (int) $mois == 0 ? date('n') : $mois ;
		$this -> _anneeDebut = (int) $annee == 0 ? date('Y') : $annee ;
		$this -> _nbMois = (int) $nbMois == 0 ? CAL_NB_MOIS : $nbMois ;
		
		$this -> _init() ;
	}
	
	protected function _init()
	{
		if( $this -> _moisDebut > 0 && $this -> _moisDebut <= 12 && $this -> _anneeDebut > 0 )
		{
			if( $this -> _nbMois > 0 )
			{
				$annee = $this -> _anneeDebut ;
				$mois  = $this -> _moisDebut ;
				for( $i = 0 ; $i < $this -> _nbMois ; $i++ )
				{
					if( $mois > 12 ) {
						$annee++ ; $mois = 1 ;
					}
					$this -> _mois[] = new Mois($mois, $annee) ;
					$mois++ ;
				}
			} else throw new CalendrierException('Le nombre de mois à afficher est nul.') ;
		} else throw new CalendrierException('Le mois de départ n\'est pas correctement renseigné.') ;
	}
	
	public function setCallbackReserve($func)
	{
		$this -> _cbReserve = $func ;
	}
	
	public function setCallbackFormat($func)
	{
		$this -> _cbFormat = $func ;
	}
	
	public function __toString()
	{
		if( $this -> _cbReserve === null || $this -> _cbFormat === null ) throw new CalendrierException('Les callbacks nécessaires n\'ont pas été fournies.') ;
		
		$cbReserve = $this -> _cbReserve ;
		$cbFormat  = $this -> _cbFormat ;
		$output  = '' ;
		$output .= '<table id="calendrier-resa">' ;
			$output .= '<thead>' ;
				$output .= '<tr>' ;
					$output .= '<th class="empty"></th>' ;
					foreach( $this -> _mois as $mois ) {
						$output .= '<th>' . $mois . '</th>' ;
					}
				$output .= '</tr>' ;
			$output .= '</thead>' ;
			$output .= '<tbody>' ;
				$jourCourant = $i = Mois::$minJour ;
				for( ; $i < Mois::$maxJour ; $i++ )
				{
					if( $jourCourant > 7 ) $jourCourant = 1 ;
					$output .= '<tr>' ;
						$output .= '<td class="jours">' . utf8_encode(Jour::$joursFr[$jourCourant]) . '</td>' ;
						for( $j = 0 ; $j < $this -> _nbMois ; $j++ )
						{
							if( ($jour = $this -> _mois[$j] -> jour($i)) !== null )
								$output .= '<td id="td_' . $jour -> getTS() . '" class="' . ($cbReserve($jour -> getTS()) ? 'reserve' : 'libre') . '">' . $cbFormat($this -> _id, $jour -> getTS()) . '</td>' ;
							else
								$output .= '<td class="empty"></td>' ;
						}
					$output .= '</tr>' ;
					$jourCourant++ ;
				}
			$output .= '</tbody>' ;
		$output .= '</table>' ;
		
		return $output ;
	}
}
