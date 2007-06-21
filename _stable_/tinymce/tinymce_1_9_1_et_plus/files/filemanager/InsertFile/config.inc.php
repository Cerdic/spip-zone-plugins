<?php
/***********************************************************************
** Title.........:    Insert File Dialog, File Manager
** Version.......:    1.1
** Authors.......:    Al Rashid <alrashid@klokan.sk>
**                    Xiang Wei ZHUO <wei@zhuo.org>
** Filename......:    config.php
** URL...........:    http://alrashid.klokan.sk/insFile/
** Last changed..:    23 July 2004
***********************************************************************/
/*
 MY_DOCUMENT_ROOT
 File system path to the directory you want to manage the files and folders
 NOTE: This directory requires write permission by PHP. That is,
       PHP must be able to create files in this directory.
 NOTE2: without trailing slash
*/
//supprime le "/" final
if (strlen($_GET['base_path'])>0 && $_GET['base_path']{strlen($_GET['base_path'])-1}=='/')
	$_GET['base_path'] = substr($_GET['base_path'], 0, strlen($_GET['base_path'])-1);
if (strlen($_GET['base_url'])>0 && $_GET['base_url']{strlen($_GET['base_url'])-1}=='/')
	$_GET['base_url'] = substr($_GET['base_url'], 0, strlen($_GET['base_url'])-1);
//ajoute un "/" initial
if ($_GET['docs']{0}!='/')
	$_GET['docs'] = '/'.$_GET['docs'];
//langue
$lang_file = 'lang/lang-'.$_GET['lang'].'.php';
if (!is_file($lang_file))
	$_GET['lang']='en';

//$MY_DOCUMENT_ROOT     = 'C:/appserv/www/tinymce142/resource/insfile'; //* system path to the directory you want to manage the files and folders
//$MY_DOCUMENT_ROOT = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].'/'.'documents';
$MY_DOCUMENT_ROOT = $_GET['base_path'].$_GET['docs'];

/* MY_BASE_URL  Not used in htmlarea3-plugin version  */
//$MY_BASE_URL          = "http://localhost/tinymce142/resource/insfile";
//$MY_BASE_URL = 'http://'.$HTTP_SERVER_VARS['SERVER_NAME'].(($_SERVER['SERVER_PORT']!=80)?':'.$_SERVER['SERVER_PORT']:'').'/'.'documents';
$MY_BASE_URL = $_GET['base_url'].$_GET['docs'];

//ajouté par ACTi le 08/03/2006
//répertoire où se trouvent les icônes
//$MY_ICON_FOLDER = 'http://'.$HTTP_SERVER_VARS['SERVER_NAME'].(($_SERVER['SERVER_PORT']!=80)?':'.$_SERVER['SERVER_PORT']:'').'/'.'images/icones-documents';
//$MY_ICON_FOLDER = 'http://'.$HTTP_SERVER_VARS['SERVER_NAME'].(($_SERVER['SERVER_PORT']!=80)?':'.$_SERVER['SERVER_PORT']:'').'/plugins/tinymce/tiny_mce/plugins/filemanager/InsertFile/img/ext';
$MY_ICON_FOLDER = 'img/ext';

/*
 MY_URL_TO_OPEN_FILE
 The URL to the MY_DOCUMENT_ROOT path, the web browser needs to be able to see it.
 It can be protected via .htaccess on apache or directory permissions on IIS,
 check you web server documentation for futher information on directory protection
 If this directory needs to be publicly accessiable, remove scripting capabilities
 for this directory (i.e. disable PHP, Perl, CGI). We only want to store documents
 in this directory and its subdirectories.
 NOTE: without trailing slash
*/
//$MY_URL_TO_OPEN_FILE  = "http://localhost/tinymce142/resource/insfile"; 
$MY_URL_TO_OPEN_FILE = $MY_BASE_URL;

/* MY_ALLOW_CREATE   Boolean (false or true) whether creating folders is allowed or not. */
$MY_ALLOW_CREATE     = true;
/* $MY_ALLOW_DELETE  Boolean (false or true) whether deleting files and folders is allowed or not. */
$MY_ALLOW_DELETE     = true;
/* $MY_ALLOW_RENAME  Boolean (false or true) whether renaming files and folders is allowed or not. */
$MY_ALLOW_RENAME     = true;
/* $MY_ALLOW_MOVE    Boolean (false or true) whether moving files and folders is allowed or not. */
$MY_ALLOW_MOVE       = true;
/* $MY_ALLOW_UPLOAD  Boolean (false or true) whether uploading files is allowed or not. */
$MY_ALLOW_UPLOAD     = true;
/* MY_LIST_EXTENSIONS This array specifies which files are listed in dialog. Setting to null causes that all files are listed,case insensitive. */
$MY_LIST_EXTENSIONS  = array('html', 'doc', 'xls', 'txt', 'gif', 'jpeg', 'jpg', 'png', 'pdf', 'zip', 'pdf', 'ppt');
/*
 MY_ALLOW_EXTENSIONS
 MY_DENY_EXTENSIONS
 MY_ALLOW_EXTENSIONS and MY_DENY_EXTENSIONS arrays specify which file types can be uploaded.
 Setting to null skips this check. The scheme is:
 1) If MY_DENY_EXTENSIONS is not null check if it does _not_ contain file extension of the file to be uploaded.
    If it does skip the upload procedure.
 2) If MY_ALLOW_EXTENSIONS is not null check if it _does_ contain file extension of the file to be uploaded.
    If it doesn't skip the upload procedure.
 3) Upload file.
 NOTE: File extensions arrays are case insensitive.
        You should always include server side executable file types in MY_DENY_EXTENSIONS !!!
*/
$MY_ALLOW_EXTENSIONS = array('html', 'doc', 'xls', 'txt', 'gif', 'jpeg', 'jpg', 'png', 'pdf', 'zip', 'pdf', 'ppt');
$MY_DENY_EXTENSIONS  = array('php', 'php3', 'php4', 'phtml', 'shtml', 'cgi', 'pl');
/*
 $MY_ALLOW_UPLOAD
 Maximum allowed size for uploaded files (in bytes).
 NOTE2: see also upload_max_filesize setting in your php.ini file
 NOTE: 2*1024*1024 means 2 MB (megabytes) which is the default php.ini setting
*/
$MY_MAX_FILE_SIZE                 = 2*1024*1024;

/*
 $MY_LANG
 Interface language. See the lang directory for translation files.
 NOTE: You should set appropriately MY_CHARSET and $MY_DATETIME_FORMAT variables
*/
//$MY_LANG                = 'en';
$MY_LANG                = $_GET['lang'];

/*
 $MY_CHARSET
 Character encoding for all Insert File dialogs.
 WARNING: For non english and non iso-8859-1 / utf8 users mostly !!!
 This setting affect also how the name of folder you create via Insert File Dialog
 and the name of file uploaded via Insert File Dialog will be encoded on your remote
 server filesystem. Note also the difference between how file names in multipart/data
 form are encoded by Internet Explorer (plain text depending on the webpage charset)
 and Mozilla (encoded according to RFC 1738).
 This should be fixed in next versions. Any help is VERY appreciated.
*/
$MY_CHARSET             = 'iso-8859-1';

/*
 MY_DATETIME_FORMAT
 Datetime format for displaying file modification time in Insert File Dialog and in inserted link, see MY_LINK_FORMAT
*/
$MY_DATETIME_FORMAT                = "d/m/Y H:i";

/*
 MY_LINK_FORMAT
 The string to be inserted into textarea.
 This is the most crucial setting. I apologize for not using the DOM functions any more,
 but inserting raw string allow more customization for everyone.
 The following strings are replaced by corresponding values of selected files/folders:
 _editor_url  the url of htmlarea root folder - you should set it in your document (see htmlarea help)
 IF_ICON      file type icon filename (see plugins/InsertFile/images/ext directory)
 IF_URL       relative path to file relative to $MY_DOCUMENT_ROOT
 IF_CAPTION   file/folder name
 IF_SIZE      file size in (B, kB, or MB)
 IF_DATE      last modification time acording to $MY_DATETIME_FORMAT format
*/
//$MY_LINK_FORMAT         = '<span class="filelink"><img src="editor_url/plugins/filemanager/InsertFile/IF_ICON" alt="IF_URL" border="0">&nbsp;<a href="IF_URL">IF_CAPTION</a> &nbsp;<span style="font-size:70%">IF_SIZE &nbsp;IF_DATE</span></span>&nbsp;';
//modification par ACTi le 08/03/2006
//IF_ICON tient compte de la variable $MY_ICON_FOLDER définie ci-dessus pour générer le lien vers l'image de l'icone
$MY_LINK_FORMAT         = '<span class="filelink"><img src="IF_ICON" alt="IF_URL" border="0">&nbsp;<a href="IF_URL">IF_CAPTION</a> &nbsp;<span style="font-size:70%">IF_SIZE &nbsp;IF_DATE</span></span>&nbsp;';
//fin de modification par ACTI le 08/03/2006


/* parse_icon function  please insert additional file types (extensions) and theis corresponding icons in switch statement */
function parse_icon($ext) {
        switch (strtolower($ext)) {
                case 'doc': return 'doc_small.gif';
                case 'rtf': return 'doc_small.gif';
                case 'txt': return 'txt_small.gif';
                case 'xls': return 'xls_small.gif';
                case 'csv': return 'xls_small.gif';
                case 'ppt': return 'ppt_small.gif';
                case 'html': return 'html_small.gif';
                case 'htm': return 'html_small.gif';
                case 'php': return 'script_small.gif';
                case 'php3': return 'script_small.gif';
                case 'cgi': return 'script_small.gif';
                case 'pdf': return 'pdf_small.gif';
                case 'rar': return 'rar_small.gif';
                case 'zip': return 'zip_small.gif';
                case 'gz': return 'gz_small.gif';
                case 'jpg': return 'jpg_small.gif';
                case 'gif': return 'gif_small.gif';
                case 'png': return 'png_small.gif';
                case 'bmp': return 'image_small.gif';
                case 'exe': return 'binary_small.gif';
                case 'bin': return 'binary_small.gif';
                case 'avi': return 'mov_small.gif';
                case 'mpg': return 'mov_small.gif';
                case 'moc': return 'mov_small.gif';
                case 'asf': return 'mov_small.gif';
                case 'mp3': return 'sound_small.gif';
                case 'wav': return 'sound_small.gif';
                case 'org': return 'sound_small.gif';
        default:
                return 'def_small.gif';
        }
}

// DO NOT EDIT BELOW
$MY_NAME = 'insertfiledialog';
$lang_file = 'lang/lang-'.$MY_LANG.'.php';
if (is_file($lang_file)) require($lang_file);
else require('lang/lang-en.php');
$MY_PATH = '/';
$MY_UP_PATH = '/';

?>