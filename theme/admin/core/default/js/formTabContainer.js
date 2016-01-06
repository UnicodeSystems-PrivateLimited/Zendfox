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
    $.fn.foxFormTabContainer=function(opt){
        var o={
            open:$.noop
        };        
        return this.each(function(){
            if(opt){
                $.extend(true,o,opt);
            }            
            var $this=$(this);
            var $tabs=$('ul.tabs li.tab',$this);
            var $pans=$('.form_cont div.tab',$this).css('display','none');
            $tabs.first().addClass("selected");
            $pans.first().css('display','block');
            $tabs.click(function(){
                var sIndx=$.inArray(this, $tabs);
                o.open(sIndx);
            });
            if(o.formObj){
                o.formObj.onAllErrors=function(els){
                    var tIndxs=new Array();
                    $tabs.removeClass('error');
                    if(els){
                        els.each(function(i,el){
                            var eIndx=$.inArray($(el).parents('.form_cont div.tab').get(0),$pans);
                            if(eIndx>=0 && $.inArray(eIndx, tIndxs)){
                                tIndxs[tIndxs.length]=eIndx;
                                $tabs.eq(eIndx).addClass('error');
                            }
                        });
                        if(tIndxs.length>0){
                            o.open(tIndxs[0]);
                        }
                    }
                };
            }
            o.open=function(i){
                if(i>-1 && i<$tabs.length){
                    var $vPan=$pans.eq(i).css('display','block');
                    $pans.not($vPan).css('display','none');
                    $tabs.removeClass("selected");
                    $tabs.eq(i).addClass("selected");
                }
            };
        });
    }
})(jQuery);