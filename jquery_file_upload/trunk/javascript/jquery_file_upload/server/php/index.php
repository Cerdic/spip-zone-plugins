<?php
/*
 * jQuery File Upload Plugin PHP Example 5.7
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);

require('upload.class.php');

$upload_handler = new UploadHandler(array(

  // SECURITY NOTICE:
  // Only change the accept_file_types setting after making sure that any
  // allowed file types cannot be executed by the webserver in the files
  // directory (e.g. PHP scripts), nor executed by the browser when downloaded
  // (e.g. HTML files with embedded JavaScript code).
  // e.g. in Apache, make sure the provided .htaccess file is present in the
  // files directory and .htaccess support has been enabled:
  // https://httpd.apache.org/docs/current/howto/htaccess.html

  // By default, only allow file uploads with image file extensions:
  'accept_file_types' => '/\.(gif|jpe?g|png)$/i'
));

header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'OPTIONS':
        break;
    case 'HEAD':
    case 'GET':
        $upload_handler->get();
        break;
    case 'POST':
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            $upload_handler->delete();
        } else {
            $upload_handler->post();
        }
        break;
    case 'DELETE':
        $upload_handler->delete();
        break;
    default:
        header('HTTP/1.1 405 Method Not Allowed');
}
