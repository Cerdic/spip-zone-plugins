/* Import theme specific language pack */
//tinyMCE.importPluginLanguagePack('filemanager', 'en');
tinyMCE.importPluginLanguagePack('filemanager','en,fr');

function TinyMCE_filemanager_getControlHTML(control_name) {
    switch (control_name) {
        case "filemanager":
            return tinyMCE.getButtonHTML(control_name, 'lang_filemanager_desc', '{$pluginurl}/images/filemanager.gif', 'mceFilemanager', false);
    }
    return "";
}

/**
 * Executes the mceFilemanager command.
 */
function TinyMCE_filemanager_execCommand(editor_id, element, command, user_interface, value) {
    // Handle commands
    switch (command) {
        case "mceFilemanager":
            var template = new Array();
            template['file'] = '../../plugins/filemanager/InsertFile/insert_file.php'; // Relative to theme
            //ACTi : ajout pour configuration via tinyMCE et non directement dans le plugin
            template['file'] += '?base_url=' + ((tinyMCE.getParam('filemanager_base_url',undefined)!=undefined)?tinyMCE.getParam('filemanager_base_url'):'/');
            template['file'] += '&base_path=' + ((tinyMCE.getParam('filemanager_base_path',undefined)!=undefined)?tinyMCE.getParam('filemanager_base_path'):'../../../../../../../../'); //six "../" pour retourner à la racine du site (en partant de /plugins/tinymce/tiny_mce/plugins/filemanager/InsertFile/)
            template['file'] += '&docs=' + ((tinyMCE.getParam('filemanager_docs_relative_path',undefined)!=undefined)?tinyMCE.getParam('filemanager_docs_relative_path'):'/documents');
            template['file'] += '&lang=' + ((tinyMCE.getParam('language',undefined)!=undefined)?tinyMCE.getParam('language'):'en');
            //ACTi : fin de ajout
            template['width']  = 660;
            template['height'] = 500;

            tinyMCE.openWindow(template, {editor_id : editor_id});
       return true;
   }
   // Pass to next handler in chain
   return false;
}


