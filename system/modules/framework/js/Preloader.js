/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Framework 
 * @license    LGPL 
 * @filesource
 */


/**
 * Class Preloader
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Javascript
 */
var Preloader = new Class({
  Implements: [ Options ],

  options: {
    preloader_class: 'preloader',
    images: []
  },

  initialize: function( options )
  {
    this.setOptions( options  );
    var preloader = new Element( 'div', {
      'class': this.options.preloader_class,
      'styles': { 'display': 'none' }
    });

    var body = $$( 'body' )[0];
    preloader.inject( body );
    
    this.options.images.each( function( src ){
      var image = new Element( 'img', { 'src': src });
      image.inject( preloader );
    });
  }
});
