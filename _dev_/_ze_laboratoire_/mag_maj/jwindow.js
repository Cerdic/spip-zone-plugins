/*
 * jwindow - open up your web apps
 * By Joe Root (http://www.therootdesign.co.uk)
 * Copyright 2006 Joe Root
 * Licensed under the MIT License:
 */

$(document).ready(initWindow);
		
	function initWindow() {
		$("div.window_content").hide();

		//$(".window_content").click(function(){
		//	levelWindow(this.id);
		//});
	
		$("a.window_link").click(function(){
	
			var url = this.href;
			var title = this.title || this.name || null;
			
			url = url.substring(url.indexOf("#")+1);	//this removes everything from the url, but the id and height, for example "www.example.com/#eg?300,600" will become "eg?300,600"
			var windowSize = url.substring(url.indexOf("?")+1); //this removes the users size optyions, so "www.example.com/#eg?300,600" will give "300,600"
			if (url.indexOf("?") != -1) {
			url = url.substring(url.indexOf("?"), -100);
			}

			calculateSizes(windowSize, url);

			$("div#" + "handle" + url).remove();
			
			$("div#" + url).prepend(
				'<div class="window_drag" id="' + 'handle' + url + '"></div>'
			);
			
			var handleID = "#" + $("div#" + url + " div.window_drag").id();
			
			$("div#" + url + " div.window_drag").empty(); //clears the window header of any information
			
			//this inserts the close button and title into the window header
			$("div#" + url + " div.window_drag").append(
				"<b>" + title + "</b> <a href='#'>close</a><div style='clear:both; display:none;' />" //if you wish to make the close button and img, replace, the close within the <a> tags, with an <img> tage referencing the img
			); 
			
			//function for when the close button is clicked
			$("div#" + url + " div.window_drag" + " a").click(function(){
				$("div#" + url).fadeOut(400);	
			});
			
			//$(handleID).mousedown(function(){
				//levelWindow(url);
			//});
	
			$("div#" + url).Draggable({
				handle: handleID, 
				zindex:0
			});
			
			$(this).TransferTo({to: url, className:'window_transfer', duration: 300, complete:function(to){$(to).fadeIn(300);}})
			
		});
	
	}
	
	function calculateSizes(size, windowID) {

		var height = 250; //default window height
		var width = 500; //default window width
		
		//runs the code in the if dtatement if there is no user input size
		if (size.indexOf(",") != -1) {
				height = size.substring(size.indexOf(","), -4) -50; //window height defined by the inputted user size
				width = size.substring(size.indexOf(",")+1); //window width defined by the inputted user size
		}
		
		//runs the code in the if statement if the hight is not equal to 0
		if (height > 0) {
			
			$("#"+windowID+" div.window_main").css({height:height+"px"});
			$("#"+windowID).css({width:width+"px"});
			
		}
			
		positionWindow(height, width, windowID);

	}
	
	function positionWindow(h,w, windowID) {
		
		var documentEl = document.documentElement;
		var documentWidth = window.innerWidth || self.innerWidth || (documentEl&&documentEl.clientWidth) || document.body.clientWidth; //obtains window width
		var documentHeight = window.innerHeight || self.innerHeight || (documentEl&&documentEl.clientHeight) || document.body.clientHeight; //obtains window height
		
		windowXpos = documentWidth/2 - (w/2); //calculates where the "left" value for the window 
		windowYpos = documentHeight/2 - (h/2) - 30; //calculates where the "top" value for the window
		
		$("#"+windowID).css({position:"absolute"});
		$("#"+windowID).css({left: windowXpos + "px"});
		$("#"+windowID).css({top:windowYpos + "px"});
	
	}