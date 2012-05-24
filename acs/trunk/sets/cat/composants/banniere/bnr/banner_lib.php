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


 function expFp($folder = ".", $filetype = "", $exclut="")
    {
    $currdir=getcwd();
    if ($folder && @is_dir("$currdir/$folder"))
        chdir("$currdir/$folder");
    $dh = opendir(".");
		$a_files = array();
      while(false !== ($file = readdir($dh)))
        {
        // insert all the files in an array
        if(is_file($file) &&
            ( strtoupper( substr( $file,(-1*strlen($filetype))))==strtoupper($filetype)) &&
						($file != $exclut)
					)
            $a_files[] = $file;
        if (@is_dir($file) && $file!="." && $file!=".." && $file!=".xvpics")
            $a_files[$file] = expFp($file, $filetype);
        }
    closedir($dh);
    chdir($currdir);
 //    if (is_array($a_files))
 //        array_multisort($a_files);
    return $a_files;
    }


?>
