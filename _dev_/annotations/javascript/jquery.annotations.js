/******************************************
 * A jQuery plugin to annotate maps
 *  
 * Copyright (c) 2007 Renato Formato <renatoformato@virgilio.it>
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *       
 ******************************************/


(function($) {
	$.fn.extend({
		getHashWindow: function() {
				if(!this.size()) return;
				return $.jqm.hash[this[0]._jqm];
		}
	});
	
	$.carto = {
		id_document: 0,
		postData: {},
		idCsv: 0,
		overlay:[],
		mapMarker: {
			src:"<div style='width:5px;height:5px;background:#000'>",
			map:"",	
			hotSpot:[0,0]
		},
		cfg: {},
		get_xy_coord: function(el,event) {
		  var off = el.offset();
			var off_x = event.pageX-off.left,off_y = event.pageY-off.top;
		  return {x:off_x,y:off_y}; 
		},
		getOverlayIndex: function(el) {
				var index;
				$.each($.carto.overlay,function(i,n){
					if(n.container[0]==el[0]) {
						index = i; 
						return false;
					}
				});	
				return index;
		},
		pointInPoly: function(point,coords) {
			var xy = coords.split(","),a,b,c,nIntersect=0;
			for(var i=0,l=xy.length-2;i<l;i+=2) {
				var min = Math.min(xy[i],xy[i+2]);
				var max = Math.max(xy[i],xy[i+2]);
				if(min<=point[0] && point[0]<max) {
					a = xy[i+3]-xy[i+1];
					b = xy[i]-xy[i+2];
					c = xy[i+2]*xy[i+1]-xy[i]*xy[i+3];
					if(b && (a*point[0]+c)/-b>point[1])
						nIntersect++;
				}
			}
			return nIntersect%2?true:false;
		},
		addMarker: function(selector,coord,attr) {
		  attr = attr || {};
			var container = $(selector).parent();
			if(!container.is(".marker_container")) {
		  	container = $(selector).wrap("<div class='marker_container'>").parent().width($(selector).width());
				container.css("position","relative");
				$.carto.overlay.push({container:container,items:$([]),zindex:1});
				var offset = container.offset();  
				if($.carto.mapMarker.map) {
					container.mousemove(function(e){
						var x = e.pageX-offset.left;
						var y = e.pageY-offset.top;
						var intersect = $.grep($.carto.overlay[index].items,function(n){
							return (x>=n.offsetLeft && x<=n.offsetLeft+n.width && y>=n.offsetTop && y<=n.offsetTop+n.height);
						});
						if(intersect.length==1)
							$(intersect).css("visibility","visible");
						if(intersect.length>1) {
							var areas = $.grep(intersect,function(n){
								var area = $("map[@name="+n.useMap.substring(1)+"] area");
								var offsetArea = $(n).offset();
								return $.carto.pointInPoly([e.pageX-offsetArea.left,e.pageY-offsetArea.top],area.attr("coords"));
							});
							var $areas = $(areas);
							$(intersect).not($areas.not(":last")).filter(":visible").css("visibility","hidden");
							$areas.filter(":last:hidden").css("visibility","visible");
						} 
					})
				}
		  }
			var index = $.carto.getOverlayIndex(container);
		  var hotSpot = $.carto.mapMarker.hotSpot;
			var marker = $($.carto.mapMarker.src).addClass("map_marker");
			marker.attr(attr);
			marker.css({zIndex:$.carto.overlay[index].zindex,position:"absolute",left:coord.xy[0]-hotSpot[0]+"px",top:coord.xy[1]-hotSpot[1]+"px"}).
			appendTo(container).ifixpng();
			var overlay = $("<img class='map_marker_overlay' src='"+$.carto.cfg.emptyImage+"' width='"+marker.width()+"' height='"+marker.height()+"' />").
			css({zIndex:1000+$.carto.overlay[index].zindex,position:"absolute",left:coord.xy[0]-hotSpot[0]+"px",top:coord.xy[1]-hotSpot[1]+"px"}).
			appendTo(container);
			$.carto.overlay[index].items = $.carto.overlay[index].items.add(overlay);
			$.carto.overlay[index].zindex++;
			var match = marker.attr("id").match(/(\D+)(\d+)/);
			var id_prefix = match[1];
			var id = match[2];
			if($.carto.mapMarker.map) {
				var name = id_prefix+"_html"+id;
				//IE cannot change name attribute at runtime
				var map = $($.carto.mapMarker.map.replace(/<map>/,"<map name='"+name+"'>"));
				map.find("area").attr({title:attr.title,id:id_prefix+"_html_area"+id});
				marker.attr("title","");
				marker.before(map);
				marker[0].useMap = "#"+name;
	 			overlay[0].useMap = "#"+name; 			
			}	else {
				overlay.attr({id:id_prefix+"_overlay"+id,title:attr.title}).css("cursor","pointer");
			}	
		},
		loadAnnotations: function(ids,callback,params) {
				var options = {
				url: $.carto.cfg.loadAnnotations,
				type: "POST",
				data: $.extend({'id_document[]':ids},params),
				dataType: "json",
				success: callback
			};
			$.ajax(options);
		},
		loadCoordAnnotations: function(ids,callback,params) {
				var options = {
				url: $.carto.cfg.loadCoordsAnnotations,
				type: "POST",
				data: $.extend({'id_document[]':ids},params),
				dataType: "json",
				success: callback
			};
			$.ajax(options);
		},
		saveAnnotations: function(postData,points,callback) {
			var data = postData;
			data['id_annotation[]'] = [];
			data['id_document[]'] = [];
			data['title[]'] = [];
			data['text[]'] = [];
			data['x[]'] = [];
			data['y[]'] = [];		
			$.each(points.ids,function(i,n){
				data['id_annotation[]'].push(n.id_annotation);
				data['id_document[]'].push(n.id_document);
				data['title[]'].push(n.title);
				data['text[]'].push(n.text);
				data['x[]'].push(n.x);
				data['y[]'].push(n.y);
			})
			var options = {
				type: "POST",
				url: $.carto.cfg.action, 
				data: data,
				dataType: "json",
				error: function() {
					alert("ajax error");
					return false;
				}, 
				success: callback
			};
			$.ajax(options);
		},
		deleteAnnotations: function(postData,points,callback) {
			var data = postData;
			data['id_annotation[]'] = [];
			$.each(points.ids,function(i,n){
				data['id_annotation[]'].push(-n.id_annotation);
			});
			var options = {
				type: "POST",
				url: $.carto.cfg.action, 
				data: data,
				dataType: "json",
				error: function() {
					alert("ajax error");
					return false;
				}, 
				success: callback
			};
			$.ajax(options);
		},
		/* Display the markers on the respective images
		*  
		*	 displayMarkers(points,images,callback)
		*			or
		*	 displayMarkers(points,images,id_prefix,callback)
		*		
		*	 Arguments:
		*	 -points (object):
		*	 		object with the points and document data			
		*	 -images (hash):
		*	 		hash of jQuery objects or selectors. The name of every item must be the id_document
		*	 -id_prefix (string):
		*	 		string that will be used to create the ids of the markers and all other necessary	 
		*	 		markup (default="map_marker"). Usefull when showing several times the same points on a page	
		*	 -callback (function):
		*	 		a function that takes 2 arguments, the point object and an hash with the attributes to
		*	 		be added to the marker for that object, and returns the modified attribute hash,
		*	 		EX: 
		*	 		function(point,attr) {
		*	 			attr.title = point.title+"; click here to show the data";
		*	 			return attr;	
		*			}	 												
		*/
		displayMarkers: function(points,images,id_prefix,callback) {
			if($.isFunction(id_prefix)) {
				callback = id_prefix;
				id_prefix = null;
			}
			id_prefix = id_prefix || "map_marker";
			$.each(points.ids,function(i,n){
				var cx,cy;
				var doc = points.documents[n.id_document];
				cx = (n.x/doc.width)*images[n.id_document].width();
				cy = (n.y/doc.height)*images[n.id_document].height();
				var attr = {id:id_prefix+n.id_annotation};
				if(callback)
					attr = callback(n,attr);
				$.carto.addMarker(
					images[n.id_document],
					{xy:[cx,cy]},
					attr
				)
			});				
		},
		loadCsv: function(id,callback) {
			var options = {
				url: $.carto.cfg.loadCsv,
				data: "id_document="+id,
				dataType: "json",
				error: function() {
					alert("ajax error");
					return false;
				}, 
				success: callback
			};
			$.ajax(options);	
		},
		loadMarkers: function(root,callback,params) {
			root = root || $("body");
			var map_ids = [];
			$("img[@id^=annotated_map]",root).each(function(){
				var id = this.id.match(/\d+/);
				if(id) map_ids.push(id);
			});
			$.carto.loadCoordAnnotations(map_ids,function(data) {
				var images = {};
				$.each(data.documents,function(i,n){
					images[i] = $("#annotated_map"+i);
				});
				callback(data,images);
			},params);
		},
		loadMarkersWithTooltip: function(root,params) {
			$.carto.loadMarkers(root,function(data,images) { 
				$.carto.displayMarkers(data,images,function(n,attr){ 
					attr.title = n.title;
					return attr;
				});
				
				//IE shows the alt as tooltips
				if($.browser.msie)
					$(".marker_container img").attr("alt","");
				var markers = $(".marker_container map >");
				if(!markers.size())
					markers = $(".marker_container .map_marker_overlay");
				//full tooltip mode
				$.Tooltip.persistent = true;
				markers.Tooltip({
					showURL: false,
					extraClass: "carto",
					bodyHandler: function(current) {
						var id = this.id.match(/\d+/);
						if(!$("#annotate_show_text"+id).size()) {
							$("<div id='annotate_show_text"+id+"' style='display:none'><div style='margin:auto;text-align:center'><img src='"+$.carto.cfg.loaderImage+"' /></div></div>").appendTo("body");
							$("#annotate_show_text"+id).load(
								$.carto.cfg.loadAnnotationText,{id_annotation:id},
								function() { 
									if($.Tooltip.current)
										$("#tooltip div.body").html($("#annotate_show_text"+id).html());
										if($("#tooltip").is(":visible"))
											$.Tooltip.update();
								}
							);
						}
						return $("#annotate_show_text"+id).html(); 						
					}
				});
				$("#tooltip").click(function(e) {
					if($(e.target).parents("a.jqmClose").size()) {
						$.Tooltip.hide();
						return false;
					}
				});
			},params)		
		},
		loadMarkersWithOverlay: function(root,params) {
			$.carto.loadMarkers(root,function(data,images) { 
				$.carto.displayMarkers(data,images,function(n,attr){ 
					attr.title = n.title+" ---- click the point to see more";
					return attr;
				});
				
				//IE shows the alt as tooltips
				if($.browser.msie)
					$(".marker_container img").attr("alt","");
				var markers = $(".marker_container map >");
				if(!markers.size())
					markers = $(".marker_container .map_marker_overlay");
				//overlay window mode
				markers.Tooltip({showBody:" ---- ",showURL:false})
				.click(function(){
					var id = this.id.match(/\d+/);
					if(!$("#annotate_show_text"+id).size())
						$("<div id='annotate_show_text"+id+"' style='display:none' class='jqmWindow'><div style='margin:auto;text-align:center'><img src='"+$.carto.cfg.loaderImage+"' /></div></div>").appendTo("body");
					$("#annotate_show_text"+id).jqm({
						onShow: function(h){
							//mozilla cannot display flash movies when a position:fixed element is in the page
							//explorer also has problems when the body element has padding
							h.o.css({height:$(document).height()+"px",width:$(document).width()+"px",position:"absolute",top:"0px"});
							h.w.show();
							$.ajax({
								url: $.carto.cfg.loadAnnotationText,
								data: {id_annotation:id},
								success: function(data) {
									//IE donesn't like the script with html comments
									if($.browser.msie) {
										data = $("<div>"+data+"</div>").find("script").each(function() {
											this.text = $.trim(this.text).replace(/^<!--.*/,"");
										}).end().html();
									}
									h.w.html(data);
									h.w.jqmAddClose(h.w.find("."+h.c.closeClass));
								}
							});
						}
					})
					.css("top",$(window).scrollTop()+$(window).height()*.17+"px").jqmShow();
					return false;
				});			
			},params)				
		}
	}
})(jQuery)
