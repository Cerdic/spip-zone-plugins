<?php
class salon
{ //---Déclaration des varibles---//
	protected $_NodeBalise;
	protected $_NodeTexte;
	protected $id_balise;
	protected $_data; 
	protected $DB_url;
	protected $_id;
	protected $_charset;
//------------------------------FONCTION PRIVE---------------------------------//
	private function random($nb) 
		{ //--  Nom du répértoire aléatoire ---//
					$string = "";
					$chaine = "1234567890"; //--- On n'utilise pas l'alphabet pour ne pas créer de conflit avec la fonction javascript replace pour les émoticons----//
					srand((double)microtime()*1000000);
					for($i=0; $i<$nb; $i++) {
					$string .= $chaine[rand()%strlen($chaine)];
					}
			return $string;
		}
			
	private function salon_xml()
		{	//---------------Mise a jour du fichier XML menu-------------------//
			do{				
				$data=fopen($this->DB_url.'catchat.xml','r+');
				$lock=flock($data,LOCK_EX);// On acquière un verrou exclusif 
				if($lock)
				{
					ftruncate($data,0);	// effacement du contenu	
					$file='<?xml version="1.0" encoding="'.$this->_charset.'"?>';
					$file.="\n";
					$file.='<catchat>';
					$file.="\n";
					foreach($this->_NodeBalise as $key => $value )
						{
							if(!empty($value) && $value!='catchat')
						
						   {	
							if($this->id[$key]!='null')
							{
								if($value!="end")
								{  //Suppression des chevrons pour ne pas interférer dans la liste "OPTION" des salons
									$file.='<'.$value.' id="'.$this->id_balise[$key].'">'.str_replace(array('&#139;','&lt;','&#155;','&gt;','gt;','lt;','<','>'),'',$this->_NodeTexte[$key]).'</'.$value.'>';
									$file.="\n"; 
								}					
							}			
						  }									
						}	
							$file.='</catchat>';
							$file.="\n";
							$file.='<end></end>'; //END petite astuce pour arrêter la boucle while du menu des salons	
					fwrite($data,$file);
					fflush($data); // On libère le contenu avant d'enlever le verrou
					flock($data,LOCK_UN); // On enlève le verrou
				}
				fclose($data); // fermeture
			}
			while(!$lock); // si le fichier est en cours d'écriture on re-boucle
		}
		
	private function addXML($lien)
		{//-- Création de la liste des salons et des liens vers les répértoires
			$i=1;
			while(!$gogo)
			{
			if($this->id_balise[count($this->id_balise)-$i]!='null')
			{
				$id=($this->id_balise[count($this->id_balise)-$i]+1);
				$gogo=true;	
				}
				else
				{
					$i++;
				}
			}
			if(!$id)
			{$id=0;}
					array_push($this->_NodeTexte,utf8_encode($this->_data[0]));
					array_push($this->_NodeBalise,'nom');
					array_push($this->id_balise,$id);
					array_push($this->_NodeTexte,$lien);
					array_push($this->_NodeBalise,'lien');
					array_push($this-> id_balise,$id+1);
					array_push($this->_NodeTexte,$this->_data[1]);
					array_push($this->_NodeBalise,'public');
					array_push($this->id_balise,$id+2);
					array_push($this->_NodeTexte,$this->_data[2]);
					array_push($this->_NodeBalise,'admin');
					array_push($this->id_balise,$id+3);
					$this->salon_xml();					
		}
//------------------------------Le constructeur--------------------------------//
	public function __construct($chemin,$chaset)
		{	$this->_charset=$chaset;
			$this->DB_url = realpath($chemin).'/';
			if(!$this->hydrate())
			{	
				if(!file_exists($this->DB_url))
			{
				mkdir($this->DB_url,0777);
				file_put_contents($this->DB_url.'.htaccess',utf8_encode('deny from all'));
				file_put_contents($this->DB_url.'catchat.xml',utf8_encode(''));
			}
			elseif(file_exists($this->DB_url) && !file_exists($this->DB_url.'catchat.xml'))
			{
				file_put_contents($this->DB_url.'.htaccess',utf8_encode('deny from all'));
				file_put_contents($this->DB_url.'catchat.xml',utf8_encode('<end></end>'));
			}
				$this->hydrate();

				}
		}
//----------------------------------LES SETTERS-------------------------------------//
	private function Set_add()
		{
		$FileName=$this->random(25);
		if(!file_exists($this->DB_url.'/'.$FileName.'/'))
			{
			$this->addXML($FileName);
			mkdir($this->DB_url.$FileName,0777);
			file_put_contents($this->DB_url.'/'.$FileName.'/'.$FileName.'.js',json_encode(array($this->_data[2])) );
			file_put_contents($this->DB_url.'/'.$FileName.'/'.$FileName.'.catchat','' );
			file_put_contents($this->DB_url.'/'.$FileName.'/.htaccess',utf8_encode('deny from all'));
			}
		}		
	private function Set_upg()
		{ //------------Modification de la ligne ---------------------------//
			if(in_array($this->_id,$this->id_balise))
			{
				if(!empty($this->_data))
				{
					$this->_NodeTexte[array_search($this->_id,$this->id_balise)]=$this->_data;
					$this->salon_xml();
				}
			}	
		}
	private function Set_del()
		{//---------------Suppréssion du menu dans le tableau-----------------//
			if(in_array($this->_id,$this->id_balise))
			{
					$this->_NodeTexte[array_search($this->_id,$this->id_balise)]='';
					$this->_NodeBalise[array_search($this->_id,$this->id_balise)]='';
					$this->id_balise[array_search($this->_id,$this->id_balise)]='';
			$l=$this->_id+1;	
			 		$del=$this->_NodeTexte[array_search($l,$this->id_balise)];	
					$this->_NodeTexte[array_search($l,$this->id_balise)]='';
					$this->_NodeBalise[array_search($l,$this->id_balise)]='';
					$this->id_balise[array_search($l,$this->id_balise)]='';
			$m=$this->_id+2;
					$this->_NodeTexte[array_search($m,$this->id_balise)]='';
					$this->_NodeBalise[array_search($m,$this->id_balise)]='';
					$this->id_balise[array_search($m,$this->id_balise)]='';
			$n=$this->_id+3;		
					$this->_NodeTexte[array_search($n,$this->id_balise)]='';
					$this->_NodeBalise[array_search($n,$this->id_balise)]='';
					$this->id_balise[array_search($n,$this->id_balise)]='';					
					$this->salon_xml();	
					return 1;										
			}	
		}
//----------------------------------LES GETTERS-------------------------------------//
	private function Get_balise()
		{
		if(array_key_exists($this->_id,$this->_NodeBalise))
		{	
			return $this->_NodeBalise[$this->_id];
		}
		else
		{
		return false;	
		}		
		
		}
	private function Get_texte()
		{
			if(array_key_exists($this->_id,$this->_NodeTexte))
		{
			return $this->_NodeTexte[$this->_id];
			}
			else
			{
				return false;
			}			
		}
	private function Get_id()
		{
			if($this->_id)
			{
				return $this->id_balise[$this->_id];
				}	
		}

	private function Get_tableau()
		{	
			echo'<table width="40%" border="1px" style="margin:0 auto;background:#eee;"><td width="15%">'
			.implode("<br/><hr />&nbsp;",$this->_NodeBalise);
			echo'</td><td width="15%">'
			.implode("<br/><hr />&nbsp;",$this->_NodeTexte);
			echo'</td><td width="15%">'
			.implode("<br/><hr />&nbsp;",$this->id_balise);
			echo'</td></table>';				
		}
//-------------------------------Methode public--------------------------------// 
	public function execute($obj,$id,$data)
	{
		if(is_numeric($id) && $id>=0)
		{
			$this->_id=$id;
		}
	    $methodGet=ucfirst('get_'.$obj);
		$methodSet=ucfirst('set_'.$obj);
		if(method_exists($this,$methodGet))
		{
			return $this->$methodGet();
		}
		elseif(method_exists($this,$methodSet))
		{
		$this->_data=$data;
			return $this->$methodSet();		
		}
	}
//---------------------------------HYDRATE-------------------------------------//
	private function hydrate()
		{
		unset($this->_NodeBalise);
		unset($this->_NodeTexte);  
		unset($this->id_balise);
		$this->_NodeBalise=array();
		$this->_NodeTexte=array();  
		$this->id_balise=array();
			if(file_exists($this->DB_url.'/catchat.xml'))
			{
				$dataXML=fopen($this->DB_url.'/catchat.xml','r+');
				while(!feof($dataXML))
				{
				$balise=fgets($dataXML);			
					if(preg_match('#<([ a-zA-Z]{1,})>#U',$balise))
					{preg_match('#<([ a-zA-Z]{1,})>#U',$balise,$r);$r[2]="null";}
					else
					{preg_match('#<([ a-zA-Z]{1,}) id="([\w]{1,})">#U',$balise,$r);}
					preg_match('#>(.*)<#U',$balise,$c);
					if($r[1])
					{
					 array_push($this->_NodeBalise,$r[1]);
					 if($c[1]){array_push($this->_NodeTexte,$c[1]);}else{array_push($this->_NodeTexte,"null");}
					 array_push($this->id_balise,$r[2]);
					
					} 				 	
				}
			}					
		}
}
?>