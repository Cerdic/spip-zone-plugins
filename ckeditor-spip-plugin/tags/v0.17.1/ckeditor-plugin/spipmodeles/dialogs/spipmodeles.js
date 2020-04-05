function paramTypeChange(paramType, tag, valSelect, valInput, tags) {
	var param = paramType.getValue() ;
	var result = '' ;
	for(var ftag in tags) {
		if (tag == tags[ftag][0]) {
			if (tags[ftag][3][param] && (tags[ftag][3][param].length > 1)) {
				valSelect.clear() ;
				for(var fval in tags[ftag][3][param]) {
					valSelect.add(tags[ftag][3][param][fval].replace(/^.*\|/, ''), tags[ftag][3][param][fval].replace(/\|.*$/, '')) ;
				}
				valSelect.getElement().show() ;
				valInput.getElement().hide() ;
			} else if (tags[ftag][3][param] && (tags[ftag][3][param].length == 1)) {
				result = tags[ftag][3][param][0].replace(/\|.*$/, '') ;
				valInput.getElement().hide() ;
				valSelect.getElement().hide() ;
			} else {
				valInput.getElement().show() ;
				valSelect.getElement().hide() ;
			}
		}
	}
	return result ;
}

function count(tab) {
	var c = 0 ;
	for(var item in tab) {
		c++;
	}
	return c ;
}

CKEDITOR.dialog.add('spipmodelesDlg', function(editor){
    var lang, m, tag, tags, tagtype, value, valDocId, valSelect, valInput, paramType, paramLigne, paramHidden, selectDocId, val_start, val_end, separateur ;
	lang = editor.lang.spipmodeles ;
    return {
		title:lang.label,
		minWidth:400,
		minHeight:200,
		onLoad: function() {
			valSelect = this.getContentElement('tab1', 'paramvalSelect') ;
			valInput = this.getContentElement('tab1', 'paramvalInput') ;
			paramType = this.getContentElement('tab1', 'paramType') ;
			paramLigne = this.getContentElement('tab1', 'paramLigne') ;
			valDocId = this.getContentElement('tab1', 'docId' ) ;
			selectDocId = this.getContentElement('tab1', 'selectDocId' ) ;
			tags = this.tags ;

			valSelect.getElement().getParent().setAttribute('style','padding:0;margin:0;width:auto;') ;
			valInput.getElement().getParent().setAttribute('style','padding:0;margin:0;width:auto;') ;
		},
		onShow: function() {
			value = tag = this.tag_requested ;
			for(var ftag in this.tags) {
				if (value == this.tags[ftag][0]) { // on trouve le tag
					if (this.tags[ftag][7]=='html'){
						val_start='"';val_end='"';separateur=' ';
					} else{
						val_start='';val_end='';separateur='|';
					}
					this.getContentElement('tab1', 'docId').setLabel(lang.promptId.replace(/%id%/, this.tags[ftag][1]?this.tags[ftag][1]:'<'+tag+'>')) ;
					tagtype = this.tags[ftag][5] ;
					if (tagtype == 'num-aucun') {
						this.getContentElement('tab1', 'numPanel').getElement().hide() ;
					} else {
						this.getContentElement('tab1', 'numPanel').getElement().show() ;
					}
					paramType.clear();
					for(var param in this.tags[ftag][3]) {
						paramType.add(param.replace(/\|.*$/, '') , param ) ;
					}
					if(count(this.tags[ftag][3]) == 0) {
						this.getContentElement('tab1', 'paramSet').getElement().hide() ;
					} else {
						this.getContentElement('tab1', 'paramSet').getElement().show() ;
					}
					paramHidden = paramTypeChange(paramType, value, valSelect, valInput, tags) ;
					if ( this.tags[ftag][4] == 'thisobject' ) { // on demande l'id de l'objet en cours d'édition
						selectDocId.getElement().hide() ;
						valDocId.setValue ( editor.config.spip_contexte.id ) ;
					} else if ( this.tags[ftag][4] ) { // on demande une liste
						var liste ;
						try {
							var json = $.ajax({
								url: CKEDITOR.spipurl + '?doctype=' + this.tags[ftag][4] + '&page=spiplistes-json', 
								async: false, // pour avoir une réponse synchrone
								global: false,
								dataType: 'json'
							}).responseText ;
							liste = $.parseJSON(json) ;
						} catch(e) {
							alert(lang.page_introuvable + CKEDITOR.spipurl + '?doctype=' + this.tags[ftag][4] + '&page=spiplistes-json' ) ;
							liste = [] ;
						}
						selectDocId =  this.getContentElement('tab1', 'selectDocId') ;
						selectDocId.clear() ;
						for(var id in liste) {
							selectDocId.add(liste[id], id) ;
						}
						selectDocId.getElement().show() ;
						valDocId.setValue( selectDocId.getValue() ) ;
					} else {
						selectDocId.getElement().hide() ;
						valDocId.setValue( '' ) ;
					}
				}
			}

		},
		onOk: function() {
			var id = this.getValueOf('tab1','docId');
			if ((!id && (!tagtype || tagtype.match(/^num-(facultatif|aucun)$/))) || id.match(/\d+/)) {
				value = tag + (id?id:'') ; // si XX alors il *faut* un id numerique; sinon l'id est facultatif
				insertTag(editor, value, tag, paramLigne.getValue(), this.tags) ;
				return true ;
			} else {
				alert(lang.erreur_nombre_obligatoire) ;
				return false ;
			}
		},
		contents: [
			{
				id: 'tab1',
				label: lang.parametres_des_modeles,
				title: lang.parametres_des_modeles,
				elements: [
					{
						id:'panel1',
						type:'vbox',
						children: [
							{
								type:'hbox',
								id:'numPanel',
								children: [
									{
										type:'text',
										label:'',
										width:'80px',
										id:'docId',
										labelLayout:'horizontal'
									},
									{
										type:'select',
										width:'80px',
										id:'selectDocId',
										items: [],
										onChange: function() { valDocId.setValue( selectDocId.getValue() ) ; }
									}
								]	
							},
							{
								type:'fieldset',
								id:'paramSet',
								label:lang.parametres,
								children: [
									{
										type:'vbox',
										children: [	
											{
												type:'hbox',
												children: [
													{
														type:'select',
														id:'paramType',
														label:lang.parametre,
														items: [],
														onChange: function() { 
															paramHidden = paramTypeChange(paramType, value, valSelect, valInput, tags) ;
														}
													},
													{
														type:'select',
														id:'paramvalSelect',
														label:lang.valeur,
														items: []
													},
													{
														type:'text',
														id:'paramvalInput',
														label:lang.valeur
													}
												]
											},
											{ 
												type:'button',
												id:'addparam',
												label:lang.ajoute_parametre,
												onClick: function()  {
													var type = paramType.getValue() ;
													var valeur = (valSelect.isVisible() ? 
															valSelect.getValue() : 
															( valInput.isVisible() ? 
																valInput.getValue() : 
																paramHidden 
															)
														) ; 
													if (type.match(/\|/)) {
														type = type.replace(/^.*\|/, '') ;
													} else {
														type = '' ;
													}
													var oldvalue = paramLigne.getValue() ;
													paramLigne.setValue( (oldvalue ? oldvalue+separateur : '') + type + ( type && valeur ? '=' : '' ) + val_start + valeur + val_end ) ;  
												}
											}
										]
									}
								]
							},
							{
								type:'text',
								id:'paramLigne',
								label:lang.parametres
							}
						]
					}
				]
			}
		]
	}
});
