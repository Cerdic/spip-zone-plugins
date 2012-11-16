/**
 * SPIP TinyMCE plugin 
 * - Retour à la barre du Porte-Plume
 * - Protection des codes d'inclusion de modeles et balises dans une SPAN speciale
 *
 * @version 1.3
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
					must_be_protected : t.mustBeProtected( ed.selection.getNode() )
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
		// un noeud doit-il protege ?
		// s'il l'est deja, on regarde si le noeud parent ne contient que la span,
		// sinon on doit le re-proteger
		mustBeProtected: function( n ){
			var _isp = this.isProtected(n);
			if (_isp===true){
				var t = this, blockparent=false;
				tinyMCE.each(this.editor.dom.getParents(n), function(parent) {
					if (t.editor.dom.isBlock(parent) && blockparent===false) blockparent = parent;
				});
				if (blockparent)
					return (t.getContenuText(blockparent.innerHTML) != t.getContenuText(n.innerHTML));
			}
			return !_isp;
		},
		// on encadre si necessaire par une div
		selectionnerContenuNode: function( node ){ 
			this.editor.selection.select( this.editor.dom.get(node), true );
		},
		// creation de la DIV avec la classe pour proteger les contenus SPIP
		protegerContenuText: function( str ){ 
				return '<span class="'+this.getSpecialClass()+'">'+this.getContenuText(str)+'</span>';
		},
		// creation de la DIV avec la classe pour proteger les contenus SPIP
		getContenuText: function( str ){ 
				return $('<div/>').html(str).text();
		}
	});
	// enregistrement du plugin
	tinymce.PluginManager.add('spip', tinymce.plugins.SpipSpecialPlugin);
})();
