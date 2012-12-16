/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/


(function()
{
	// Load image preview.
	var IMAGE = 1,
		PREVIEW = 4,
		CLEANUP = 8,
		regexGetSize = /^\s*(\d+)((px)|\%)?\s*$/i,
		regexGetSizeOrEmpty = /(^\s*(\d+)((px)|\%)?\s*$)|^$/i,
		regexGetSpipDoc = /^(.*)\?docid=(\d+)(?:(&amp;|&)doctype=(\w+))?((&amp;|&)docparam=(.*))?$/,
		urlUpdating = false ;

	var updateUrl = function(item, editor, caller) {
		if (!urlUpdating) {
			urlUpdating = true ;
			var dialog = item.getDialog() ;

			var docId = dialog.getValueOf('info', 'docId'),
				docType = dialog.getValueOf('info', 'docType'),
				docParam = dialog.getValueOf('info', 'docParam'),
				url = dialog.getValueOf('info', 'txtUrl'),
				doctypeRegex = /^.*\/([^-]+).*?\.(png|gif|jpe?g)$/ ;
				
			if (url) {
				var docMatch = url.match( regexGetSpipDoc ) ;
				if (docMatch) {
					var m = docMatch[1].match( doctypeRegex ) ;
					if ( m && ( (m[1].toLowerCase() == 'mp3') || (m[1].toLowerCase() == 'flv') ) && 
							(!docParam.match( /^(.*\|)?player(\|.*)?$/ )) ) {
						if ((caller == 'txtUrl') && confirm( editor.lang.spipdoc.multimedia_content_add_player ) ) {
							dialog.setValueOf('info','docType', 'doc') ;
							docType = 'doc'; // ne pas tenir compte du docType, on souhaite un player, donc un 'doc'
							if (docParam) {
								docParam = 'player|'+docParam ;
							} else { 
								docParam = 'player' ;
							}
							dialog.setValueOf('info', 'docParam', docParam) ;
						}
					}
					if (docMatch[7] == undefined) { docMatch[7] = '' ; }
					var mustUpdate = (docId != docMatch[2]) || (docType != docMatch[4]) || (docParam != docMatch[7]) ;
					if (true) {
						var newUrl = docMatch[1]+"?docid="+docId+"&doctype="+docType ;
						if (docParam) {
							newUrl = newUrl+"&docparam="+encodeURI(docParam) ;
						} 
						dialog.setValueOf('info', 'txtUrl', newUrl) ;
					}
				}
			}
			urlUpdating = false ;
		}
		return true ;
	}
	var onSizeChange = function()
	{
		var value = this.getValue(),	// This = input element.
			dialog = this.getDialog(),
			aMatch  =  value.match( regexGetSize );	// Check value
		if ( aMatch )
		{
			if ( aMatch[2] == '%' )			// % is allowed - > unlock ratio.
				switchLockRatio( dialog, false );	// Unlock.
			value = aMatch[1];
		}

		// Only if ratio is locked
		if ( dialog.lockRatio )
		{
			var oImageOriginal = dialog.originalElement;
			if ( oImageOriginal.getCustomData( 'isReady' ) == 'true' )
			{
				if ( this.id == 'txtHeight' )
				{
					if ( value && value != '0' )
						value = Math.round( oImageOriginal.$.width * ( value  / oImageOriginal.$.height ) );
					if ( !isNaN( value ) )
						dialog.setValueOf( 'info', 'txtWidth', value );
				}
				else		//this.id = txtWidth.
				{
					if ( value && value != '0' )
						value = Math.round( oImageOriginal.$.height * ( value  / oImageOriginal.$.width ) );
					if ( !isNaN( value ) )
						dialog.setValueOf( 'info', 'txtHeight', value );
				}
			}
		}
		updatePreview( dialog );
	};

	var updatePreview = function( dialog )
	{
		//Don't load before onShow.
		if ( !dialog.originalElement || !dialog.preview )
			return 1;

		// Read attributes and update imagePreview;
		dialog.commitContent( PREVIEW, dialog.preview );
		return 0;
	};

	var switchLockRatio = function( dialog, value )
	{
		var oImageOriginal = dialog.originalElement,
			ratioButton = CKEDITOR.document.getById( 'btnLockSizes' );

		if ( oImageOriginal.getCustomData( 'isReady' ) == 'true' )
		{
			if ( value == 'check' )			// Check image ratio and original image ratio.
			{
				var width = dialog.getValueOf( 'info', 'txtWidth' ),
					height = dialog.getValueOf( 'info', 'txtHeight' ),
					originalRatio = oImageOriginal.$.width * 1000 / oImageOriginal.$.height,
					thisRatio = width * 1000 / height;
				dialog.lockRatio  = false;		// Default: unlock ratio

				if ( !width && !height )
					dialog.lockRatio = true;
				else if ( !isNaN( originalRatio ) && !isNaN( thisRatio ) )
				{
					if ( Math.round( originalRatio ) == Math.round( thisRatio ) )
						dialog.lockRatio = true;
				}
			}
			else if ( value != undefined )
				dialog.lockRatio = value;
			else
				dialog.lockRatio = !dialog.lockRatio;
		}
		else if ( value != 'check' )		// I can't lock ratio if ratio is unknown.
			dialog.lockRatio = false;

		if ( dialog.lockRatio )
			ratioButton.removeClass( 'cke_btn_unlocked' );
		else
			ratioButton.addClass( 'cke_btn_unlocked' );

		return dialog.lockRatio;
	};

	var resetSize = function( dialog )
	{
		var oImageOriginal = dialog.originalElement;
		if ( oImageOriginal.getCustomData( 'isReady' ) == 'true' )
		{
			dialog.setValueOf( 'info', 'txtWidth', oImageOriginal.$.width );
			dialog.setValueOf( 'info', 'txtHeight', oImageOriginal.$.height );
		}
		updatePreview( dialog );
	};

	var setupDimension = function( type, element )
	{
		if ( type != IMAGE )
			return;

		function checkDimension( size, defaultValue )
		{
			var aMatch  =  size.match( regexGetSize );
			if ( aMatch )
			{
				if ( aMatch[2] == '%' )				// % is allowed.
				{
					aMatch[1] += '%';
					switchLockRatio( dialog, false );	// Unlock ratio
				}
				return aMatch[1];
			}
			return defaultValue;
		}

		var dialog = this.getDialog(),
			value = '',
			dimension = (( this.id == 'txtWidth' )? 'width' : 'height' ),
			size = element.getAttribute( dimension );

		if ( size )
			value = checkDimension( size, value );
		value = checkDimension( element.$.style[ dimension ], value );

		this.setValue( value );
	};

	var spipdocDialog = function( editor, dialogType )
	{
		var onImgLoadEvent = function()
		{
			// Image is ready.
			var original = this.originalElement;
			original.setCustomData( 'isReady', 'true' );
			original.removeListener( 'load', onImgLoadEvent );
			original.removeListener( 'error', onImgLoadErrorEvent );
			original.removeListener( 'abort', onImgLoadErrorEvent );

			// Hide loader
			CKEDITOR.document.getById( 'ImagePreviewLoader' ).setStyle( 'display', 'none' );

			// New image -> new domensions
			if ( !this.dontResetSize )
				resetSize( this );

			if ( this.firstLoad )
				switchLockRatio( this, 'check' );
			this.firstLoad = false;
			this.dontResetSize = false;
		};

		var onImgLoadErrorEvent = function()
		{
			// Error. Image is not loaded.
			var original = this.originalElement;
			original.removeListener( 'load', onImgLoadEvent );
			original.removeListener( 'error', onImgLoadErrorEvent );
			original.removeListener( 'abort', onImgLoadErrorEvent );

			// Set Error image.
			var noimage = CKEDITOR.getUrl( editor.skinPath + 'images/noimage.png' );

			if ( this.preview )
				this.preview.setAttribute( 'src', noimage );

			// Hide loader
			CKEDITOR.document.getById( 'ImagePreviewLoader' ).setStyle( 'display', 'none' );
			switchLockRatio( this, false );	// Unlock.
		};
		return {
			title : editor.lang.spipdoc.title,
			minWidth : 420,
			minHeight : 360,
			onShow : function()
			{
				this.imageElement = false;

				// Default: create a new element.
				this.imageEditMode = false;

				this.lockRatio = true;
				this.dontResetSize = false;
				this.firstLoad = true;

				//Hide loader.
				CKEDITOR.document.getById( 'ImagePreviewLoader' ).setStyle( 'display', 'none' );
				// Preview
				this.preview = CKEDITOR.document.getById( 'previewImage' );

				var editor = this.getParentEditor(),
					sel = this.getParentEditor().getSelection(),
					element = sel.getSelectedElement() ;

				// Copy of the image
				this.originalElement = editor.document.createElement( 'img' );
				this.originalElement.setAttribute( 'alt', '' );
				this.originalElement.setCustomData( 'isReady', 'false' );

				if ( element && element.getName() == 'img' && !element.getAttribute( '_cke_protected_html' ) )
					this.imageEditMode = 'img';

				if ( this.imageEditMode || this.imageElement )
				{
					if ( !this.imageElement )
						this.imageElement = element;

					// Fill out all fields.
					this.setupContent( IMAGE, this.imageElement );

					// Refresh LockRatio button
					switchLockRatio ( this, true );
				}
			},
			onOk : function()
			{
				// Create a new image.
				this.imageElement = editor.document.createElement( 'img' );
				this.imageElement.setAttribute( 'alt', '' );
				// Set attributes.
				this.commitContent( IMAGE, this.imageElement );
				if (CKEDITOR.ckConfig.vignette) {
					this.imageElement.setStyle('max-width', CKEDITOR.ckConfig.vignette+'px') ;
					this.imageElement.setStyle('max-height', CKEDITOR.ckConfig.vignette+'px') ;
					this.imageElement.setStyle('width', '') ;
					this.imageElement.setStyle('height','') ;
					this.imageElement.setAttribute('width', '') ;
					this.imageElement.setAttribute('height','') ;
				}
				editor.insertElement( this.imageElement );

			},
			onLoad : function()
			{
				var doc = this._.element.getDocument();
				this.addFocusable( doc.getById( 'btnResetSize' ), 5 );
				this.addFocusable( doc.getById( 'btnLockSizes' ), 5 );
			},
			onHide : function()
			{
				if ( this.preview )
					this.commitContent( CLEANUP, this.preview );

				if ( this.originalElement )
				{
					this.originalElement.removeListener( 'load', onImgLoadEvent );
					this.originalElement.removeListener( 'error', onImgLoadErrorEvent );
					this.originalElement.removeListener( 'abort', onImgLoadErrorEvent );
					this.originalElement.remove();
					this.originalElement = false;		// Dialog is closed.
				}
			},
			contents : [
				{
					id : 'info',
					label : editor.lang.spipdoc.infoTab,
					accessKey : 'I',
					elements :
					[
						{
							type : 'vbox',
							padding : 0,
							children :
							[
								{
									type : 'html',
									html : '<span>' + CKEDITOR.tools.htmlEncode( editor.lang.spipdoc.url ) + '</span>'
								},
								{
									type : 'hbox',
									widths : [ '280px', '110px' ],
									align : 'right',
									children :
									[
										{
											id : 'txtUrl',
											type : 'text',
											label : '',
											onChange : function()
											{
												var dialog = this.getDialog(),
													newUrl = this.getValue();

												//Update original image
												if ( newUrl.length > 0 )	//Prevent from load before onShow
												{
													dialog = this.getDialog();

													if (!urlUpdating) {
														urlUpdating = true ;
														var docMatch = newUrl.match( regexGetSpipDoc ) ;
														if (docMatch) {
															if (docMatch[7]) // Change docParam only if there's a docParam
																dialog.setValueOf('info', 'docParam', decodeURI(docMatch[7])) ;
															if (docMatch[4]) // Change docType only, if thers's a docType
																dialog.setValueOf('info', 'docType', docMatch[4]) ;
															dialog.setValueOf('info', 'docId', docMatch[2]) ;
														} else {
															dialog.setValueOf('info', 'docParam', '') ;
															dialog.setValueOf('info', 'docType', '') ;
															dialog.setValueOf('info', 'docId', '') ;
															alert('là');
														}
														urlUpdating = false ;
														updateUrl(this, editor, 'txtUrl') ;
													}

													var original = dialog.originalElement;

													if (original) {

														original.setCustomData( 'isReady', 'false' );
														// Show loader
														var loader = CKEDITOR.document.getById( 'ImagePreviewLoader' );
														if ( loader )
															loader.setStyle( 'display', '' );
	
														original.on( 'load', onImgLoadEvent, dialog );
														original.on( 'error', onImgLoadErrorEvent, dialog );
														original.on( 'abort', onImgLoadErrorEvent, dialog );
														original.setAttribute( 'src', newUrl );
													}

													dialog.preview.setAttribute( 'src', newUrl );
													updatePreview( dialog );
												}
											},
											setup : function( type, element )
											{
												if ( type == IMAGE )
												{
													var url = element.getAttribute( 'src' );
													var field = this;

													this.getDialog().dontResetSize = true;

													// In IE7 the dialog is being rendered improperly when loading
													// an image with a long URL. So we need to delay it a bit. (#4122)
													setTimeout( function()
														{
															field.setValue( url );		// And call this.onChange()
															field.focus();
														}, 0 );
												}
											},
											commit : function( type, element )
											{
												if ( type == IMAGE && ( this.getValue() || this.isChanged() ) )
												{
													element.setAttribute( 'src', decodeURI( this.getValue() ) );
												}
												else if ( type == CLEANUP )
												{
													element.setAttribute( 'src', '' );	// If removeAttribute doesn't work.
													element.removeAttribute( 'src' );
												}
											}
										},
										{
											type : 'button',
											id : 'browse',
											align : 'center',
											label : editor.lang.spipdoc.browseServer,
											hidden : true,
											filebrowser : 'info:txtUrl'
										}
									]
								}
							]
						},
						{
							type : 'hbox',
							widths : [ '140px', '240px' ],
							children :
							[
								{
									type : 'vbox',
									padding : 10,
									children :
									[
										{
											type : 'hbox',
											widths : [ '70%', '30%' ],
											children :
											[
												{
													type : 'vbox',
													padding : 1,
													children :
													[
														{
															type : 'text',
															width: '40px',
															id : 'txtWidth',
															labelLayout : 'horizontal',
															label : editor.lang.spipdoc.width,
															onKeyUp : onSizeChange,
															validate: function()
															{
																var aMatch  =  this.getValue().match( regexGetSizeOrEmpty );
																if ( !aMatch )
																	alert( editor.lang.common.validateNumberFailed );
																return !!aMatch;
															},
															setup : setupDimension,
															commit : function( type, element )
															{
																if ( type == IMAGE )
																{
																	var value = this.getValue();
																	if ( value )
																		element.setAttribute( 'width', value );
																	else if ( !value && this.isChanged() )
																		element.removeAttribute( 'width' );
																}
																else if ( type == PREVIEW )
																{
																	value = this.getValue();
																	var aMatch = value.match( regexGetSize );
																	if ( !aMatch )
																	{
																		var oImageOriginal = this.getDialog().originalElement;
																		if ( oImageOriginal.getCustomData( 'isReady' ) == 'true' )
																			element.setStyle( 'width',  oImageOriginal.$.width + 'px');
																	}
																	else
																		element.setStyle( 'width', value + 'px');
																}
																else if ( type == CLEANUP )
																{
																	element.setStyle( 'width', '0px' );	// If removeAttribute doesn't work.
																	element.removeAttribute( 'width' );
																	element.removeStyle( 'width' );
																}
															}
														},
														{
															type : 'text',
															id : 'txtHeight',
															width: '40px',
															labelLayout : 'horizontal',
															label : editor.lang.spipdoc.height,
															onKeyUp : onSizeChange,
															validate: function()
															{
																var aMatch = this.getValue().match( regexGetSizeOrEmpty );
																if ( !aMatch )
																	alert( editor.lang.common.validateNumberFailed );
																return !!aMatch;
															},
															setup : setupDimension,
															commit : function( type, element )
															{
																if ( type == IMAGE )
																{
																	var value = this.getValue();
																	if ( value )
																		element.setAttribute( 'height', value );
																	else if ( !value && this.isChanged() )
																		element.removeAttribute( 'height' );
																}
																else if ( type == PREVIEW )
																{
																	value = this.getValue();
																	var aMatch = value.match( regexGetSize );
																	if ( !aMatch )
																	{
																		var oImageOriginal = this.getDialog().originalElement;
																		if ( oImageOriginal.getCustomData( 'isReady' ) == 'true' )
																			element.setStyle( 'height',  oImageOriginal.$.height + 'px');
																	}
																	else
																		element.setStyle( 'height', value + 'px');
																}
																else if ( type == CLEANUP )
																{
																	element.setStyle( 'height', '0px' );	// If removeAttribute doesn't work.
																	element.removeAttribute( 'height' );
																	element.removeStyle( 'height' );
																}
															}
														}
													]
												},
												{
													type : 'html',
													style : 'margin-top:10px;width:40px;height:40px;',
													onLoad : function()
													{
														// Activate Reset button
														var	resetButton = CKEDITOR.document.getById( 'btnResetSize' ),
															ratioButton = CKEDITOR.document.getById( 'btnLockSizes' );
														if ( resetButton )
														{
															resetButton.on( 'click', function()
																{
																	resetSize( this );
																}, this.getDialog() );
															resetButton.on( 'mouseover', function()
																{
																	this.addClass( 'cke_btn_over' );
																}, resetButton );
															resetButton.on( 'mouseout', function()
																{
																	this.removeClass( 'cke_btn_over' );
																}, resetButton );
														}
														// Activate (Un)LockRatio button
														if ( ratioButton )
														{
															ratioButton.on( 'click', function()
																{
																	var locked = switchLockRatio( this ),
																		oImageOriginal = this.originalElement,
																		width = this.getValueOf( 'info', 'txtWidth' );

																	if ( oImageOriginal.getCustomData( 'isReady' ) == 'true' && width )
																	{
																		var height = oImageOriginal.$.height / oImageOriginal.$.width * width;
																		if ( !isNaN( height ) )
																		{
																			this.setValueOf( 'info', 'txtHeight', Math.round( height ) );
																			updatePreview( this );
																		}
																	}
																}, this.getDialog() );
															ratioButton.on( 'mouseover', function()
																{
																	this.addClass( 'cke_btn_over' );
																}, ratioButton );
															ratioButton.on( 'mouseout', function()
																{
																	this.removeClass( 'cke_btn_over' );
																}, ratioButton );
														}
													},
													html : '<div>'+
														'<a href="javascript:void(0)" tabindex="-1" title="' + editor.lang.spipdoc.lockRatio +
														'" class="cke_btn_locked" id="btnLockSizes"></a>' +
														'<a href="javascript:void(0)" tabindex="-1" title="' + editor.lang.spipdoc.resetSize +
														'" class="cke_btn_reset" id="btnResetSize"></a>'+
														'</div>'
												}
											]
										},
										{
											type : 'html',
											html : '<div style="width:250px;white-space:normal;margin:0;padding:0;">Les informations de largeur et hauteur ne servent que pour la prévisualisation dans l\'éditeur.<br/>Pour les modifier effectivement, il faut utiliser l\'interface de SPIP.</div>'
										},
										{
											type : 'vbox',
											padding : 1,
											children :
											[
												{
													id : 'cmbAlign',
													type : 'select',
													labelLayout : 'horizontal',
													widths : [ '35%','65%' ],
													style : 'width:90px',
													label : editor.lang.spipdoc.align,
													'default' : '',
													items :
													[
														[ editor.lang.spipdoc.alignMiddle , 'middle'],
														[ editor.lang.spipdoc.alignLeft , 'left'],
														[ editor.lang.spipdoc.alignRight , 'right']
													],
													onChange : function()
													{
														updatePreview( this.getDialog() );
													},
													setup : function( type, element )
													{
														if ( type == IMAGE )
															this.setValue( element.getAttribute( 'align' ) );
													},
													commit : function( type, element )
													{
														var value = this.getValue();
														if ( type == IMAGE )
														{
															if ( value || this.isChanged() )
																element.setAttribute( 'align', value );
															if ( value == 'middle') {
																element.setStyle ('display', 'block') ;
																element.setStyle ('margin-left', 'auto') ;
																element.setStyle ('margin-right', 'auto') ;
															} else {
																element.setStyle ('display', '') ;
																element.setStyle ('margin-left', '') ;
																element.setStyle ('margin-right', '') ;
															}

														}
														else if ( type == PREVIEW )
														{
															element.setAttribute( 'align', this.getValue() );

															if ( value == 'absMiddle' || value == 'middle' )
																element.setStyle( 'vertical-align', 'middle' );
															else if ( value == 'top' || value == 'textTop' )
																element.setStyle( 'vertical-align', 'top' );
															else
																element.removeStyle( 'vertical-align' );

															if ( value == 'right' || value == 'left' )
																element.setStyle( 'styleFloat', value );
															else
																element.removeStyle( 'styleFloat' );

															if (value == 'middle') {
																element.setStyle ('display', 'block') ;
																element.setStyle ('margin-left', 'auto') ;
																element.setStyle ('margin-right', 'auto') ;
															} else {
																element.setStyle ('display', '') ;
																element.setStyle ('margin-left', '') ;
																element.setStyle ('margin-right', '') ;
															}


														}
														else if ( type == CLEANUP )
														{
															element.removeAttribute( 'align' );
														}
													}
												}
											]
										}
									]
								},
								{
									type : 'vbox',
									height : '250px',
									children :
									[
										{
											type : 'html',
											style : 'width:95%;',
											html : '<div>' + CKEDITOR.tools.htmlEncode( editor.lang.spipdoc.preview ) +'<br>'+
											'<div id="ImagePreviewLoader" style="display:none"><div class="loading">&nbsp;</div></div>'+
											'<div style="width:200px;height:200px;overflow:scroll;"><div id="ImagePreviewBox" style="white-space:normal;">'+
											'<a href="javascript:void(0)" target="_blank" onclick="return false;" id="previewLink">'+
											'<img id="previewImage" src="" alt="" /></a>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. '+
											'Maecenas feugiat consequat diam. Maecenas metus. Vivamus diam purus, cursus a, commodo non, facilisis vitae, '+
											'nulla. Aenean dictum lacinia tortor. Nunc iaculis, nibh non iaculis aliquam, orci felis euismod neque, sed ornare massa mauris sed velit. Nulla pretium mi et risus. Fusce mi pede, tempor id, cursus ac, ullamcorper nec, enim. Sed tortor. Curabitur molestie. Duis velit augue, condimentum at, ultrices a, luctus ut, orci. Donec pellentesque egestas eros. Integer cursus, augue in cursus faucibus, eros pede bibendum sem, in tempus tellus justo quis ligula. Etiam eget tortor. Vestibulum rutrum, est ut placerat elementum, lectus nisl aliquam velit, tempor aliquam eros nunc nonummy metus. In eros metus, gravida a, gravida sed, lobortis id, turpis. Ut ultrices, ipsum at venenatis fringilla, sem nulla lacinia tellus, eget aliquet turpis mauris non enim. Nam turpis. Suspendisse lacinia. Curabitur ac tortor ut ipsum egestas elementum. Nunc imperdiet gravida mauris.' +
											'</div></div>'+'</div>'
										}
									]
								}
							]
						},
							 { 
								 type : 'hbox',
								 padding : 1,
								 widths: [ '50%', '50%' ],
								 children : [
									 {
										 id : 'docId',
										 type : 'text',
										 label : editor.lang.spipdoc.docid,
										 style : 'width: 60%',
										 onChange: function() { updateUrl(this, editor, "docId") ; }
									 },
									 {

										 id : 'docType',
										 type : 'select',
										 label : editor.lang.spipdoc.doctype,
										 labelLayout : 'vertical',
										 items : [
											 [ 'Image', 'img' ],
											 [ 'Incorporé', 'emb'],
											 [ 'Document', 'doc'],
											 [ 'Vidéo', 'video'],
											 [ 'Audio', 'audio'],
											 [ 'Texte', 'text']
										 ],
										 onChange: function () { updateUrl(this, editor, "docType") ; }
									 }
								 ]
							 },
							 {
									id : 'docParam',
									label: editor.lang.spipdoc.spip_parameters,
									type : 'text',
									onChange: function() { updateUrl(this, editor, "docParam") ; }
							 }

					]
				}
			]
		};
	};

	CKEDITOR.dialog.add( 'spipdoc', function( editor )
		{
			return spipdocDialog( editor, 'spipdoc' );
		});

	CKEDITOR.dialog.add( 'spipdocbutton', function( editor )
		{
			return spipdocDialog( editor, 'spipdocbutton' );
		});
})();

