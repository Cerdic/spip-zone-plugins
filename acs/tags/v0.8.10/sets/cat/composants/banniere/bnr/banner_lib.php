<?PHP

/* getBanners function read banners files names.
 *
 */

function getBanners($path = '.', $exclut = "")
	{
	$filetypes = array('gif', 'jpg','png','jpeg');
	$r = array();
	foreach($filetypes as $type) {
		$r = array_merge($r, expFp($path, $type, $exclut));
	}
	return $r;
	}

 function expFp($folder = ".", $filetype = "", $exclut="") {
    $currdir=getcwd();
    if ($folder && @is_dir("$currdir/$folder"))
        chdir("$currdir/$folder");
    $dh = opendir(".");
		$a_files = array();
    while(false !== ($file = readdir($dh))) {
    	if (@is_dir($file)) continue;
      // insert all the files except $exclut in an array
      if (is_file($file) &&
        ( strtoupper( substr( $file,(-1*strlen($filetype))))==strtoupper($filetype)) &&
				($file != $exclut))
        $a_files[] = $file;
    }
    closedir($dh);
    chdir($currdir);
    return $a_files;
}

?>
