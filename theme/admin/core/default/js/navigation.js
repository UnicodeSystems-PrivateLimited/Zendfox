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
;(function($){
	$.fn.navigation = function(navObj){

		var nav = $.fn.navigation,
			$arrowObj = $(['<span class="',nav.options.arrowClass,'"> &#187;</span>'].join('')),
			mouseOver = function(){
				var $$ = $(this), mainMenu = getNavigationMenu($$);
				clearTimeout(mainMenu.navTimer);
				$$.showNavigation().siblings().hideNavigation();
			},
			mouseOut = function(){
				var $$ = $(this), mainMenu = getNavigationMenu($$), obj = nav.configOptions;
				clearTimeout(mainMenu.navTimer);
				mainMenu.navTimer=setTimeout(function(){
					obj.retainPath=($.inArray($$[0],obj.$path)>-1);
					$$.hideNavigation();
					if (obj.$path.length && $$.parents(['li.',obj.hoverClass].join('')).length<1){mouseOver.call(obj.$path);}
				},obj.delay);	
			},
			getNavigationMenu = function($menu){
				var mainMenu = $menu.parents(['ul.',nav.options.menuClass,':first'].join(''))[0];
				nav.configOptions = nav.obj[mainMenu.serial];
				return mainMenu;
			},
			navigationArrow = function($a){$a.addClass(nav.options.anchorClass).append($arrowObj.clone());};
			
		return this.each(function() {
			var serialLen = this.serial = nav.obj.length;
			var obj = $.extend({},nav.defaultVars,navObj);
			obj.$path = $('li.'+obj.pathClass,this).slice(0,obj.pathLevels).each(function(){
				$(this).addClass([obj.hoverClass,nav.options.breadCrumbClass].join(' '))
					.filter('li:has(ul)').removeClass(obj.pathClass);
			});
			nav.obj[serialLen] = nav.configOptions = obj;
			
			$('li:has(ul)',this)[($.fn.hoverIntent && !obj.disableHI) ? 'hoverIntent' : 'hover'](mouseOver,mouseOut).each(function() {
				if (obj.autoArrows) navigationArrow( $('>a:first-child',this) );
			})
			.not('.'+nav.options.breadCrumbClass)
				.hideNavigation();
			
			var $a = $('a',this);
			$a.each(function(i){
				var $li = $a.eq(i).parents('li');
				$a.eq(i).focus(function(){mouseOver.call($li);}).blur(function(){mouseOut.call($li);});
			});
			obj.onInit.call(this);
			
		}).each(function() {
			var menuClasses = [nav.options.menuClass];
			if (nav.configOptions.dropShadows  && !($.browser.msie && $.browser.version < 7)) menuClasses.push(nav.options.shadowClass);
			$(this).addClass(menuClasses.join(' '));
		});
	};

	var nav = $.fn.navigation;
	nav.obj = [];
	nav.configOptions = {};
	nav.IE7fix = function(){
		var o = nav.configOptions;
		if ($.browser.msie && $.browser.version > 6 && o.dropShadows && o.animation.opacity!=undefined)
			this.toggleClass(nav.options.shadowClass+'-off');
		};
	nav.options = {
		menuClass       : 'nav-js-enabled',
		anchorClass     : 'nav-with-ul',
		arrowClass      : 'nav-sub-indicator',
		shadowClass     : 'nav-shadow',
		breadCrumbClass : 'nav-breadcrumb'
	};
	nav.defaultVars = {
		hoverClass	: 'navHover',
		pathClass	: 'overideThisToUse',
		pathLevels	: 1,
		delay		: 800,
		animation	: {opacity:'show'},
		speed		: 'normal',
		autoArrows	: true,
		dropShadows     : true,
		disableHI	: false,		// true disables hoverIntent detection
		onInit		: function(){}, // callback functions
		onBeforeShow    : function(){},
		onShow		: function(){},
		onHide		: function(){}
	};
	$.fn.extend({
		hideNavigation : function(){
			var obj = nav.configOptions,
				not = (obj.retainPath===true) ? obj.$path : '';
			obj.retainPath = false;
			var $ul = $(['li.',obj.hoverClass].join(''),this).add(this).not(not).removeClass(obj.hoverClass)
					.find('>ul').hide().css('visibility','hidden').find('ul').addClass('subsub').parent('li').addClass('liZ').siblings('li').addClass('liZ');
					
			obj.onHide.call($ul);
			return this;
		},
		showNavigation : function(){
			var obj = nav.configOptions,
				sh = nav.options.shadowClass+'-off',
				$ul = this.addClass(obj.hoverClass)
					.find('>ul:hidden').css('visibility','visible');
			nav.IE7fix.call($ul);
			obj.onBeforeShow.call($ul);
			$ul.animate(obj.animation,obj.speed,function(){nav.IE7fix.call($ul);obj.onShow.call($ul);});
			return this;
		}
	});

})(jQuery);
