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
    $.fn.jvalidator=function(opts){
        var o={
            errorClass:"error",
            addAfter:true,
            skipLabel:true,
            errorMsg:{
                tag:"p",
                className:"errMsg"
            },
            validation:{
                required:{
                    pat:/\S/,
                    msg:"Required Entry"
                },
                url:{
                    pat:/^(((ht|f)tp(s?))\:\/\/)([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}(\:[0-9]+)?(\/\S*)?$/,
                    msg:"Invalid URL"
                },
                alnum:{
                    pat:/^[0-9a-z]*$/i,
                    msg:"Alfa-Numeric Only"
                },
                number:{
                    pat:/^-?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/,
                    msg:"Decimal Numbers Only"
                },
                integer:{
                    pat:/^[-]{0,1}[0-9]*$/,
                    msg:"Numbers Only"
                },
                email:{
                    pat:/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.([a-z]){2,4})$/,
                    msg:"Invalid email"
                }
            },
            rad:{},
            vIt:$.noop
        };
        this.validate=function (){
            return o.vIt($(this));
        };
        return this.each(function(){
            var $f; 
            if(opts){
                $.extend(o,opts);
            }
            var bL=o.skipLabel;
            var af=o.addAfter;
            var $this=$(this);
            $this.bind("submit", function(evt){                
                return o.vIt($(this));
            });
            o.vIt=function($cnt){
                var aft=$.isFunction(o.afterEach);
                var bfr=$.isFunction(o.beforeEach);
                if($.isFunction(o.before)){
                    o.before($cnt);
                }
                $f=undefined;
                var valid=true;
                var $eles=$(":input",$cnt);
                var v=o.validation;
                for(var i=0;i<$eles.length;i++){
                    var $ele=$($eles[i]);
                    bfr && o.beforeEach($ele);
                    if(chkCond($ele)){
                        if($ele.filter(".required:radio,.required:checkbox").length>0){
                            var nm=$ele.attr("name");
                            o.rad[nm]=o.rad[nm]?$(o.rad[nm]).add($ele):$ele;
                        }else{
                            if($ele.attr("class").indexOf("required")>-1){
                                var vres=isValid($ele);
                                valid=valid && vres;
                            }else{
                                if(v.required.pat.test($ele.val())){
                                    vres=isValid($ele);
                                    valid=valid && vres;
                                }else{
                                    remErr($ele);
                                }
                            }
                        }
                    }else{
                        remErr($ele);
                    }
                    aft && o.afterEach($ele,valid);
                }
                for(var p in o.rad){
                    var $e=o.rad[p];
                    if($e.length){
                        bfr && o.beforeEach($e);
                        if(!$e.is(":checked")){
                            addErr($e,v.required);
                            vres=false;
                        }else{
                            remErr($e);
                            vres=true;
                        }
                        aft && o.afterEach($e,vres);
                    }
                }
                valid=valid&&vres;                
                valid || $f.focus();
                if($.isFunction(o.after)){
                    o.after($cnt,valid);
                }
                return valid;
            };
            function chkCond($e){
                var cn=o.conditional;
                if(cn){
                    if($.isArray(cn)){
                        for(var i=0;i<cn.length;i++){
                            if(isIn($e,cn[i])){
                                return toV(cn[i]);
                            }
                        }
                        return i==cn.length;
                    }else{
                        if(isIn($e,cn)){
                            return toV(cn);
                        }else{
                            return true;
                        }
                    }
                }else{
                    return true;
                }
            }
            function isIn($e,cn){
                return $(cn.context+" :input").index($e)>-1;
            }
            function toV(cn){
                return $.isFunction(cn.validate)?cn.validate():cn.validate;
            }
            function last($e){
                return ($e&&$e.length>0?$e.eq($e.length-1):$e);
            }
            function isValid($ele){
                var cls=$ele.attr("class").split(/[\s]+/);
                var v=o.validation;
                for(var j=0;j<cls.length;j++){
                    if(v[cls[j]]!=undefined){
                        if(!v[cls[j]].pat.test($ele.val())){
                            addErr($ele,v[cls[j]]);
                            break;
                        }else{
                            remErr($ele)
                        }
                    }
                }
                return (j>=cls.length);
            }
            function addErr($e,v){
                if(!$f){
                    $f=$e;
                }
                $e.addClass(o.errorClass);
                $e=getTE($e);
                if(af){
                    last($e).next(errSel()).length>0 ||
                    last($e).after(getErrEle(v.msg));
                }else{
                    $e.eq(0).prev(errSel()).length>0 ||
                    $e.eq(0).before(getErrEle(v.msg));
                }
            }
            function remErr($e){
                $e.removeClass(o.errorClass);
                $e=getTE($e);
                if(af){
                    $e.next(errSel()).remove();
                }else{
                    $e.prev(errSel()).remove();
                }                
            }
            function getTE($e){
                if(bL){
                    var $l=$e.parent("label");
                    $e=$l.length>0?$l:$e;   
                } 
                return $e;
            }
            function getErrEle(m){
                return '<'+o.errorMsg.tag+' class="'+o.errorMsg.className+'">'+m+'</'+o.errorMsg.tag+'>';
            }
            function errSel(){
                return o.errorMsg.tag+"."+o.errorMsg.className;
            }            
        });
    };
})(jQuery);