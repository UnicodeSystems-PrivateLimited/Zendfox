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
 * @package     global
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
(function($){
    $.fn.jaccordion=function(opts){
        var o={
            action:"click",
            active:"active",
            menu:false,
            collapse:false,
            speed:"slow",
            op:$.noop,
            opAll:$.noop
        };
        this.activate=function(ixs){
            if(ixs!=undefined){
                if($.isArray(ixs)){
                    for(var i=0;i<ixs.length;i++){
                        o.op(ixs[i]);
                    }
                }else{
                    i>0?o.op(ixs):o.opAll();
                }
            }
        };
        return this.each(function(){
            if(opts){
                $.extend(o,opts);
            }
            $this=$(this);
            var $pans=$this.children("div").css("display","none");
            var $heads=$this.children("h3");
            o.opAll=function(){
                for(var i=0;i<$pans.length;i++){
                    o.op(i);
                }
            };
            o.op=function(i){
                if(i<$pans.length){
                    $($heads[i]).addClass(o.active);
                    $($pans[i]).slideDown(o.speed);
                }
            };
            if(o.open!=undefined){
                o.op(o.open);
            }
            $heads.bind(o.action, function(evt){
                evt.preventDefault();
                var i=$.inArray(this, $heads);
                var $cp=$($pans[i]);
                var $ch=$(this);
                if(!o.menu){
                    $heads.not($ch).removeClass(o.active);
                    $pans.not($cp).slideUp(o.speed);
                }
                if(o.menu||o.collapse){
                    $ch.toggleClass(o.active);
                    $cp.slideToggle(o.speed);
                }else{
                    $ch.addClass(o.active);
                    $cp.slideDown(o.speed);
                }
            });
        });
    };
})(jQuery);