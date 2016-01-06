/* 
 * Zendfox Framework
 *
 * LICENSE
 *
 * This file is part of Zendfox.
 *
 * Zendfox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zendfox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Zendfox in the file LICENSE.txt.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @category    theme
 * @package     core_default
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
(function($){
    $.fn.jbanner=function(opt){
        var o={
            duration:2000,
            selected:'selected',
            hover:"hover",
            showIndex:true,
            useBannerImageOnIndex:false,
            indexImageSetting:{
                width:'50px',
                height:'50px'
            },
            transitionBegin:function(c,p){},
            transitionEnd:function(c,p){}
        };
        return this.each(function(){
            var $this=$(this);
            var playing=true;
            var started=false;
            var tId=null;            
            if(opt){
                $.extend(o, opt);
            }            
            $this.css('position','relative');
            var slides=$this.children();
            $(slides).css('position','absolute').css('top','0').css('left',0);
            var nadd=$(o.navigation).children().length==0;
            $(slides).fadeOut(1);
            for(var i=0;i<slides.length;i++){
                if(o.navigation!=undefined && nadd){
                    $(o.navigation).append('<span>'+(o.showIndex?(i+1):(o.useBannerImageOnIndex?'<img src="'+$(slides[i]).find('img').attr('src')+'" width="'+o.indexImageSetting.width+'" height="'+o.indexImageSetting.height+'" />':''))+'</span>');
                }
                $(slides[i]).css('z-index',(i+1));
            }
            var navBtns=$(o.navigation).children();
            var prev=slides.length-1;
            var cur=0;            
            var next=function(){
                if(!started){
                    clearTimeout(tId);
                    prev=cur;
                    cur=(cur+1>=slides.length?0:cur+1);
                    slideIt();
                }
            };
            var previous=function(){
                if(!started){
                    clearTimeout(tId);
                    prev=cur;
                    cur=(cur==0?(slides.length-1):cur-1);                    
                    slideIt();
                }
            };
            var absolute=function(index){
                clearTimeout(tId);
                if(cur!=index){
                    prev=cur;
                    cur=index;
                    slideIt();
                }
            };
            var play=function(){
                clearTimeout(tId);
                playing=true;
                if(o.controls!=undefined){
                    $(o.controls.play).removeClass(o.controls.pauseClass);
                }
                tId=setTimeout(next,o.duration);
            };
            var pause=function(){
                clearTimeout(tId);                
                playing=false;
                if(o.controls!=undefined){
                    $(o.controls.play).addClass(o.controls.pauseClass);
                }
            };
            var playPause=function(){                                        
                playing?pause():play();
            };
            var slideIt=function(){               
                started=true;
                o.transitionBegin(cur,prev);
                $(slides[prev]).css('z-index',slides.length);
                $(slides[cur]).css('z-index',slides.length+1);
                if(o.navigation){
                    $(navBtns).removeClass(o.selected);
                    $(navBtns[cur]).addClass(o.selected);
                }                
                $(slides[cur]).fadeIn('slow',function(){
                    o.transitionEnd(cur,prev);
                    started=false;
                    $(slides[prev]).fadeOut(1);                    
                    if(playing){
                        tId=setTimeout(next,o.duration);
                    }
                });
            };
            slideIt();            
            if(o.controls!=undefined){
                if(o.controls.container){
                    $cpc=$(o.controls.container).bind("mouseover",function(){
                        $(this).stop().animate({
                            opacity:1
                        },"slow");
                    });
                    $this.hover(function(){
                        $cpc.stop().animate({
                            opacity:1
                        },"slow");
                    }, function(){
                        $cpc.stop().animate({
                            opacity:0
                        },"slow");
                    });
                }
                if(o.controls.prev!=undefined){
                    $(o.controls.prev).click(previous);
                    $(o.controls.next).click(next);
                    $(o.controls.play).click(playPause);
                    $(o.controls.play,o.controls.next,o.controls.prev).hover(function(){
                        $(this).addClass(o.hover)
                        },function(){
                        $(this).removeClass(o.hover)
                        });
                }
            }
            $(navBtns).hover(function(){                
                absolute($.inArray(this, navBtns));
                pause();
            },function(){
                play();
            });
        });
    };
})(jQuery);