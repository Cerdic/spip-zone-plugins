/* jDemo - a jQuery plugin to make great online demos
 * version 0.1 (8/6/2007)
 * 
 * Copyright (c) 2007 - Renato Formato <renatoformato@virgilio.it>
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */ 

(function($) {
  
  $.startDemo = function(url,name,step,path_resources) {
    jDemo.path_resources = path_resources;
    jDemo.loadDemo(url,name,step);
  };
  
  var jDemo = {
    xmlData: null,
    actionsToRevert: [],
    path_resources: "",
    queues: {},
    init: function(url,name,step) {
      $.get(url,function(data){
        jDemo.xmlData = {data:data,steps:{}};
        jDemo.loadDemo(url,name,step);
      });
    },
    loadDemo: function(url,name,step) {
      if(!jDemo.xmlData) 
        return jDemo.init(url,name);
      if(!jDemo.xmlData.steps[name]) {
        var $demo = $("demo[@name="+name+"]",jDemo.xmlData.data);
        if(!$demo.size()) 
          return alert("Demo non trovata");
        jDemo.xmlData.steps[name] = $('step',$demo);
      }    
      $steps = jDemo.xmlData.steps[name]; 
      
      if(!step) step = 1;
      step--;
      $step = $steps.eq(step);
      if(!$step.size())
        return alert("Step non trovato");
      var title = $('title',$step).text();
      var content = $('content',$step).text();
      var res = '<div id="jDemo_window" class="jqmWindow">'+
                (title?'<h3>'+title+'</h3>':'')+
                (content?'<div class="jDemo_content">'+content+'</div>':'')+
                '<div class="jDemo_footer">';
      
      if(step!=0) 
        res += ' <a rel="'+name+'&'+step+'" href="#" id="jDemo_prev">Precedente</a>';
  
      if(step!=$steps.size()-1) 
        res += ' <a rel="'+name+'&'+(step+2)+'" href="#" id="jDemo_next">Successivo</a>';
  
      res += ' <a href="#" class="jqmClose">Chiudi</a></div></div>';
      $('body').append(res);
      $('#jDemo_prev').click(jDemo.navigate);
      $('#jDemo_next').click(jDemo.navigate);
      $('#jDemo_window').jqm({overlay:0,onHide:function(hash){hash.w.hide();jDemo.revertActions();}}).jqmShow();
      jDemo.doActions($step);
    },
    doActions: function($step) {
      var $actions = $('actions>',$step);
      $actions.each(function(){
        $el = $(this);
        switch(this.tagName) {
          case 'highlight':
            var $target = $($el.attr('selector'));
            var curr_color = $target.css('background-color');
            jDemo.actionsToRevert.push(function() {$target.css('background-color',curr_color)});
            $target.css('background-color',$el.attr('color') || 'yellow');
            break;
          case 'reversehighlight':
            if(!$.fn.offset) {
              alert("E' necessario il plugin dimensions");
              break;
            }
            var $target = $($el.attr('selector'));
            var offset = $target.offset({scroll:false});
            offset.height = $target.height();
            offset.bottom = offset.top+offset.height;
            offset.right = offset.left+$target.width();
            var $overlay_base = $('<div class="jqmOverlay"></div>').css({position:'absolute','z-index':3000-1,opacity:0.5});
            var $overlay = $([]).add($overlay_base.clone().css({top:'0px',left:'0px',width:$(document).width(),height:offset.top})).
            add($overlay_base.clone().css({top:offset.bottom,left:'0px',width:$(document).width(),height:$(document).height()-offset.bottom})).
            add($overlay_base.clone().css({top:offset.top,left:'0px',width:offset.left,height:offset.height})).
            add($overlay_base.clone().css({top:offset.top,left:offset.right,width:$(document).width()-offset.right,height:offset.height}));
            jDemo.actionsToRevert.push(function() {$overlay.remove();});
            $("body").append($overlay);
            break;
          case 'mouse':
            if(!$.fn.offset) {
              alert("E' necessario il plugin dimensions");
              break;
            }
            var queue = [];
            $(">",this).each(function(){
              $cmd = $(this);
              switch(this.tagName) {
                case "set":
                  var $target = $($cmd.attr('selector'));
                  var offset = $target.offset({scroll:false});
                  $mouse = $('<img id="jDemo_pointer" src="'+jDemo.path_resources+'Cursor.png">').css({position:'absolute',left:offset.left,top:offset.top,zIndex:3000+2});
                  queue.push(function(){$("body").append($mouse);jDemo.executeQueue("mouse");});
                  jDemo.actionsToRevert.push(function() {$mouse.remove();});
                  break;
                case "move":
                  var $target = $($cmd.attr('selector'));
                  var speed = 1000/($cmd.attr('speed') || 100);
                  var offset = $target.offset({scroll:false});
                  queue.push(function(){
                    var offsetPointer = $('#jDemo_pointer').offset({scroll:false});
                    var distance = Math.sqrt(Math.pow(offset.top-offsetPointer.top,2)+Math.pow(offset.left-offsetPointer.left,2));
                    $('#jDemo_pointer').animate({top:offset.top,left:offset.left},parseInt(distance*speed),function(){jDemo.executeQueue("mouse");});
                    });
                  break;
                 case "click":
                    queue.push(function(){
                      var offset = $('#jDemo_pointer').offset({scroll:false});
                      var $click = $('<img id="jDemo_click" src="'+jDemo.path_resources+'CursorClick.png">').css({position:'absolute',left:offset.left,top:offset.top,zIndex:3000+1});
                      $("body").append($click);
                      jDemo.actionsToRevert.push(function() {$click.remove();});
                      jDemo.executeQueue("mouse");
                    });
              }
            });
            jDemo.queues["mouse"] = queue;
            jDemo.executeQueue("mouse");
            break;
        }
      });
    },
    revertActions : function() {
      var action;
      while(action = jDemo.actionsToRevert.pop()) {
        action();
      }      
    },
    navigate: function() {
      $('#jDemo_window').jqmHide().remove();
      jDemo.revertActions();
      var data = this.rel.split(/&/);
      jDemo.loadDemo('',data[0],data[1]);
      return false;    
    },
    executeQueue: function(queue) {
      var f = jDemo.queues[queue].shift();
      if(f) f(); 
    }
  }
  
})(jQuery)
