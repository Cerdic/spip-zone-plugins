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
    css:{},
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
      $step = $steps.filter('[@num='+step+']');
      step--;
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
      
      $window = $step.find('window');
      $jDemoWindow = $('#jDemo_window'); 
      if($window.size()) {
        var $css = $window.find('css');
        var css = {};
        $css.find('>rule').each(function(){
          var $rule = $(this);
          css[$rule.attr('name')] = $rule.attr('val'); 
        });
        $.extend(jDemo.css,css);
      }
      $jDemoWindow.css(jDemo.css);  

      
      $('#jDemo_prev').click(jDemo.navigate);
      $('#jDemo_next').click(jDemo.navigate);
      $jDemoWindow.jqm({overlay:0,onHide:function(hash){
        hash.w.hide();
        jDemo.stopLoops();
        jDemo.revertActions();
      }}).jqmShow();
      jDemo.doActions($step);
    },
    doActions: function($step) {
      var $actions = $('actions>',$step);
      $actions.each(function(){
        $el = $(this);
        var revert = ($el.attr("revert")=="false"?false:true); 
        switch(this.tagName) {
          case 'highlight':
            var $target = $($el.attr('selector'));
            var curr_color = $target.css('background-color');
            if(revert) jDemo.actionsToRevert.push(function() {$target.css('background-color',curr_color)});
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
            offset.totalHeight = Math.max($(window).height(),$(document).height());
            var $overlay = $('#jDemo_overlay');
            if(!$overlay.size()) {
              var $overlay_base = $('<div class="jqmOverlay"></div>').css({position:'absolute','z-index':3000-1,opacity:0.5});
              $overlay = $('<div id="jDemo_overlay">').append($overlay_base.clone().css({top:'0px',left:'0px',width:$(document).width(),height:offset.top})).
              append($overlay_base.clone().css({top:offset.bottom,left:'0px',width:$(document).width(),height:offset.totalHeight-offset.bottom})).
              append($overlay_base.clone().css({top:offset.top,left:'0px',width:offset.left,height:offset.height})).
              append($overlay_base.clone().css({top:offset.top,left:offset.right,width:$(document).width()-offset.right,height:offset.height}));
            }
            if(revert) jDemo.actionsToRevert.push(function() {$overlay.remove();});
            $("body").append($overlay);
            break;
          case 'mouse':
            if(!$.fn.offset) {
              alert("E' necessario il plugin dimensions");
              break;
            }
            var qName = 'mouse'+$step.attr('num');
            var queue = [];
            $(">",this).each(function(){
              $cmd = $(this);
              switch(this.tagName) {
                case "set":
                  var $target = $($cmd.attr('selector'));
                  var offset = $target.offset({scroll:false});
                  queue.push(function(){
                    jDemo.queues[qName].target = $target;
                    $mouse = $('<img id="jDemo_pointer" src="'+jDemo.path_resources+'Cursor.png">').css({position:'absolute',left:offset.left,top:offset.top,zIndex:3000+2});
                    $("body").append($mouse);
                    if(revert) jDemo.queues[qName].actionsToRevert.push(function() {$mouse.remove();});
                    jDemo.executeQueue(qName);
                  });
                  break;
                case "move":
                  var $target = $($cmd.attr('selector'));
                  var speed = 1000/($cmd.attr('speed') || 100);
                  var offset = $target.offset({scroll:false});
                  queue.push(function(){
                    jDemo.queues[qName].target = $target;
                    var offsetPointer = $('#jDemo_pointer').offset({scroll:false});
                    var distance = Math.sqrt(Math.pow(offset.top-offsetPointer.top,2)+Math.pow(offset.left-offsetPointer.left,2));
                    $('#jDemo_pointer').animate({top:offset.top,left:offset.left},parseInt(distance*speed),function(){jDemo.executeQueue(qName);});
                    });
                  break;
                 case "click":
                    var delay = $cmd.attr('delayEvent');
                    queue.push(function(){
                      var offset = $('#jDemo_pointer').offset({scroll:false});
                      var $click = $('<img id="jDemo_click" src="'+jDemo.path_resources+'CursorClick.png">').css({position:'absolute',left:offset.left,top:offset.top,zIndex:3000+1});
                      $("body").append($click);
                      if(revert) jDemo.queues[qName].actionsToRevert.push(function() {$click.remove();});
                      if(delay) {
                        var q = jDemo.queues[qName];
                        jDemo.executeDelayed(q,function(){                          
                            q.target.click();
                            jDemo.executeQueue(qName);
                        },delay*1000);
                      } else 
                        jDemo.executeQueue(qName);
                    });
                    break;
                case "pause":
                    var time = $cmd.attr('time')*1000 || 1000;
                    queue.push(function(){
                      jDemo.executeDelayed(jDemo.queues[qName],function(){jDemo.executeQueue(qName)},time);
                    });
                    break;
              }
            });
            jDemo.queues[qName] = {queue:queue,loop:($(this).attr("loop") || false),index:0,timeOutHandle:[],actionsToRevert:[]};
            jDemo.executeQueue(qName);
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
      var data = this.rel.split(/&/);
      jDemo.loadDemo('',data[0],data[1]);
      return false;    
    },
    executeQueue: function(queue) {
      var q = jDemo.queues[queue];
      if(!q) return;
      var f = q.queue[q.index];
      q.index++;
      if(f) 
        f();
      else 
        if(q.loop) {
          q.index = 0;
          var f = function() {
            var action;
            while(action = q.actionsToRevert.pop())
              action();
            jDemo.executeQueue(queue)
          }; 
          jDemo.executeDelayed(q,f,1000);
        }
    },
    stopLoops: function() {
      $.each(jDemo.queues,function(i,n){
        if(n) {
          var handle,action;
          while(handle = n.timeOutHandle.pop())
            window.clearTimeout(handle);
          while(action = n.actionsToRevert.pop())
            action();  
        }
        jDemo.queues[i] = null;
      })
    },
    executeDelayed: function(queue,f,delay) {
      if(queue) queue.timeOutHandle.push(window.setTimeout(f,delay));
    }
  }
})(jQuery)
