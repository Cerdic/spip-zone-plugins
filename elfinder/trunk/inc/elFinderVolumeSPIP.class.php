<?php

/**
 * Simple elFinder driver for SPIP.
 *
 * @author Guillaume Wauquier inspired by Dmitry (dio) Levashov
 **/
class elFinderVolumeSPIP extends elFinderVolumeDriver {
	
	/**
	 * Driver id
	 * Must be started from letter and contains [a-z0-9]
	 * Used as part of volume id
	 *
	 * @var string
	 **/
	protected $driverId = 's';
	
	/**
	 * Database object
	 *
	 * @var mysqli
	 **/
	protected $db = null;
	
	/**
	 * Tables to store files
	 *
	 * @var string
	 **/
	protected $tbf = '';
	
	/**
	 * Directory for tmp files
	 * If not set driver will try to use tmbDir as tmpDir
	 *
	 * @var string
	 **/
	protected $tmpPath = _DIR_TMP;
	
	/**
	 * Numbers of sql requests (for debug)
	 *
	 * @var int
	 **/
	protected $sqlCnt = 0;
	
	/**
	 * Last db error message
	 *
	 * @var string
	 **/
	protected $dbError = '';
	
	/**
	 * Constructor
	 * Extend options with required fields
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function __construct() {
		$opts = array(
			'host'          => 'localhost',
			'user'          => '',
			'pass'          => '',
			'db'            => '',
			'port'          => null,
			'socket'        => null,
			'files_table'   => 'spip_rubriques',
			'tmbPath'       => '',
			'tmpPath'       => _DIR_TMP
		);
		$this->options = array_merge($this->options, $opts);
		$this->options['mimeDetect'] = 'internal';
	}
	
	/*********************************************************************/
	/*                        INIT AND CONFIGURE                         */
	/*********************************************************************/
	
	/**
	 * Prepare driver before mount volume.
	 * Connect to db, check required tables and fetch root path
	 *
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function init() {
		

		/*
		if (
		!$this->options['path']
		||  !$this->options['files_table']) {
			return false;
		}
		
		$this->db = new mysqli($this->options['host'], $this->options['user'], $this->options['pass'], $this->options['db'], $this->options['port'], $this->options['socket']);
		if ($this->db->connect_error || @mysqli_connect_error()) {
			return false;
		}
		
		$this->db->set_charset('utf8');

		if ($res = $this->db->query('SHOW TABLES')) {
			while ($row = $res->fetch_array()) {
				if ($row[0] == $this->options['files_table']) {
					$this->tbf = $this->options['files_table'];
					break;
				}
			}
		}

		if (!$this->tbf) {
			return false;
		}
		
		$this->updateCache($this->options['path'], $this->_stat($this->options['path']));*/
		return true;
	}



	/**
	 * Set tmp path
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function configure() {
		parent::configure();
		
		if (($tmp = $this->options['tmpPath'])) {
			if (!file_exists($tmp)) {
				if (@mkdir($tmp)) {
					@chmod($tmp, $this->options['tmbPathMode']);
				}
			}
			
			$this->tmpPath = is_dir($tmp) && is_writable($tmp) ? $tmp : false;
		}
		
		if (!$this->tmpPath && $this->tmbPath && $this->tmbPathWritable) {
			$this->tmpPath = $this->tmbPath;
		}

		$this->mimeDetect = 'internal';
	}
	
	/**
	 * Close connection
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function umount() {
		/*$this->db->close();*/
	}
	
	/**
	 * Return debug info for client
	 *
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function debug() {
		$debug = parent::debug();
		$debug['sqlCount'] = $this->sqlCnt;
		if ($this->dbError) {
			$debug['dbError'] = $this->dbError;
		}
		return $debug;
	}

	/**
	 * Perform sql query and return result.
	 * Increase sqlCnt and save error if occured
	 *
	 * @param  string  $sql  query
	 * @return misc
	 * @author Dmitry (dio) Levashov
	 **/
	protected function query($sql) {
		$this->sqlCnt++;
		$res = $this->db->query($sql);
		if (!$res) {
			$this->dbError = $this->db->error;
		}
		return $res;
	}

	/**
	 * Create empty object with required mimetype
	 *
	 * @param  string  $path  parent dir path
	 * @param  string  $name  object name
	 * @param  string  $mime  mime type
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function make($path, $name, $mime) {
		
		if($mime=='directory'){
			
		}
		else{
			
			
				//$ajouter_documents = charger_fonction('ajouter_documents', 'action');
				//$nouveaux_doc = $ajouter_documents('new',$files,$objet,$id_objet,'document');
			
			$result=true;
			//$sql = 'INSERT INTO %s (`id_parent`, `titre`, `size`, `mtime`, `mime`, `content`, `read`, `write`) VALUES ("%s", "%s", 0, %d, "%s", "", "%d", "%d")';
		}
		return $result;
	}

	/**
	 * Return temporary file path for required file
	 *
	 * @param  string  $path   file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function tmpname($path) {
		return $this->tmpPath.DIRECTORY_SEPARATOR.md5($path);
	}

	/**
	 * Resize image
	 *
	 * @param  string   $hash    image file
	 * @param  int      $width   new width
	 * @param  int      $height  new height
	 * @param  bool     $crop    crop image
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
	public function resize($hash, $width, $height, $x, $y, $mode = 'resize', $bg = '', $degree = 0) {
		if ($this->commandDisabled('resize')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (($file = $this->file($hash)) == false) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if (!$file['write'] || !$file['read']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		$path = $this->decode($hash);
		
		if (!$this->canResize($path, $file)) {
			return $this->setError(elFinder::ERROR_UNSUPPORT_TYPE);
		}

		$img = $this->tmpname($path);
		
		if (!($fp = @fopen($img, 'w+'))) {
			return false;
		}

		if (($res = $this->query('SELECT content FROM '.$this->tbf.' WHERE id="'.$path.'"'))
		&& ($r = $res->fetch_assoc())) {
			fwrite($fp, $r['content']);
			rewind($fp);
			fclose($fp);
		} else {
			return false;
		}


		switch($mode) {
			
			case 'propresize':
				$result = $this->imgResize($img, $width, $height, true, true);
				break;

			case 'crop':
				$result = $this->imgCrop($img, $width, $height, $x, $y);
				break;

			case 'fitsquare':
				$result = $this->imgSquareFit($img, $width, $height, 'center', 'middle', $bg ? $bg : $this->options['tmbBgColor']);
				break;
			
			default:
				$result = $this->imgResize($img, $width, $height, false, true);
				break;				
    	}
		
		if ($result) {
			
			$sql = sprintf('UPDATE %s SET content=LOAD_FILE("%s"), mtime=UNIX_TIMESTAMP() WHERE id=%d', $this->tbf, $this->loadFilePath($img), $path);
			
			if (!$this->query($sql)) {
				$content = file_get_contents($img);
				$sql = sprintf('UPDATE %s SET content="%s", mtime=UNIX_TIMESTAMP() WHERE id=%d', $this->tbf, $this->db->real_escape_string($content), $path);
				if (!$this->query($sql)) {
					@unlink($img);
					return false;
				}
			}
			@unlink($img);
			if (!empty($file['tmb']) && $file['tmb'] != "1") {
				$this->rmTmb($file['tmb']);
			}
			$this->clearcache();
			return $this->stat($path);
		}
		
   		return false;
	}
	

	/*********************************************************************/
	/*                               FS API                              */
	/*********************************************************************/
	
	/**
	 * Cache dir contents
	 *
	 * @param  string  $path  dir path
	 * @return void
	 * @author Dmitry Levashov
	 **/

	protected function cacheDir($path) {
		$this->dirsCache[$path] = array();
		$tab_result=sql_allfetsel('distinct r.id_rubrique as id, r.id_parent as parent_id, r.titre as name, 0 as taille,  UNIX_TIMESTAMP(maj)  AS ts, \'directory\' as mime, 1 as `read`, 1 as `write`, 0 as `locked`, 0 as `hidden`, 0 as largeur,0 as hauteur, 1 AS dirs',array('r'=>'spip_rubriques'),'r.id_parent='.$path);
		if (!empty($tab_result)) {
			
			foreach ($tab_result as $row) {
				 //debug($row);
				 //print_r($row);
				$id = $row['id'];
				if ($row['parent_id']==0) {
					$row['phash'] = $this->encode($row['parent_id']);
				} 
				
				
				unset($row['width']);
				unset($row['height']);
				unset($row['id']);
				unset($row['parent_id']);
				
				if (($stat = $this->updateCache($id, $row)) && empty($stat['hidden'])) {
					$this->dirsCache[$path.""][] = $id;
					
				}
				
				
			}
		
		}

		if($path!=0){
		$sql = 'SELECT concat(\'d\',f.id_document) as id, '.$path.' as parent_id, IF(f.titre = \'\', SUBSTRING(f.fichier,LENGTH(f.extension)+2), f.titre) as name, f.taille as size, UNIX_TIMESTAMP(f.maj) AS ts, td.mime_type as mime, 1 as `read`, 1 as `write`, 0 as `locked`, 0 as `hidden`, largeur as width, hauteur as height, 0 AS dirs
				FROM spip_documents AS f , spip_documents_liens as fl  ,spip_types_documents AS td
				WHERE f.extension=td.extension and f.id_document=fl.id_document and fl.objet=\'rubrique\' and fl.id_objet=\''.$path.'\' 
				GROUP BY f.id_document';
		$res = sql_query($sql);
		if ($res) {
			
			while ($row = sql_fetch($res)) {

				 //debug($row);
				$id = $row['id'];
				if ($row['parent_id']) {
					$row['phash'] = $this->encode($row['parent_id']);
				} 

				
				unset($row['id']);
				unset($row['parent_id']);
				//echo($id);
				
				if (($stat = $this->updateCache($id, $row)) && empty($stat['hidden'])) {
					$this->dirsCache[$path][] = $id;
				}
			}
		}
						
		$sql = 'SELECT concat(\'a\',f.id_article) as id, f.id_rubrique as parent_id, f.titre as name, 0 as size, UNIX_TIMESTAMP(f.maj) AS ts, \'text/html\' as mime, 1 as `read`, 1 as `write`, 0 as `locked`, 0 as `hidden`, 0 as width, 0 as height, 0 AS dirs
						FROM spip_articles AS f 
						WHERE f.id_rubrique='.$path;
		//echo($sql);
		$res = sql_query($sql);
		if ($res) {
			while ($row = sql_fetch($res)) {
				 //debug($row);
				$id = $row['id'];
				if ($row['parent_id']) {
					$row['phash'] = $this->encode($row['parent_id']);
				} 
				
				if ($row['mime'] == 'directory') {
					unset($row['width']);
					unset($row['height']);
				} else {
					//echo($id);
					unset($row['dirs']);
			}
					
				unset($row['id']);
				unset($row['parent_id']);
				//echo($id);
				
				if (($stat = $this->updateCache($id, $row)) && empty($stat['hidden'])) {
					$this->dirsCache[$path][] = $id;
				}
			}
		}
		}
		return $this->dirsCache[$path];
	}

	/**
	 * Return array of parents paths (ids)
	 *
	 * @param  int   $path  file path (id)
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function getParents($path) {
		$parents = array();

		while ($path) {
			if ($file = $this->stat($path)) {
				array_unshift($parents, $path);
				$path = isset($file['phash']) ? $this->decode($file['phash']) : false;
			}
		}
		
		if (count($parents)) {
			array_pop($parents);
		}
		return $parents;
	}

	/**
	 * Return correct file path for LOAD_FILE method
	 *
	 * @param  string $path  file path (id)
	 * @return string
	 * @author Troex Nevelin
	 **/
	protected function loadFilePath($path) {
		$realPath = realpath($path);
		if (DIRECTORY_SEPARATOR == '\\') { // windows
			$realPath = str_replace('\\', '\\\\', $realPath);
		}
		return $this->db->real_escape_string($realPath);
	}

	/*********************** paths/urls *************************/
	
	/**
	 * Return parent directory path
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _dirname($path) {
		return ($stat = $this->stat($path)) ? ($stat['phash'] ? $this->decode($stat['phash']) : $this->root) : false;
	}

	/**
	 * Return file name
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _basename($path) {
		return ($stat = $this->stat($path)) ? $stat['name'] : false;
	}

	/**
	 * Join dir name and file name and return full path
	 *
	 * @param  string  $dir
	 * @param  string  $name
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _joinPath($dir, $name='',$type='') {
		
		if ($type=='d'){
			$sql = 'SELECT concat(\'d\',f.id_document) as id
			FROM spip_documents AS f , spip_documents_liens as fl  ,spip_types_documents AS td
			WHERE fl.id_objet='.intval($dir).' and f.id_document=\''.$name.'\' and f.extension=td.extension and f.id_document=fl.id_document and fl.objet=\'rubrique\'
			GROUP BY f.id_document';
			$res=sql_query($sql);
			$id = sql_fetch($res);
			$id=$id['id'];
			//echo($sql);
		}
		elseif($type=='a'){
			$sql = 'SELECT concat(\'a\',f.id_article) as id
			FROM spip_articles AS f
			WHERE f.id_rubrique='.intval($dir).' and f.id_article='.$name
			.' GROUP BY f.id_article';
			//echo($sql);
			$res=sql_query($sql);
			$id = sql_fetch($res);
			$id=$id['id'];
			
			
		}
		else{
			
			$where="";
			if($name)
				$where=' and titre='.sql_quote($name);

			$id = sql_getfetsel('id_rubrique as id','spip_rubriques','id_parent='.intval($dir).$where);

		}

		if (!empty($id)) {
			$this->updateCache($id, $this->_stat($id));
			return $id;
		}
		return -1;
	}
	
	/**
	 * Return normalized path, this works the same as os.path.normpath() in Python
	 *
	 * @param  string  $path  path
	 * @return string
	 * @author Troex Nevelin
	 **/
	protected function _normpath($path) {
		return $path;
	}
	
	/**
	 * Return file path related to root dir
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _relpath($path) {
		return $path;
	}
	
	/**
	 * Convert path related to root dir into real path
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _abspath($path) {
		return $path;
	}
	
	/**
	 * Return fake path started from root dir
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _path($path) {
		if (($file = $this->stat($path)) == false) {
			return '';
		}
		
		$parentsIds = $this->getParents($path);
		$path = '';
		foreach ($parentsIds as $id) {
			$dir = $this->stat($id);
			$path .= $dir['name'].$this->separator;
		}
		return $path.$file['name'];
	}
	
	/**
	 * Return true if $path is children of $parent
	 *
	 * @param  string  $path    path to check
	 * @param  string  $parent  parent path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _inpath($path, $parent) {
		return $path == $parent
			? true
			: in_array($parent, $this->getParents($path));
	}
	
	/***************** file stat ********************/
	/**
	 * Return stat for given path.
	 * Stat contains following fields:
	 * - (int)    size    file size in b. required
	 * - (int)    ts      file modification time in unix time. required
	 * - (string) mime    mimetype. required for folders, others - optionally
	 * - (bool)   read    read permissions. required
	 * - (bool)   write   write permissions. required
	 * - (bool)   locked  is object locked. optionally
	 * - (bool)   hidden  is object hidden. optionally
	 * - (string) alias   for symlinks - link target path relative to root path. optionally
	 * - (string) target  for symlinks - link target path. optionally
	 *
	 * If file does not exists - returns empty array or false.
	 *
	 * @param  string  $path    file path 
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _stat($path) {
		//echo($this->root);
		//echo('<br>ssss'.$path);

		if (substr($path,0,1)=='d')
			{
				$sql = 'SELECT concat(\'d\',f.id_document) as id, fl.id_objet as parent_id, IF(f.titre = \'\', SUBSTRING(f.fichier,LENGTH(f.extension)+2),f.titre) as name, f.taille as size, UNIX_TIMESTAMP(f.maj) AS ts, td.mime_type as mime, 1 as `read`, 1 as `write`, 0 as `locked`, 0 as `hidden`, largeur as width, hauteur as height, 0 AS dirs
					FROM spip_documents AS f , spip_documents_liens as fl  ,spip_types_documents AS td
					WHERE f.id_document=\''.substr($path,1).'\' and f.extension=td.extension and f.id_document=fl.id_document and fl.objet=\'rubrique\'
					GROUP BY f.id_document';
					
			}
		elseif(substr($path,0,1)=='a')
			{
				$sql = 'SELECT concat(\'a\',f.id_article) as id, f.id_rubrique as parent_id, f.titre as name, 0 as size, UNIX_TIMESTAMP(f.maj) AS ts, \'text/html\' as mime, 1 as `read`, 1 as `write`, 0 as `locked`, 0 as `hidden`, 0 as width, 0 as height, 0 AS dirs
				FROM spip_articles AS f 
				WHERE f.id_article='.substr($path,1);

				
			}
			else
			{
				$sql = 'SELECT r.id_rubrique as id, r.id_parent as parent_id, r.titre as name, 0 as taille, UNIX_TIMESTAMP(maj) AS ts, \'directory\' as mime, 1 as `read`, 1 as `write`, 0 as `locked`, 0 as `hidden`, 0 as largeur,0 as hauteur, 1 AS dirs
				FROM spip_rubriques AS r 
				WHERE r.id_rubrique='.$path;
				if ($path==0)
					$sql = 'SELECT 0 as id, \'-1\' as parent_id, \'racine du site\' as name, 0 as taille, UNIX_TIMESTAMP(maj) AS ts, \'directory\' as mime, 1 as `read`, 1 as `write`, 0 as `locked`, 0 as `hidden`, 0 as largeur,0 as hauteur, 1 AS dirs
					FROM spip_rubriques AS r 
					LIMIT 0,1';
				
			}
		//echo($sql);
		$res = sql_query($sql);
		if ($res) {
			$stat = sql_fetch($res);
			$sql="";
		
			//if (substr($path,0,1)=='d')print_r($stat);
			if ( $this->root!==$path) {
				$stat['phash'] = $this->encode($stat['parent_id']);
			} 
			if ($stat['mime'] == 'directory') {
				unset($stat['width']);
				unset($stat['height']);
			} else {
				unset($stat['dirs']);
			}
			unset($stat['id']);
			unset($stat['parent_id']);
			//print_r($stat);
			return $stat;
		}
		
		return array();
	}
	
	/**
	 * Return true if path is dir and has at least one childs directory
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _subdirs($path) {
		return ($stat = $this->stat($path)) && isset($stat['dirs']) ? $stat['dirs'] : false;
	}
	
	/**
	 * Return object width and height
	 * Usualy used for images, but can be realize for video etc...
	 *
	 * @param  string  $path  file path
	 * @param  string  $mime  file mime type
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _dimensions($path, $mime) {
		return ($stat = $this->stat($path)) && isset($stat['width']) && isset($stat['height']) ? $stat['width'].'x'.$stat['height'] : '';
	}
	
	/******************** file/dir content *********************/
		
	/**
	 * Return files list in directory.
	 *
	 * @param  string  $path  dir path
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _scandir($path) {
		return isset($this->dirsCache[$path])
			? $this->dirsCache[$path]
			: $this->cacheDir($path);
	}
		
	/**
	 * Open file and return file pointer
	 *
	 * @param  string  $path  file path
	 * @param  string  $mode  open file mode (ignored in this driver)
	 * @return resource|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _fopen($path, $mode='rb') {
		
			if ($fichier = sql_getfetsel('fichier','spip_documents','id_document='.substr($path,1))){
				//echo(realpath(_DIR_IMG.$fichier));
				//exit();
				$fp = @fopen(realpath(_DIR_IMG.$fichier), 'r+');
				
				if ($fp) 
					return $fp;
						
				}
		return false;
	}
	
	/**
	 * Close opened file
	 *
	 * @param  resource  $fp  file pointer
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _fclose($fp, $path='') {
		@fclose($fp);
		if ($path) {
			@unlink($this->tmpname($path));
		}
	}
	
	/********************  file/dir manipulations *************************/
	
	/**
	 * Create dir and return created dir path or false on failed
	 *
	 * @param  string  $path  parent dir path
	 * @param string  $name  new directory name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _mkdir($path, $name) {
		
		include_spip('action/editer_rubrique');
		$id_rubrique = rubrique_inserer($path);
		$tab_data = array('titre'=>$name);
		rubrique_modifier($id_rubrique,$tab_data);
		//echo($this->_joinPath($id_rubrique, $name));
		return ($id_rubrique!=0) ? $this->_joinPath($path, $name) : false;
	}
	
	/**
	 * Create file and return it's path or false on failed
	 *
	 * @param  string  $path  parent dir path
	 * @param string  $name  new file name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _mkfile($path, $name) {
		
		$file=_DIR_TMP.'elfinder/'.$name;
		if (($fp = @fopen($file, 'w'))) {
			@fwrite($fp," ");
			@fclose($file);
			@chmod($file, 0775);
		}
		$file = array('tmp_name'=>realpath($file),'name'=>$name,'distant'=>false,'titrer'=>true);
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		
		$nouveaux_doc = $ajouter_documents('new',array($file),'rubrique',$path,'document');
		$path = $path.DIRECTORY_SEPARATOR.'d'.$nouveaux_doc[0];
		$tab_data = array('titre'=>$name);
		
		
		return $this->make($path, $name, 'text/plain') ? $this->_joinPath($path, $nouveaux_doc[0],'d') : false;
	}
	
	/**
	 * Create symlink. FTP driver does not support symlinks.
	 *
	 * @param  string  $target  link target
	 * @param  string  $path    symlink path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _symlink($target, $path, $name) {
		return false;
	}
	
	/**
	 * Copy file into another file
	 *
	 * @param  string  $source     source file path
	 * @param  string  $targetDir  target directory path
	 * @param  string  $name       new file name
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _copy($source, $targetDir, $name) {
		$this->clearcache();
		$id = $this->_joinPath($targetDir, $name);

		$sql = $id > 0
			? sprintf('REPLACE INTO %s (id, parent_id, name, content, size, mtime, mime, width, height, `read`, `write`, `locked`, `hidden`) (SELECT %d, %d, name, content, size, mtime, mime, width, height, `read`, `write`, `locked`, `hidden` FROM %s WHERE id=%d)', $this->tbf, $id, $this->_dirname($id), $this->tbf, $source)
			: sprintf('INSERT INTO %s (parent_id, name, content, size, mtime, mime, width, height, `read`, `write`, `locked`, `hidden`) SELECT %d, "%s", content, size, %d, mime, width, height, `read`, `write`, `locked`, `hidden` FROM %s WHERE id=%d', $this->tbf, $targetDir, $this->db->real_escape_string($name), time(), $this->tbf, $source);

		return $this->query($sql);
	}
	
	
		/**
	 * Move file
	 * Return new file path or false.
	 *
	 * @param  string  $src   source path
	 * @param  string  $dst   destination dir path
	 * @param  string  $name  new file name 
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function move($src, $dst, $name) {
		$stat = $this->stat($src);
				
		$stat['realpath'] = $src;
	
		$this->clearcache();

		if ($this->_move($src, $dst, $name)) {
						
			$this->removed[] = $stat;
			if (substr($src,0,1)=='d')
				return $this->_joinPath($dst, substr($src,1), 'd');
			elseif (substr($src,0,1)=='a')
				return $this->_joinPath($dst, substr($src,1), 'a');
			else
				return $this->_joinPath($dst, $name);
			
		}
		return $this->setError(elFinder::ERROR_MOVE, $this->_path($src));
	}
	
	
		/**
	 * Return subdirs tree
	 *
	 * @param  string  $path  parent dir path
	 * @param  int     $deep  tree deep
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function gettree($path, $deep, $exclude='') {
		$dirs = array();
		

		!isset($this->dirsCache[$path]) && $this->cacheDir($path);

		foreach ($this->dirsCache[$path] as $p) {
			$stat = $this->stat($p);
			
			if ($stat && empty($stat['hidden']) && $path != $exclude && $stat['mime'] == 'directory') {
				$dirs[] = $stat;
				if ($deep > 0 && !empty($stat['dirs'])) {
					$dirs = array_merge($dirs, $this->gettree($p, $deep-1));
				}
			}
		}

		return $dirs;
	}	
	
	
	/**
	 * Return subfolders for required folder or false on error
	 *
	 * @param  string   $hash  folder hash or empty string to get tree from root folder
	 * @param  int      $deep  subdir deep
	 * @param  string   $exclude  dir hash which subfolders must be exluded from result, required to not get stat twice on cwd subfolders
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function tree($hash='', $deep=0, $exclude='') {
		$path = $hash ? $this->decode($hash) : $this->root;

		if (($dir = $this->stat($path)) == false || $dir['mime'] != 'directory') {
			return false;
		}

		$dirs = $this->gettree($path, $deep > 0 ? $deep -1 : $this->treeDeep-1, $this->decode($exclude));
		array_unshift($dirs, $dir);
		return $dirs;
	}
		
	
	
	
	
	
	
	/**
	 * Move file into another parent dir.
	 * Return new file path or false.
	 *
	 * @param  string  $source  source file path
	 * @param  string  $target  target dir path
	 * @param  string  $name    file name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _move($source, $targetDir, $name) {
		
		$ok=false;
		if(substr($source,0,1)=='d'){
			include_spip('action/editer_document');
			$id_document = intval(substr($source,1));
			$tab_parent=$this->getParents($source);
			$id_rub=array_pop($tab_parent);
			sql_updateq('spip_documents_liens',array('id_objet'=>$targetDir),'id_document='.$id_document.' and objet=\'rubrique\' and id_objet='.$id_rub);
			$tab_data = array('titre'=>$name);
			document_modifier($id_document,$tab_data);
			if(substr($this->_joinPath($targetDir,$id_document ,'d'),1)>0){
				$ok = true;
				}
		}
		elseif(substr($source,0,1)=='a'){
			include_spip('action/editer_article');
			
			$id_article = intval(substr($source,1));
			//echo($id_article);
			//exit();
			$tab_data = array('id_parent'=>$targetDir);
			$ok = article_instituer($id_article,$tab_data);
			if(substr($this->_joinPath($targetDir,$id_article ,'a'),1)>0){
				$ok = true;
				}

		}
		else{
			
			include_spip('action/editer_rubrique');
			$tab_data = array('titre'=>$name,'id_parent'=>$targetDir,'confirme_deplace'=>'oui');
			$ok = rubrique_modifier($source,$tab_data);
			$ok=empty($ok);
		}
		return $ok;
	}
		
	/**
	 * Remove file
	 *
	 * @param  string  $path  file path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _unlink($path) {
		
		
		//print_r($path);
		$id_objet=intval(substr($path,1));
		$objet=substr($path,0,1);
		switch($objet){
			case 'd':
				sql_delete('spip_documents_liens', 'id_document='.$id_objet);
				$supprimer_document = charger_fonction('supprimer_document', 'action');
				return $supprimer_document($id_objet);
			break;
			case 'a':
				sql_delete('spip_auteurs_liens', 'objet=\'article\' and id_objet='.$id_objet);
				sql_delete('spip_articles', 'id_article='.$id_objet);
				return true;
			break;
			
		//return $this->query(sprintf('DELETE FROM %s WHERE id=%d AND mime!="directory" LIMIT 1', $this->tbf, $path)) && $this->db->affected_rows;
		}
		return false;
	}

	/**
	 * Remove dir
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _rmdir($path) {
		
		$supprimer_rubrique = charger_fonction('supprimer_rubrique', 'action');
		$supprimer_rubrique($path);
		return ($path!=0) ? $this->_joinPath($path) : false;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Dmitry Levashov
	 **/
	protected function _setContent($path, $fp) {
		rewind($fp);
		$fstat = fstat($fp);
		$size = $fstat['size'];
		
		
	}
	
	/**
	 * Create new file and write into it from file pointer.
	 * Return new file path or false on error.
	 *
	 * @param  resource  $fp   file pointer
	 * @param  string    $dir  target dir path
	 * @param  string    $name file name
	 * @return bool|string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _save($fp, $dir, $name, $mime, $w, $h) {
		$this->clearcache();
		
		$id_document = $this->_joinPath($dir,$name,'d');
		rewind($fp);
		$stat = fstat($fp);
		$size = $stat['size'];
		
		if (($tmpfile = tempnam($this->tmpPath, $this->id))) {
			if (($trgfp = fopen($tmpfile, 'wb')) == false) {
				unlink($tmpfile);
			} else {
				while (!feof($fp)) {
					fwrite($trgfp, fread($fp, 8192));
				}
				fclose($trgfp);
				
				
				if($id_document > 0){
					
					include_spip('action/editer_document');
					document_modifier($id_document, $set);	
					
				} else {

					$file = array('tmp_name'=>realpath($tmpfile),'name'=>$name,'distant'=>false,'titrer'=>true);
					$ajouter_documents = charger_fonction('ajouter_documents', 'action');
					$nouveaux_doc = $ajouter_documents('new',array($file),'rubrique',$dir,'document');
					$id_document=$nouveaux_doc[0];
					}
					unlink($tmpfile);
				if ($id_document) {
					return 'd'.$id_document;
				}
				
			}
		}
	

		if ($this->query($sql)) {
			return $id > 0 ? $id : $this->db->insert_id;
		}
		
		return false;
	}
	
	/**
	 * Get file contents
	 *
	 * @param  string  $path  file path
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _getContents($path) {
		include_spip('inc/documents');
		return contenu_document(substr($path,1));
	}
	
	/**
	 * Write a string to a file
	 *
	 * @param  string  $path     file path
	 * @param  string  $content  new file content
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _filePutContents($path, $content) {
		return $this->query(sprintf('UPDATE %s SET content="%s", size=%d, mtime=%d WHERE id=%d LIMIT 1', $this->tbf, $this->db->real_escape_string($content), strlen($content), time(), $path));
	}

	/**
	 * Detect available archivers
	 *
	 * @return void
	 **/
	protected function _checkArchivers() {
		return;
	}

	/**
	 * Unpack archive
	 *
	 * @param  string  $path  archive path
	 * @param  array   $arc   archiver command and arguments (same as in $this->archivers)
	 * @return void
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
	protected function _unpack($path, $arc) {
		return;
	}

	/**
	 * Recursive symlinks search
	 *
	 * @param  string  $path  file/dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _findSymlinks($path) {
		return false;
	}

	/**
	 * Extract files from archive
	 *
	 * @param  string  $path  archive path
	 * @param  array   $arc   archiver command and arguments (same as in $this->archivers)
	 * @return true
	 * @author Dmitry (dio) Levashov, 
	 * @author Alexey Sukhotin
	 **/
	protected function _extract($path, $arc) {
		return false;
	}
	
	/**
	 * Create archive and return its path
	 *
	 * @param  string  $dir    target dir
	 * @param  array   $files  files names list
	 * @param  string  $name   archive name
	 * @param  array   $arc    archiver options
	 * @return string|bool
	 * @author Dmitry (dio) Levashov, 
	 * @author Alexey Sukhotin
	 **/
	protected function _archive($dir, $files, $name, $arc) {
		return false;
	}
	

		/**
	 * Put file stat in cache and return it
	 *
	 * @param  string  $path   file path
	 * @param  array   $stat   file stat
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function updateCache($path, $stat) {
		if (empty($stat) || !is_array($stat)) {
			return $this->cache[$path] = array();
		}

		$stat['hash'] = $this->encode($path);

		$root = $path === $this->root;
		
		if ($root) {
			$stat['volumeid'] = $this->id;
			if ($this->rootName) {
				$stat['name'] = $this->rootName;
			}
		} else {
			if (empty($stat['name'])) {
				$stat['name'] = $this->_basename($path);
			}
			if (empty($stat['phash'])) {
				$stat['phash'] = $this->encode($this->_dirname($path));
			}
		}
		
		// fix name if required
		if ($this->options['utf8fix'] && $this->options['utf8patterns'] && $this->options['utf8replace']) {
			$stat['name'] = json_decode(str_replace($this->options['utf8patterns'], $this->options['utf8replace'], json_encode($stat['name'])));
		}
		
		
		if (empty($stat['mime'])) {
			$stat['mime'] = $this->mimetype($stat['name']);
		}
		
		// @todo move dateformat to client
		$stat['date'] = isset($stat['ts'])
			? $this->formatDate($stat['ts'])
			: 'unknown';
			
		if (!isset($stat['size'])) {
			$stat['size'] = 'unknown';
		}	

		$stat['read']  = intval($this->attr($path, 'read', isset($stat['read']) ? !!$stat['read'] : false));
		$stat['write'] = intval($this->attr($path, 'write', isset($stat['write']) ? !!$stat['write'] : false));
		if ($root) {
			$stat['locked'] = 1;
		} elseif ($this->attr($path, 'locked', !empty($stat['locked']))) {
			$stat['locked'] = 1;
		} else {
			unset($stat['locked']);
		}

		if ($root) {
			unset($stat['hidden']);
		} elseif ($this->attr($path, 'hidden', !empty($stat['hidden'])) 
		|| !$this->mimeAccepted($stat['mime'])) {
			$stat['hidden'] = $root ? 0 : 1;
		} else {
			unset($stat['hidden']);
		}
		
		if ($stat['read'] && empty($stat['hidden'])) {
			
			if ($stat['mime'] == 'directory') {
				// for dir - check for subdirs

				if ($this->options['checkSubfolders']) {
					if (isset($stat['dirs'])) {
						if ($stat['dirs']) {
							$stat['dirs'] = 1;
						} else {
							unset($stat['dirs']);
						}
					} elseif (!empty($stat['alias']) && !empty($stat['target'])) {
						$stat['dirs'] = isset($this->cache[$stat['target']])
							? intval(isset($this->cache[$stat['target']]['dirs']))
							: $this->_subdirs($stat['target']);
						
					} elseif ($this->_subdirs($path)) {
						$stat['dirs'] = 1;
					}
				} else {
					$stat['dirs'] = 1;
				}
			} else {
				// for files - check for thumbnails
				$p = isset($stat['target']) ? $stat['target'] : $path;
				if ($this->tmbURL && !isset($stat['tmb']) && $this->canCreateTmb($p, $stat)) {
					$tmb = $this->gettmb($p, $stat);
					$stat['tmb'] = $tmb ? $tmb : 1;
				}
				
			}
		}
		
		if (!empty($stat['alias']) && !empty($stat['target'])) {
			$stat['thash'] = $this->encode($stat['target']);
			unset($stat['target']);
		}
		return $this->cache[$path] = $stat;
	}


	/**
	 * Paste files
	 *
	 * @param  Object  $volume  source volume
	 * @param  string  $source  file hash
	 * @param  string  $dst     destination dir hash
	 * @param  bool    $rmSrc   remove source after copy?
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function paste($volume, $src, $dst, $rmSrc = false) {
		$err = $rmSrc ? elFinder::ERROR_MOVE : elFinder::ERROR_COPY;
		
		if ($this->commandDisabled('paste')) {
			return $this->setError($err, '#'.$src, elFinder::ERROR_PERM_DENIED);
		}
		
		if (($file = $volume->file($src, $rmSrc)) == false) {
			return $this->setError($err, '#'.$src, elFinder::ERROR_FILE_NOT_FOUND);
		}
			
		$name = $file['name'];
		$errpath = $volume->path($src);
		
		if (($dir = $this->dir($dst)) == false) {
			return $this->setError($err, $errpath, elFinder::ERROR_TRGDIR_NOT_FOUND, '#'.$dst);
		}
		
		if (!$dir['write'] || !$file['read']) {
			return $this->setError($err, $errpath, elFinder::ERROR_PERM_DENIED);
		}
		
		$destination = $this->decode($dst);
		
		if (($test = $volume->closest($src, $rmSrc ? 'locked' : 'read', $rmSrc))) {
			return $rmSrc
				? $this->setError($err, $errpath, elFinder::ERROR_LOCKED, $volume->path($test))
				: $this->setError($err, $errpath, elFinder::ERROR_PERM_DENIED);
		}
	
		$test = $this->_joinPath($destination, $name);
		
		if($test!=-1){
			$stat = $this->stat($test);
			$this->clearcache();
		}
		if ($stat) {
			if ($this->options['copyOverwrite']) {
				// do not replace file with dir or dir with file
				if (!$this->isSameType($file['mime'], $stat['mime'])) {
					return $this->setError(elFinder::ERROR_NOT_REPLACE, $this->_path($test));
				}
				// existed file is not writable
				if (!$stat['write']) {
					return $this->setError($err, $errpath, elFinder::ERROR_PERM_DENIED);
				}
				// existed file locked or has locked child
				if (($locked = $this->closestByAttr($test, 'locked', true))) {
					return $this->setError(elFinder::ERROR_LOCKED, $this->_path($locked));
				}
				// remove existed file
				if (!$this->remove($test)) {
					return $this->setError(elFinder::ERROR_REPLACE, $this->_path($test));
				}
			} else {
				$name = $this->uniqueName($destination, $name, ' ', false);
			}
		}
		
		// copy/move inside current volume
		if ($volume == $this) {
			$source = $this->decode($src);
			
			// do not copy into itself
			if ($this->_inpath($destination, $source)) {
				return $this->setError(elFinder::ERROR_COPY_INTO_ITSELF, $path);
			}
			$method = $rmSrc ? 'move' : 'copy';
		
			return ($path = $this->$method($source, $destination, $name)) ? $this->stat($path) : false;
		}
		
		
		// copy/move from another volume
		if (!$this->options['copyTo'] || !$volume->copyFromAllowed()) {
			return $this->setError(elFinder::ERROR_COPY, $errpath, elFinder::ERROR_PERM_DENIED);
		}
		
		if (($path = $this->copyFrom($volume, $src, $destination, $name)) == false) {
			return false;
		}
		
		if ($rmSrc) {
			if ($volume->rm($src)) {
				$this->removed[] = $file;
			} else {
				return $this->setError(elFinder::ERROR_MOVE, $errpath, elFinder::ERROR_RM_SRC);
			}
		}
		return $this->stat($path);
	}

	
} // END class 
