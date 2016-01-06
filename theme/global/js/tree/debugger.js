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
$.jdebug=function(obj){
    var dStr="";
    function d(obj){
        var tStr="";
        if($.isArray(obj)){
            for(var i=0;i<obj.length;i++){
                tStr=tStr+(i>0?", ":"[")+($.isArray(obj[i])||isJsonObject(obj[i])?d(obj[i]):obj[i]);
            }
            tStr=tStr+"]";
        }else if(isJsonObject(obj)){
            var i=0;
            for(var prop in obj){
                tStr=tStr+(i++>0?", ":"{")+prop+" : "+($.isArray(obj[prop])||isJsonObject(obj[prop])?d(obj[prop]):obj[prop]);
            }
            tStr=tStr+"}";
        }else{
            tStr=tStr+obj;
        }
        return tStr;
    }
    function isJsonObject( obj ) { 
		// Must be an Object.
		// Because of IE, we also have to check the presence of the constructor property.
		// Make sure that DOM nodes and window objects don't pass through, as well
		if ( !obj || obj.toString() !== "[object Object]" || obj.nodeType || obj.setInterval ) {
			return false;
		}

		// Not own constructor property must be Object
		if ( obj.constructor
			&& !obj.hasOwnProperty("constructor")
			&& !obj.constructor.prototype.hasOwnProperty("isPrototypeOf")) {
			return false;
		}

		// Own properties are enumerated firstly, so to speed up,
		// if last one is own, then all properties are own.

		var key;
		for ( key in obj ) {}

		return key === undefined || obj.hasOwnProperty( key );
	}
        dStr=d(obj);
    return dStr;
};