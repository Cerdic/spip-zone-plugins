//fichier contenant la configuration de TinyMCE
// tinyMCE
	tinyMCE.init({
		
		filemanager_docs_relative_path : '/documents', 	//par défaut '/documents' ; normalement, rien à modifier ici
		filemanager_base_url : '/', 					//par défaut '/' ; normalement "/", parfois "/recette/"
		//filemanager_base_path : 'C:/Spip-zone/mydev/', 	//par défaut '../../../../../../../../' soit la racine du site => normalement laisser en commentaire
		ibrowser_library : '/images/librairie/', 		//par défaut "/images/librairie/" ; normalement "/images/librairie/", parfois "/recette/images/librairie/"
		
		document_base_url : "/", 						//normalement "/", parfois "/recette/"
		
		
		convert_urls : true,
		relative_urls : true,
 		remove_script_host : true,
 		
		mode : "specific_textareas", // à laisser car utiliser par le pipeline pour intégrer le miniword
		editor_selector : "mceEditor", // à laisser car utiliser par le pipeline pour intégrer le miniword
		theme : "advanced",
		language : "fr",
		accessibility_warnings : true,
		content_css : "../plugins/tinymce_1_9_1_et_plus/config/tiny_mce_style.css", //ATENTION, le chemin relatif part de /ecrire/ !!
		width : "600",
		plugins : "table,advlink,insertdatetime,preview,searchreplace,print,contextmenu,paste,directionality,fullscreen,style,layer,ibrowser,filemanager",
		paste_create_paragraphs : false,
		//theme_advanced_buttons1_add_before : "newdocument,separator",
		theme_advanced_buttons1_add : "fontsizeselect",
		//theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor,styleprops",
		theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
		theme_advanced_buttons3_add_before : "tablecontrols,separator",
		theme_advanced_buttons3_add : "separator,ibrowser, filemanager",
//		theme_advanced_buttons3_add : "separator,print,separator,ltr,rtl,separator,fullscreen",
		//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,separator,ibrowser,filemanager",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_path_location : "bottom",
		convert_fonts_to_spans : true,
		trim_span_elements : true,
		cleanup : true,
		cleanup_on_startup : true,
		remove_linebreaks : false,
//		urlconvertor_callback: "convLinkVC", 
		/*extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",*/
		external_link_list_url : "example_link_list.js",
		external_image_list_url : "example_image_list.js",
		flash_external_list_url : "example_flash_list.js",
		file_browser_callback : "fileBrowserCallBack",
		cleanup_callback : "myCustomCleanup",
		
		valid_elements : ""
						+"a[accesskey|charset|class|coords|dir<ltr?rtl|href|hreflang|id|lang|name"
						  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rel|rev"
						  +"|shape<circle?default?poly?rect|style|tabindex|title=|target|type],"
						+"abbr[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"acronym[class|dir<ltr?rtl|id|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"address[class|align|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|style|title],"
						+"applet[align<bottom?left?middle?right?top|alt|archive|class|code|codebase"
						  +"|height|hspace|id|name|object|style|title|vspace|width],"
						+"area[accesskey|alt|class|coords|dir<ltr?rtl|href|id|lang|nohref<nohref"
						  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup"
						  +"|shape<circle?default?poly?rect|style|tabindex|title|target],"
						+"base[href|target],"
						+"basefont[color|face|id|size],"
						+"bdo[class|dir<ltr?rtl|id|lang|style|title],"
						+"big[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"blockquote[dir|style|cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
						  +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
						  +"|onmouseover|onmouseup|style|title],"
						+"body[alink|background|bgcolor|class|dir<ltr?rtl|id|lang|link|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|onunload|style|title|text|vlink],"
						+"br[class|clear<all?left?none?right|id|style|title],"
						+"button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur"
						  +"|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown"
						  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|tabindex|title|type"
						  +"|value],"
						+"caption[align<bottom?left?right?top|class|dir<ltr?rtl|id|lang|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"center[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"cite[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"code[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"col[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
						  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
						  +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
						  +"|valign<baseline?bottom?middle?top|width],"
						+"colgroup[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl"
						  +"|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
						  +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
						  +"|valign<baseline?bottom?middle?top|width],"
						+"dd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
						+"del[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|style|title],"
						+"dfn[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"dir[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|style|title],"
						+"div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"dl[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|style|title],"
						+"dt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
						+"em/i[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"fieldset[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"font[class|color|dir<ltr?rtl|face|id|lang|size|style|title],"
						+"form[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang"
						  +"|method<get?post|name|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onsubmit"
						  +"|style|title|target],"
						+"frame[class|frameborder|id|longdesc|marginheight|marginwidth|name"
						  +"|noresize<noresize|scrolling<auto?no?yes|src|style|title],"
						+"frameset[class|cols|id|onload|onunload|rows|style|title],"
						+"h1[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"h2[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"h3[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"h4[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"h5[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"h6[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"head[dir<ltr?rtl|lang|profile],"
						+"hr[align<center?left?right|class|dir<ltr?rtl|id|lang|noshade<noshade|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|size|style|title|width],"
						+"html[dir<ltr?rtl|lang|version],"
						+"iframe[align<bottom?left?middle?right?top|class|frameborder|height|id"
						  +"|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style"
						  +"|title|width],"
						+"img[align<bottom?left?middle?right?top|alt=|border|class|dir<ltr?rtl|height"
						  +"|hspace|id|ismap<ismap|lang|longdesc|name|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|src|style|title|usemap|vspace|width],"
						+"input[accept|accesskey|align<bottom?left?middle?right?top|alt"
						  +"|checked<checked|class|dir<ltr?rtl|disabled<disabled|id|ismap<ismap|lang"
						  +"|maxlength|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
						  +"|readonly<readonly|size|src|style|tabindex|title"
						  +"|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text"
						  +"|usemap|value],"
						+"ins[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|style|title],"
						+"isindex[class|dir<ltr?rtl|id|lang|prompt|style|title],"
						+"kbd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"label[accesskey|class|dir<ltr?rtl|for|id|lang|onblur|onclick|ondblclick"
						  +"|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
						  +"|onmouseover|onmouseup|style|title],"
						+"legend[align<bottom?left?right?top|accesskey|class|dir<ltr?rtl|id|lang"
						  +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"li[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title|type"
						  +"|value],"
						+"link[charset|class|dir<ltr?rtl|href|hreflang|id|lang|media|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|rel|rev|style|title|target|type],"
						+"map[class|dir<ltr?rtl|id|lang|name|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"menu[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|style|title],"
						+"meta[content|dir<ltr?rtl|http-equiv|lang|name|scheme],"
						+"noframes[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"noscript[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"object[align<bottom?left?middle?right?top|archive|border|class|classid"
						  +"|codebase|codetype|data|declare|dir<ltr?rtl|height|hspace|id|lang|name"
						  +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|standby|style|tabindex|title|type|usemap"
						  +"|vspace|width],"
						+"ol[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|start|style|title|type],"
						+"optgroup[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick|ondblclick"
						  +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
						  +"|onmouseover|onmouseup|selected<selected|style|title|value],"
						+"p[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|style|title],"
						+"param[id|name|type|value|valuetype<DATA?OBJECT?REF],"
						+"pre/listing/plaintext/xmp[align|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
						  +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
						  +"|onmouseover|onmouseup|style|title|width],"
						+"q[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"s[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
						+"samp[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"script[charset|defer|language|src|type],"
						+"select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name"
						  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|size|style"
						  +"|tabindex|title],"
						+"small[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"span[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|style|title],"
						+"strike[class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|style|title],"
						+"strong/b[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"style[dir<ltr?rtl|lang|media|title|type],"
						+"sub[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"sup[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title],"
						+"table[align<center?left?right|background|bgcolor|border|cellpadding|cellspacing|class"
						  +"|dir<ltr?rtl|frame|height|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rules"
						  +"|style|summary|title|width],"
						+"tbody[align<center?char?justify?left?right|background|char|class|charoff|dir<ltr?rtl|id"
						  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
						  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
						  +"|valign<baseline?bottom?middle?top],"
						+"td[abbr|align<center?char?justify?left?right|axis|background|bgcolor|char|charoff|class"
						  +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
						  +"|style|title|valign<baseline?bottom?middle?top|width],"
						+"textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name"
						  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
						  +"|readonly<readonly|rows|style|tabindex|title],"
						+"tfoot[align<center?char?justify?left?right|background|char|charoff|class|dir<ltr?rtl|id"
						  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
						  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
						  +"|valign<baseline?bottom?middle?top],"
						+"th[abbr|align<center?char?justify?left?right|axis|background|bgcolor|char|charoff|class"
						  +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
						  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
						  +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
						  +"|style|title|valign<baseline?bottom?middle?top|width],"
						+"thead[align<center?char?justify?left?right|background|char|charoff|class|dir<ltr?rtl|id"
						  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
						  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
						  +"|valign<baseline?bottom?middle?top],"
						+"title[dir<ltr?rtl|lang],"
						+"tr[abbr|align<center?char?justify?left?right|background|bgcolor|char|charoff|class"
						  +"|rowspan|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title|valign<baseline?bottom?middle?top],"
						+"tt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
						+"u[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
						  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
						+"ul[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
						  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
						  +"|onmouseup|style|title|type],"
						+"var[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
						  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
						  +"|title]"
	});

	function fileBrowserCallBack(field_name, url, type) {
		// This is where you insert your custom filebrowser logic
		alert("Filebrowser callback: " + field_name + "," + url + "," + type);
	}
	function convLinkVC(strUrl, node, on_save) { 
            strUrl=strUrl.replace("../",""); 
            return strUrl; 
    } 
	function myCustomCleanup(type, value) {
		var foo = new String (value);
		switch (type) {
			case "get_from_editor":
				//remplace le code des images et documents pour que la balise ne soit pas interprétée par TinyMCE :
				//ex. : remplace un texte du genre "&lt;IMG97|left&gt;" par "<IMG97|left>"
				foo=foo.replace(/\&amp\;lt\;((img)|(doc)|(emb))([0-9]+)((\|left)|(\|center)|(\|right))?\&amp\;gt\;/gi, "<$1$5$6>");
				foo=foo.replace(/\&lt\;((img)|(doc)|(emb))([0-9]+)((\|left)|(\|center)|(\|right))?\&gt\;/gi, "<$1$5$6>");
				foo=foo.replace(/\&lt\;(form)([0-9]+)?\&gt\;/gi, "<$1$2>");
				foo=foo.replace(/&lt\;(acti_framework)([^>]*)&gt\;/g, "<$1$2>");
				foo=foo.replace(/\[([^\]]*)-&gt;([^\]]*)]/g, "[$1->$2]");
				break;
			case "insert_to_editor":
				//remplace le code des images et documents pour que la balise ne soit pas interprétée par TinyMCE :
				//ex. : remplace un texte du genre "<IMG97|left>" par "&lt;IMG97|left&gt;"
				foo=foo.replace(/\<((img)|(doc)|(emb))([0-9]+)((\|left)|(\|center)|(\|right))?\>/gi, "&lt;$1$5$6&gt;");
				foo=foo.replace(/\<(form)([0-9]+)?\>/gi, "&lt;$1$2&gt;");
				foo=foo.replace(/<(acti_framework)([^>]*)>/g, "&lt;$1$2&gt;");
				foo=foo.replace(/\[([^\]]*)-\>([^\]]*)]/g, "[$1-&gt;$2]");
				break;
			case "get_from_editor_dom":
				//remplace le code des images et documents pour que la balise ne soit pas interprétée par TinyMCE :
				//ex. : remplace un texte du genre "&lt;IMG97|left&gt;" par "<IMG97|left>"
				foo=foo.replace(/\&amp\;lt\;((img)|(doc)|(emb))([0-9]+)((\|left)|(\|center)|(\|right))?\&amp\;gt\;/gi, "<$1$5$6>");
				foo=foo.replace(/\&lt\;((img)|(doc)|(emb))([0-9]+)((\|left)|(\|center)|(\|right))?\&gt\;/gi, "<$1$5$6>");
				foo=foo.replace(/\&lt\;(form)([0-9]+)?\&gt\;/g, "<$1$2>");
				foo=foo.replace(/&lt\;(acti_framework)([^>]*)&gt\;/g, "<$1$2>");
				foo=foo.replace(/-&gt;/g, "[$1->$2]");
				break;
			case "insert_to_editor_dom":
				//remplace le code des images et documents pour que la balise ne soit pas interprétée par TinyMCE :
				//ex. : remplace un texte du genre "<IMG97|left>" de spip par "&lt;IMG97|left&gt;"
				foo=foo.replace(/\<((img)|(doc)|(emb))([0-9]+)((\|left)|(\|center)|(\|right))?\>/gi, "&lt;$1$5$6&gt;");
				foo=foo.replace(/\<(form)([0-9]+)?\>/gi, "&lt;$1$2&gt;");
				foo=foo.replace(/<(acti_framework)([^>]*)>/g, "&lt;$1$2&gt;");
				foo=foo.replace(/\[([^\]]*)-\>([^\]]*)]/g, "[$1-&gt;$2]");
				break;
			case "submit_content":
				//remplace le code des images et documents pour que la balise ne soit pas interprétée par TinyMCE :
				//ex. : remplace un texte du genre "&lt;IMG97|left&gt;" par "<IMG97|left>"
				foo=foo.replace(/\&amp\;lt\;((img)|(doc)|(emb))([0-9]+)((\|left)|(\|center)|(\|right))?\&amp\;gt\;/gi, "<$1$5$6>");
				foo=foo.replace(/\&lt\;((img)|(doc)|(emb))([0-9]+)((\|left)|(\|center)|(\|right))?\&gt\;/gi, "<$1$5$6>");
				foo=foo.replace(/\&lt\;(form)([0-9]+)?\&gt\;/gi, "<$1$2>");
				foo=foo.replace(/&lt\;(acti_framework)([^>]*)&gt\;/g, "<$1$2>");
				foo=foo.replace(/\[([^\]]*)-&gt;([^\]]*)]/g, "[$1->$2]");
				break;
		}
		value = foo;
		return value;
	}
