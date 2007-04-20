<?php
//------------------------------------------------------------------------------
// editable files:
$GLOBALS['spx']['editable_ext'] = array(
	'\.txt$|\.php$|\.php3$|\.phtml$|\.inc$|\.sql$|\.pl$',
	'\.htm$|\.html$|\.shtml$|\.dhtml$|\.xml$',
	'\.js$|\.css$|\.cgi$|\.cpp$\.c$|\.cc$|\.cxx$|\.hpp$|\.h$',
	'\.pas$|\.p$|\.java$|\.py$|\.sh$\.tcl$|\.tk$'
);
//------------------------------------------------------------------------------
// image files:
$GLOBALS['spx']['images_ext']='\.png$|\.bmp$|\.jpg$|\.jpeg$|\.gif$';
//------------------------------------------------------------------------------
// mime types: (description,image,extension)
$GLOBALS['spx']['super_mimes']=array(
	// dir, exe, file
	'dir'	=> array($GLOBALS['spx']['mimes']['dir'],'dir.gif'),
	'exe'	=> array($GLOBALS['spx']['mimes']['exe'],'exe.gif','\.exe$|\.com$|\.bin$'),
	'file'	=> array($GLOBALS['spx']['mimes']['file'],'file.gif')
);
$GLOBALS['spx']['used_mime_types']=array(
	// text
	'text'	=> array($GLOBALS['spx']['mimes']['text'],'txt.gif','\.txt$'),
	
	// programming
	'php'	=> array($GLOBALS['spx']['mimes']['php'],'php.gif','\.php$|\.php3$|\.phtml$|\.inc$'),
	'sql'	=> array($GLOBALS['spx']['mimes']['sql'],'src.gif','\.sql$'),
	'perl'	=> array($GLOBALS['spx']['mimes']['perl'],'pl.gif','\.pl$'),
	'html'	=> array($GLOBALS['spx']['mimes']['html'],'html.gif','\.htm$|\.html$|\.shtml$|\.dhtml$|\.xml$'),
	'js'	=> array($GLOBALS['spx']['mimes']['js'],'js.gif','\.js$'),
	'css'	=> array($GLOBALS['spx']['mimes']['css'],'src.gif','\.css$'),
	'cgi'	=> array($GLOBALS['spx']['mimes']['cgi'],'exe.gif','\.cgi$'),
	//'py'	=> array($GLOBALS['spx']['mimes']['py'],'py.gif','\.py$'),
	//'sh'	=> array($GLOBALS['spx']['mimes']['sh'],'sh.gif','\.sh$'),
	// C++
	'cpps'	=> array($GLOBALS['spx']['mimes']['cpps'],'cpp.gif','\.cpp$|\.c$|\.cc$|\.cxx$'),
	'cpph'	=> array($GLOBALS['spx']['mimes']['cpph'],'h.gif','\.hpp$|\.h$'),
	// Java
	'javas'	=> array($GLOBALS['spx']['mimes']['javas'],'java.gif','\.java$'),
	'javac'	=> array($GLOBALS['spx']['mimes']['javac'],'java.gif','\.class$|\.jar$'),
	// Pascal
	'pas'	=> array($GLOBALS['spx']['mimes']['pas'],'src.gif','\.p$|\.pas$'),
	
	// images
	'gif'	=> array($GLOBALS['spx']['mimes']['gif'],'image.gif','\.gif$'),
	'jpg'	=> array($GLOBALS['spx']['mimes']['jpg'],'image.gif','\.jpg$|\.jpeg$'),
	'bmp'	=> array($GLOBALS['spx']['mimes']['bmp'],'image.gif','\.bmp$'),
	'png'	=> array($GLOBALS['spx']['mimes']['png'],'image.gif','\.png$'),
	
	// compressed
	'zip'	=> array($GLOBALS['spx']['mimes']['zip'],'zip.gif','\.zip$'),
	'tar'	=> array($GLOBALS['spx']['mimes']['tar'],'tar.gif','\.tar$'),
	'gzip'	=> array($GLOBALS['spx']['mimes']['gzip'],'tgz.gif','\.tgz$|\.gz$'),
	'bzip2'	=> array($GLOBALS['spx']['mimes']['bzip2'],'tgz.gif','\.bz2$'),
	'rar'	=> array($GLOBALS['spx']['mimes']['rar'],'tgz.gif','\.rar$'),
	//'deb'	=> array($GLOBALS['spx']['mimes']['deb'],'package.gif','\.deb$'),
	//'rpm'	=> array($GLOBALS['spx']['mimes']['rpm'],'package.gif','\.rpm$'),
	
	// music
	'mp3'	=> array($GLOBALS['spx']['mimes']['mp3'],'mp3.gif','\.mp3$'),
	'wav'	=> array($GLOBALS['spx']['mimes']['wav'],'sound.gif','\.wav$'),
	'midi'	=> array($GLOBALS['spx']['mimes']['midi'],'midi.gif','\.mid$'),
	'real'	=> array($GLOBALS['spx']['mimes']['real'],'real.gif','\.rm$|\.ra$|\.ram$'),
	//'play'	=> array($GLOBALS['spx']['mimes']['play'],'mp3.gif','\.pls$|\.m3u$'),
	
	// movie
	'mpg'	=> array($GLOBALS['spx']['mimes']['mpg'],'video.gif','\.mpg$|\.mpeg$'),
	'mov'	=> array($GLOBALS['spx']['mimes']['mov'],'video.gif','\.mov$'),
	'avi'	=> array($GLOBALS['spx']['mimes']['avi'],'video.gif','\.avi$'),
	'flash'	=> array($GLOBALS['spx']['mimes']['flash'],'flash.gif','\.swf$'),
	
	// Micosoft / Adobe
	'word'	=> array($GLOBALS['spx']['mimes']['word'],'word.gif','\.doc$'),
	'excel'	=> array($GLOBALS['spx']['mimes']['excel'],'spread.gif','\.xls$'),
	'pdf'	=> array($GLOBALS['spx']['mimes']['pdf'],'pdf.gif','\.pdf$')
);
//------------------------------------------------------------------------------
?>
