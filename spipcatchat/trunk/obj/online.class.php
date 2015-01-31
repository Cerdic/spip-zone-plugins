<?php
class online
{ //---Déclaration des varibles---//
private $_chemin;
private $_db;
private $_nb;
private $_id;
private $_statut;
//------------------------------Le constructeur--------------------------------//
public function __construct($chemin)
{
	$this->_chemin=$chemin; $this->hydrate();
}
//------------------------------FONCTION PRIVE---------------------------------//	 
private function files()
{	
	do{$fichier=fopen($this->_chemin.'line.js','r+');
	ftruncate($fichier,0);
	$lock=fwrite($fichier,json_encode($this->_db));
	fflush($fichier);
	fclose($fichier);
	}while(!$lock);	
} 
//----------------------------------LES SETTERS-------------------------------------//	
private function set_record()
{
	$table=$this->_db;
	$id=$this->_id;
	$statut=$this->_statut;
	$table[$id]=$statut;	
	$this->_db = $table;
	$this->files();
}
private function set_del()
{
	$table=$this->_db;
	$id=$this->_id;
	$statut=$this->_statut;
		if(is_array($table) && array_key_exists($id,$table))
		{
		  unset($table[$id]);
		  $this->_db=$table;
		  $this->files();
		 }
		 else { return false;}
}
//----------------------------------LES GETTERS-------------------------------------//	
private function get_count()
{
	return  $this->_nb;
}
private function get_statut()
{
	$table=$this->_db;
	$id=$this->_id;
	$statut=$this->_statut;
		if(is_array($table) && array_key_exists($id,$table))
		  {return $table[$id];}
		else{ return false;}
}	
private function get_tableau()
{
		if(is_array($this->_db))
		     { return $this->_db;}
		else { return false; }
}
//---------------------------------HYDRATE-------------------------------------//		
private function hydrate()
{
	if(file_exists($this->_chemin.'line.js'))
	{ 
		if(false!=($liste=file_get_contents($this->_chemin.'line.js'))){
		$db=json_decode($liste,true);
		if(is_array($db))
			{ $this->_db=$db; $this->_nb=count($db);}
		  }
		}
		else
		{	file_put_contents($this->_chemin.'line.js','{}');}
	} 
//-------------------------------Methode public--------------------------------// 	
public function execute($func,$id='',$statut='')
{	$id=preg_replace('#^id#','',$id);
	$this->_id='id'.$id;
	$this->_statut=$statut;
	$funcget=ucfirst('get_'.$func);
	$funcset=ucfirst('set_'.$func);
	if(method_exists($this,$funcget))
	{
	return $this->$funcget();
		}
	elseif(method_exists($this,$funcset))
	{return $this->$funcset();}
	}
} ?>