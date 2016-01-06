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
    $.fn.foxTable=function(opt){
        var o={
            url:'',
            pageCount:0,
            page:0,
            recCount:0,
            params:{},
            sortDir:'ASC'
        };
        return this.each(function(){
            if(opt){
                $.extend(o,opt);
            }
            var $this=$(this);
            var $cpNavBtns=$('.cp_btn_nav .btn_cp',$this);
            $cpNavBtns.filter('.disable').css('cursor','not-allowed');
            $cpNavBtns=$cpNavBtns.not('.disable');
            var $headers=$('table.table_data tr.table_header td a',$this);
            var $fEles=$('table.table_data thead tr.table_sec_header',$this).find(':input');
            var $rows=$('table.table_data tbody tr',$this);
            var selCount=0;
            //            o.selected=o.params['selected'];
            //            o.extra=o.params['extra'];
            for(var f in o.params.filters){
                //alert('Found Filter:'+'[name="filters['+f+']"]')
                if($.isPlainObject(o.params.filters[f])){
                    //alert("Json");
                    $fEles.filter('[name="filters['+f+'][from]"]').val(o.params.filters[f].from);
                    $fEles.filter('[name="filters['+f+'][to]"]').val(o.params.filters[f].to);
                }else{
                    $fEles.filter('[name="filters['+f+']"]').val(o.params.filters[f]);
                }
            }
            if(o.params['sort']){
                $headers.filter('[name="'+o.params['sort']+'"]').addClass(o.params['dir'].toUpperCase()=='ASC'?'asc':'desc');
            }
            /*************************Selection Area************************/   
            if(o.params.selected!=undefined){           
                if(o.params.extra==undefined){
                    o.params.extra=[];
                }
                //alert((o.params.selected?"| All":"| None")+' | Excluded:'+$.jdebug(o.params.extra));
                $('.groupaction_check :checkbox',$this).each(function(i,chk){
                    var inArr=$.inArray($(chk).val(),o.params.extra);
                    //                    alert('hello:'+(inArr));                    
                    if(o.params.selected){
                        $(chk).attr('checked',inArr<0);
                    }else{
                        $(chk).attr('checked',inArr>-1);                        
                    }                    
                });  
                manageExtra(false);
            }
            $('#tableGroupAction',$this).change(function(){
                $('div.groupaction-dependent').css('display','none');
                $('#groupaction_' + $(this).val() + '_dependent',$this).css('display','inline');
            });
            $('.table_cp form .form-button',$this).click(function(){
                var act=$('#tableGroupAction',$this).val();
                var grUrl=(o.groupAction[act]!=undefined && o.groupAction[act]['url']!=undefined)?o.groupAction[act]['url']:'';
                if(selCount>0){
//                    if(grUrl!=''){
                    if($('.table_cp form#groupActionForm').foxForm({visible:true}).validate()){
                        var allow=true;
                        if(o.groupAction[act]['confirm']!=undefined){
                            allow=confirm(o.groupAction[act]['confirm']);
                        }
                        if(allow){
                            //                    alert('Action:'+grUrl+(o.params.selected?"| All":"| None")+' | Excluded:'+$.jdebug(o.params.extra));
                            if(o.groupAction[act]['dependent']){
                                o.params['dependents']={};
                                for(var i=0;i<o.groupAction[act]['dependent'].length;i++){
                                    o.params.dependents[o.groupAction[act]['dependent'][i]['name']]=$('#dependent-'+act+'-'+o.groupAction[act]['dependent'][i]['name'],$this).val();
                                }
                            }
                            if(o.params.selected==undefined){
                                o.params.selected=0;
                            }
                            o.params['colTypes']=o.colTypes;
                            $.get(grUrl+'/isAjax/1', o.params, function(res){
                                if(res.error){
                                    alert(res.error);
                                }else if(res.redirect){
                                    window.location.href=res.redirect;
                                }
                            }, 'json');
                        }
                    }else{
                       // alert("Please select action !");
                    }
                }else{
                    alert("Please select record !");
                }
            });
            $('.groupaction_check,.groupaction_check :checkbox',$this).click(function(e){
                e.stopPropagation();  
                var $chk;
                if($(this).is(':checkbox')){                
                    $chk=$(this);                
                }else{
                    $chk=$(':checkbox',$(this));  
                    $chk.attr('checked',!$chk.attr('checked'));
                }                    
                manageSelect($chk);
            });            
            $('.cp_btn_select .all',$this).click(function(){
                $('.groupaction_check :checkbox',$this).attr('checked',true);
                o.params.selected=1;
                o.params.extra=[];
                manageExtra(false);
            });
            $('.cp_btn_select .current',$this).click(function(){                
                $('.groupaction_check :checkbox',$this).attr('checked',true).each(function(i,chk){                    
                    manageExtra($(chk).val(),!o.params.selected);
                });  
            });
            $('.cp_btn_select .none',$this).click(function(){
                $('.groupaction_check :checkbox',$this).attr('checked',false); 
                o.params.selected=0;
                o.params.extra=[];
                manageExtra(false);
            });
            $('.cp_btn_select .current-none',$this).click(function(){
                $('.groupaction_check :checkbox',$this).attr('checked',false).each(function(i,chk){                    
                    manageExtra($(chk).val(),o.params.selected);                                    
                });                
            });
            function resetSelection(){
                o.params.selected=undefined;
                o.params.extra=undefined;
            }
            /***********************************Selection End**************************************/
            /***********************************Export Action**************************************/
            $('form#exportForm',$this).submit(function(e){
                e.preventDefault();
                o.params.exportType=$(this).children('select').val();
                var expUrl=$(this).attr('action');
                if($.trim(o.params.exportType)!=''){
//                    alert($.param(o.params));
                window.open(expUrl+"?"+$.param(o.params), "_self", false);
//                    $.get(expUrl+'/isAjax/1', o.params, function(res){
//                                if(res.error){
//                                    alert(res.error);
//                                }else if(res.redirect){
//                                    window.location.href=res.redirect;
//                                }
//                            }, 'html');
                }else{
                    alert('Select export type first!');
                }
            });
            /***********************************Export End*****************************************/
            $headers.click(function(e){
                e.preventDefault();
                var sort=$(this).attr('name');
                o.params['dir']=(o.params['sort']&&(o.params['sort']==sort)&& o.params['dir'] && o.params['dir'].toUpperCase()=='ASC')?'DESC':'ASC';
                o.params['sort']=sort;
                loadPage(o.page);
            });
            $cpNavBtns.hover(function(e){
                $(this).addClass('hover');
            }, function(e){
                $(this).removeClass('hover');
            });
            $cpNavBtns.filter('.first').click(function(){
                loadPage(1);
            });
            $cpNavBtns.filter('.pre').click(function(){
                loadPage(o.page-1);
            });
            $cpNavBtns.filter('.next').click(function(){
                loadPage(o.page+1);
            });
            $cpNavBtns.filter('.last').click(function(){
                loadPage(o.pageCount);
            });
            $('.form-button.search',$this).click(function(){
                setFilter();
            });
            $('.form-button.reset',$this).click(function(){
                resetFilter();
            });
            $rows.hover(function(){
                $(this).addClass('hover');
            }, function(){
                $(this).removeClass('hover');
            });
            $rows.click(function(e){
                window.open($(this).attr('title'),'_self',false);
            }).find('a').click(function(e){
                e.stopPropagation();                
            });
            $('select#itemCount',$this).change(function(){
                o.recCount=$(this).val();
                loadPage(o.page);
            });
            $('.table_cp input.page_no',$this).keyup(function(e){
                if(e.keyCode==13){
                    if(!isNaN($(this).val())){
                        loadPage($(this).val());
                    }
                }
            });
            $fEles.keyup(function(e){
                if(e.keyCode==13){
                    //if($.trim($(this).val())!=''){
                        setFilter();
                    //}
                }
            });
            function manageSelect($chk){                
                if(!o.params.selected){
                    manageExtra($chk.val(),$chk.attr('checked'));
                }else{                    
                    manageExtra($chk.val(),!$chk.attr('checked'));
                }                
            }
            function manageExtra(val,add){
                if(val){
                    if(o.params.selected==undefined){
                        o.params.selected=0;
                    }
                    if(o.params.extra==undefined){
                        o.params.extra=[];
                    }
                    if(add){                    
                        var i;                        
                        for(i=0;i<o.params.extra.length;i++){
                            if(val==o.params.extra[i]){                            
                                break;
                            }
                        }
                        if(i==o.params.extra.length){
                            o.params.extra[o.params.extra.length]=val;
                        }
                    }else{
                        var temp=[];
                        for(var i=0;i<o.params.extra.length;i++){
                            if(val!=o.params.extra[i]){                            
                                temp[temp.length]=o.params.extra[i];
                            }
                        }
                        o.params.extra=temp;
                    } 
                }
                //o.selected?(o.totalRec-o.extra.length):(o.totalRec-o.extra.length);
                selCount=(o.params.selected?(o.totalRec-o.params.extra.length):o.params.extra.length);
                $('.table_cp .rec_count').text(selCount);
            }
            function resetFilter(){
                o.params.filters=undefined;
                resetSelection();
                loadPage(o.page);
            }
            function setFilter(){
                o.params.filters=undefined;
                resetSelection();
                $fEles.each(function(){
                    var v=$.trim($(this).val());
                    if(v.length>0){
                        //alert('Adding Filter:'+$(this).attr('name')+'|'+v);
                        o.params[$(this).attr('name')]=v;
                    }
                });
                loadPage(o.page);
            }
            function loadPage(page){                
                $.get(o.url+'/isAjax/1/page/'+page+'/recCount/'+o.recCount, o.params, function(res){
                    $this.html(res);
                }, 'html')
            }
        });
    }
})(jQuery);