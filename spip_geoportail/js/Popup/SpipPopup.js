/*
 * Copyright 2012 Institut Geographique National France, released under the
 * BSD license.
 *
 *	Affichage de Popup personnalisables par css.
 *
 */
/*
 * @requires OpenLayers.Popup.Anchored
 */
 
// Fonction de chargement
function GeoportailInitSpipPopup()
{
/**
 * Class: OpenLayers.Popup.SpipPopup
 * Affichage de popup personnalises par css
 *
 * Inherits from:
 * - <OpenLayers.Popup.Anchored>
 */

OpenLayers.Popup.SpipPopup =
  OpenLayers.Class(OpenLayers.Popup.Anchored, {
    
    // Recouvrement avec le bord (vertical)
    dOverlap: 8,
    // Distance du bord (horizontal)
    dRound: 20,	
    // Classe pour l'affichage (les fonds sont definis dans le css
    displayClass: "SpipPopup",
    
    /**
    *	Position des blocks
    *	| TL | TR |
	*	| BL | BR |
	*	 C
    */
    initialize:function(id, lonlat, contentSize, contentHTML, anchor, closeBox, closeBoxCallback) 
    {   OpenLayers.Popup.Anchored.prototype.initialize.apply(this, arguments);

		// Prevoir le fond
        this.contentDiv.style.zIndex = 1;
        this.div.style.overflow = 'visible';
        this.div.className = "SpipPopup "+this.div.className;
        this.groupDiv.style.overflow = 'visible';
        this.groupDiv.className = "SpipPopupGroup "+this.groupDiv.className;
        this.contentDiv.className = "SpipPopupContent "+this.contentDiv.className;
        if (closeBox) {
            this.closeDiv.style.zIndex = 1;
        }

		// Calcul de la taille des accroches (toutes identiques)
		if (!jQuery("."+this.displayClass+"C").length)
		{	jQuery("body").append ("<div class='"+this.displayClass+"C' style='display:none'></div>");
		}
		this.csize = new OpenLayers.Size (jQuery("."+this.displayClass+"C").width(), jQuery("."+this.displayClass+"C").height() -this.dOverlap );
    },

    /**
     * APIMethod: setBackgroundColor, setBorder, setOpacity : ne fait rien !
     */
    setBackgroundColor:function(color) {},
	setBorder:function() {},
	setOpacity:function(opacity) {},

    /** 
     * APIMethod: destroy
     */
    destroy: function() 
    {	//remove our blocks
        for(var i = 0; i < this.blocks.length; i++) 
        {   var block = this.blocks[i];
            if (block.div) this.groupDiv.removeChild(block.div);
            block.div = null;
        }
        this.blocks = null;

        OpenLayers.Popup.Anchored.prototype.destroy.apply(this, arguments);
    },
    
    /** 
    *	Calcul de la position des accroches
    */
    accroche: function()
    {	var pos;
		switch (this.relativePosition)
		{	case 'bl' : 
				pos = new OpenLayers.Pixel (this.size.w-this.csize.w-this.dRound, -this.csize.h); 
				break;
			case 'br' : 
				pos = new OpenLayers.Pixel (this.dRound, -this.csize.h); 
				break;
			case 'tl' : 
				pos = new OpenLayers.Pixel (this.size.w-this.csize.w-this.dRound, this.size.h-this.dOverlap); 
				break;
			case 'tr' : 
				pos = new OpenLayers.Pixel (this.dRound, this.size.h-this.dOverlap); 
				break;
		}
		return pos;
    },
    
	/**
	*	Ajout des blocks en fond
	*/
	createBlocks:function ()
	{	if (!this.blocks)
		{	this.blocks = [];
		
			var block = {};
            this.blocks.push(block);
			
			var tblock = [ "TL", "TR", "BL", "BR" ];
			var tpos =
			[	new OpenLayers.Pixel (0,0),
				new OpenLayers.Pixel (Math.round(this.size.w/2), 0),
				new OpenLayers.Pixel (0, Math.round(this.size.h/2)),
				new OpenLayers.Pixel (Math.round(this.size.w/2), Math.round(this.size.h/2))
			];
			var tsize =
			[	new OpenLayers.Size (Math.round(this.size.w/2), Math.round(this.size.h/2)),
				new OpenLayers.Size (this.size.w-Math.round(this.size.w/2), Math.round(this.size.h/2)),
				new OpenLayers.Size (Math.round(this.size.w/2), this.size.h-Math.round(this.size.h/2)),
				new OpenLayers.Size (this.size.w-Math.round(this.size.w/2), this.size.h-Math.round(this.size.h/2))
			];
			
			for (var i=0; i< 4; i++)
			{	block.div = OpenLayers.Util.createDiv(this.id+'_back'+tblock[i], tpos[i], tsize[i], null, "absolute", null, "hidden", null );
				block.div.className = this.displayClass+"Back SpipPopup"+tblock[i];
				this.groupDiv.appendChild(block.div);
			}
			
            block.div = OpenLayers.Util.createDiv(this.id+'_backC', this.accroche(), null, null, "absolute", null, "hidden", null );
            block.div.className = this.displayClass+"C "+this.displayClass+"C"+this.relativePosition;
            this.groupDiv.appendChild(block.div);
		}
	},
	
    /**
     * APIMethod: setSize
     * Overridden here, because we need to update the blocks whenever the size
     *     of the popup has changed.
     * 
     * Parameters:
     * contentSize - {<OpenLayers.Size>} the new size for the popup's 
     *     contents div (in pixels).
     */
    setSize:function(contentSize) 
    {   OpenLayers.Popup.Anchored.prototype.setSize.apply(this, arguments);

        this.createBlocks();
    },

    /**
     * Method: updateRelativePosition
     *	Replacer les accroches
     */
    updateRelativePosition: function() 
    {	if (this.blocks && this.size && this.relativePosition)
		{	var accr = jQuery("."+this.displayClass+"C");
			accr.removeClass(""+this.displayClass+"Ctl")
				.removeClass(""+this.displayClass+"Ctr")
				.removeClass(""+this.displayClass+"Cbl")
				.removeClass(""+this.displayClass+"Cbr")
				.addClass(""+this.displayClass+"C"+this.relativePosition);
			var pos = this.accroche();
			accr.css('top',pos.y);
			accr.css('left',pos.x);
		}
    },
    
    /** 
     * Method: calculateNewPx
     *	Nouvelle position
     */
	calculateNewPx:function(px) 
	{   var newPx = OpenLayers.Popup.Anchored.prototype.calculateNewPx.apply(this, arguments);

        var top = (this.relativePosition.charAt(0) == 't');
        newPx.y += (top) ? -this.csize.h : this.csize.h;

        var left = (this.relativePosition.charAt(1) == 'l');
        newPx.x += (left) ? this.csize.w/2+this.dRound : -this.csize.w/2-this.dRound;

        return newPx;
    },
    
    CLASS_NAME: "OpenLayers.Popup.SpipPopup"
});

/** Autres definition
*/
OpenLayers.Popup.SpipPopupOmbre = OpenLayers.Class(OpenLayers.Popup.SpipPopup, { 'displayClass':OpenLayers.Popup.SpipPopupShadow, 'dOverlap':11 });

OpenLayers.Popup.SpipPopupThink = OpenLayers.Class(OpenLayers.Popup.SpipPopup, { 'displayClass':OpenLayers.Popup.SpipPopupThink, 'dOverlap':11, dRound:5 });

}