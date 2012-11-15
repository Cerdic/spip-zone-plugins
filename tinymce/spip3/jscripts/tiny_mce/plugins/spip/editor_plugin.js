/**
 * SPIP TinyMCE plugin 
 * - Retour à la barre du Porte-Plume
 * - Protection des codes d'inclusion de modeles et balises dans une SPAN speciale
 *
 * @version 1.2
 */

(function() {
	// charger les chaines de langue
	tinymce.PluginManager.requireLangPack('spip');
	// code du plugin
	tinymce.create('tinymce.plugins.SpipSpecialPlugin', {
		//initialisation
		init: function(ed,url){
			var t = this;
			t.url = url;
			t.editor = ed;

			// bouton de retour a la barre du porte-plume
			ed.addCommand('spip_back_barre', function(){ document.location.search += '&tmce_barre='+t.getArgBarre(); });
			ed.addButton('spip',{ title: 'spip.desc', cmd: 'spip_back_barre', image: url+'/img/spip-porteplume.gif' });

			// bouton de la popup de code SPIP
			ed.addCommand('spip_insert_code', function() {
				ed.windowManager.open({
					file : url + '/dialog.htm',
					width : 480 + parseInt(ed.getLang('spip.delta_width', 0)),
					height : 320 + parseInt(ed.getLang('spip.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : t.url,
					special_class : t.getSpecialClass(),
					special_class_regexp : t.getSpecialClassRegexp(),
					must_be_protected : !t.isProtected( ed.selection.getNode() )
				});
			});
			ed.addButton('spipinsert',{ title: 'spip.desc_insert', cmd: 'spip_insert_code', image: url+'/img/spip-code.gif' });

			// on protege les divs avec la classe en entree
			ed.onSetContent.add(function(ed, o) {
				o.content = o.content.replace( t.getSpecialClassRegexp(), function(m){ 
					return t.protegerContenuText(m); 
				});
			});

			// on protege les divs avec la classe en sortie
			ed.onPostProcess.add(function(ed, o) {
				o.content = o.content.replace( t.getSpecialClassRegexp(), function(m){ 
					return t.protegerContenuText(m); 
				});
			});

			// on selectionne tout le contenu si double-clique sur une div avec la classe
			ed.onDblClick.add(function(ed, e) {
				if ( t.isProtected(e.target) ){
					t.selectionnerContenuNode( e.target );
					t.editor.execCommand('spip_insert_code');
				}
			});

		},
		// getter : special class
		getSpecialClass: function(){
			var ed = this.editor;
			return ed.getParam("plugin_spip_special_class", "spiptmceInsert");
		},
		// getter : special class RegExp
		getSpecialClassRegexp: function(){
			var _str = '<span class="'+this.getSpecialClass()+'">(.*)</span>',
				_patrn = new RegExp(_str.replace('/', '\/'), 'gim');
			return _patrn;
		},
		// getter : argument pour retour a la barre du porte-plume
		getArgBarre: function(){
			var ed = this.editor;
			return ed.getParam("plugin_spip_arg_barre", "porteplume");
		},
		// un noeud est-il protege ?
		isProtected: function( n ){
			return (n && n.nodeName=='SPAN' && n.className==this.getSpecialClass());
		},
		// on encadre si necessaire par une div
		selectionnerContenuNode: function( node ){ 
			this.editor.selection.select( this.editor.dom.get(node), true );
		},
		// creation de la DIV avec la classe pour proteger les contenus SPIP
		protegerContenuText: function( str ){ 
				return '<span class="'+this.getSpecialClass()+'">'+$('<div/>').html(str).text()+'</span>';
		}
	});
	// enregistrement du plugin
	tinymce.PluginManager.add('spip', tinymce.plugins.SpipSpecialPlugin);
})();

/*
// Differents essais pour proteger les selections ... sans succes
			ed.addCommand('spip_pre_insert_code', function() {
				var _node = ed.selection.getNode();
				if ( t.isProtected(_node) ) {
console.debug('=> cas d un noeud deja protege');
					ed.selection.select(ed.dom.get(_node), true);
				} else {
console.debug('=> cas d un noeud non protege');


					var _node_ctt = _node.innerHTML.replace('<br data-mce-bogus="1">', '');
					// cas ou la selection est un noeud complet ou vide
					if (ed.selection.getContent()==_node_ctt){
console.debug('=> cas d un noeud complet ou vide, on entoure de la div');
//						new_node = _node.cloneNode(true);
						var new_node = ed.dom.create('div', {'class': t.getSpecialClass()}, ed.selection.getContent());
//						ed.dom.removeAllAttribs(new_node);
//						ed.dom.setAttrib(new_node, 'class', t.getSpecialClass());
//						ed.dom.rename(new_node, 'div');
						ed.dom.replace(_node, new_node);
						ed.selection.select( new_node.firstChild() );
					// sinon, on cree une div
					} else {
console.debug('=> cas d un noeud existant, on entoure la selection de la div');
						ed.selection.setContent( t.protegerContenuText(ed.selection.getContent()) );
					}

					ed.execCommand('mceInsertContent', false, t.protegerContenuText(ed.selection.getContent({format : 'text'})));
					ed.selection.select(ed.dom.get(_node).firstChild('div'), true);
				}
				ed.execCommand('spip_insert_code');
			});
//			ed.addButton('spipinsert',{ title: 'spip.desc_insert', cmd: 'spip_pre_insert_code', image: url+'/img/spip-code.gif' });
*/