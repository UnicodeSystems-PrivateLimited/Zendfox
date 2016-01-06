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
    var o={     //Script Path
        script_base:'',            
        visible:true,
        setting:{
            // General options
            mode : "none",
            theme : "advanced",
            plugins : "pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
            // Theme options
            theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
            theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true

        // Example content CSS (should be your site CSS)
        //content_css : "css/content.css",

        // Drop lists for link/image/media/template dialogs
        //template_external_list_url : "lists/template_list.js",
        //external_link_list_url : "lists/link_list.js",
        //external_image_list_url : "lists/image_list.js",
        //media_external_list_url : "lists/media_list.js",

        // Replace values for the template plugin
        //template_replace_values : {
        //	username : "Some User",
        //	staffid : "991234"
        //}
        }
    };
    var methods = {
        init : function(opts) {
            return this.each(function(){
                if(opts){
                    $.extend(true,o,opts);
                }
                var $this = $(this),
                data = $this.data('foxEditor');
                if (!data) {                    
                    o.setting.script_url=o.script_base+'/tiny_mce.js';                    
                    $(this).data('foxEditor', {
                        target : $this
                    });
                }
            });
        },
        destroy : function() {
            return this.each(function(){
                var $this = $(this),
                data = $this.data('foxEditor');
                // Namespacing FTW
                $(window).unbind('.foxEditor');
                if(data.tooltip){
                    data.tooltip.remove();
                }
                $this.removeData('foxEditor');
            })
        },     
        show : function(){
            var $this=$(this);            
            if(typeof $this.tinymce != 'function'){
                $('head').append('<script type="text/javascript" src="'+o.script_base+'/jquery.tinymce.js"></script>');                
            }
            var data=$this.data('foxEditor');            
            if(data.inited==undefined){
                $this.tinymce(o.setting);                
                data.inited=true;
            }else{
//                $this.tinymce().show(); 
                  tinymce.execCommand('mceAddControl',true,$this.attr('id'));
            }
        },
        hide : function(){
            var $this=$(this);
            if($this.data('foxEditor').inited){
//                $this.tinymce().hide();
                tinymce.execCommand('mceRemoveControl',true,$this.attr('id'));
            }
        }  
    };
    $.fn.foxEditor = function(method){    
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.foxEditor' );
        }
        return null;
    };
})( jQuery );