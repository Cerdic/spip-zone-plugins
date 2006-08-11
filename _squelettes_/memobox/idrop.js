/*
 * interface dropzones - http://www.eyecon.ro/interface/
 *
 * Copyright (c) 2006 Stefan Petre
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 */

jQuery.droppables = {atLeast:0};
jQuery.droppablesHighlited = {};

jQuery.drophighlight = function ()
{
    if (jQuery.drug == null) {
		return;
    }
	var i;
	for (i in jQuery.droppables) {
		if ( i != 'atLeast') {
			iEL = jQuery.droppables[i].cur[0];
			if (jQuery.hasWord(jQuery.drug,iEL.f.a)) {
				if (iEL.f.m == false) {
					iEL.f.p = jQuery.getPos(iEL);
					iEL.f.m = true;
				}
				if (iEL.f.ac) {
					jQuery.droppables[i].addClass(iEL.f.ac);
				}
				iEL.f.drug = true;
				jQuery.droppablesHighlited[i] = jQuery.droppables[i];
				if (jQuery.sortables && jQuery.drug.d.so) {
					iEL.f.el = jQuery('.' + iEL.f.a, iEL);
					jQuery.sortremeasure(iEL);
				}
			}
		}
	}
	if (jQuery.sortables && jQuery.drug.d.so) {
		jQuery.sortstart();
	}
};

jQuery.dropcheck = function (e)
{
    if (jQuery.drug == null) {
		return;
    }
	var i;
	for (i in jQuery.droppablesHighlited) {
		if ( i != 'atLeast') {
			iEL = jQuery.droppablesHighlited[i].cur[0];
			if (iEL.f.ac) {
				jQuery.droppablesHighlited[i].removeClass(iEL.f.ac);
			}
			if (iEL.f.hc) {
				jQuery.droppablesHighlited[i].removeClass(iEL.f.hc);
			}
			if(iEL.f.s) {
				jQuery.sortchanged[jQuery.sortchanged.length] = i;
			}
			
			if (iEL.f.ondrop && iEL.f.h == true) {
				iEL.f.ondrop(iEL, e, iEL.f.fx);
			}
			iEL.f.drug = false;
			iEL.f.m = false;
			iEL.f.h  = false;
		}
	}
	jQuery.droppablesHighlited = {};
};

jQuery.dropcheckhover = function ( x, y, ex, ey)
{
    if (jQuery.drug == null) {
		return;
    }
	jQuery.overzone = false;
	var i;
	for (i in jQuery.droppablesHighlited)
	{
		if ( i != 'atLeast') {
			iEL = jQuery.droppablesHighlited[i].cur[0];
				cond = false;
			if ( jQuery.overzone == false) {
				switch (iEL.f.t)
				{
					case 'fit':
						cond = iEL.f.p.x <= ex && 
						(iEL.f.p.x + iEL.f.p.wb) >= (ex + jQuery.drug.d.oC.w) &&
						iEL.f.p.y <= ey && 
						(iEL.f.p.y + iEL.f.p.hb) >= (ey + jQuery.drug.d.oC.h) ? true :false;
						break;
					case 'intersect':
						cond = 
						! ( iEL.f.p.x > (ex + jQuery.drug.d.oC.w)
						|| (iEL.f.p.x + iEL.f.p.wb) < ex 
						|| iEL.f.p.y > (ey + jQuery.drug.d.oC.h) 
						|| (iEL.f.p.y + iEL.f.p.hb) < ey
						) ? true :false;
						break;
					case 'pointer':
						cond = 
						( iEL.f.p.x < x
						&& (iEL.f.p.x + iEL.f.p.wb) > x 
						&& iEL.f.p.y < y 
						&& (iEL.f.p.y + iEL.f.p.hb) > y
						) ? true :false;
						break;
				}
			}
			if ( jQuery.overzone == false && cond == true ) {
				if (iEL.f.hc) {
					jQuery.droppablesHighlited[i].addClass(iEL.f.hc);
					jQuery.droppablesHighlited[i].removeClass(iEL.f.ac);
				}
				iEL.f.h = true;
				jQuery.overzone = iEL;
				if(jQuery.sortables && jQuery.drug.d.so) {
					jQuery.sortcheckhover(
						 iEL,
						{
							x : x,
							y : y,
							ex : ex,
							ey : ey
						}
					);
				}
			} else {
				if (iEL.f.hc) {
					jQuery.droppablesHighlited[i].removeClass(iEL.f.hc);
					jQuery.droppablesHighlited[i].addClass(iEL.f.ac);
				}
				iEL.f.h = false;
			}
		}
	}
	if (jQuery.sortables && jQuery.overzone == false) {
		jQuery.sortHelper.cur[0].style.display = 'none';
		jQuery('body').append(jQuery.sortHelper.cur[0]);
	}
};

jQuery.fn.Droppable = function (o)
{
	if (o.accept) {
		return this.each(
			function()
			{
				if (this.isDroppable == true){
					return;
				}
				this.f = {
					a : o.accept,
					ac: o.activeclass, 
					hc:	o.hoverclass,
					ondrop:	o.ondrop,
					t: o.tolerance && ( o.tolerance == 'fit' || o.tolerance == 'intersect') ? o.tolerance : 'pointer',
					fx: o.fx ? o.fx : false,
					m: false,
					h: false
				};
				if (o.sortable == true) {
					id = jQuery.attr(this,'id');
					if (!jQuery.sortables){
						jQuery.sortables = [];
					}
					jQuery.sortables[id] = this.f.a;
					this.f.s = true;
					if(o.onchange) {
						this.f.onchange = o.onchange;
						this.f.os = jQuery.Sortserialize(id).hash;
					}
				}
				this.isDroppable = true;
				idsa = parseInt(Math.random() * 10000);
				jQuery.droppables['d' + idsa] = jQuery(this);
				jQuery.droppables.atLeast ++;
			}
		);
	}
};
