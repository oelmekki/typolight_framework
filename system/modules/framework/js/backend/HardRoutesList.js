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
 * Class HardRoutesList
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Javascript
 */
var HardRoutesList = new Class({
  Implements: [ Options, Events ],

  options: {
    element: 'mod_hardRoutesList',
  },

  initialize: function( options )
  {
    this.setOptions( options );
    this.$element         = $( this.options.element );
    $( 'routes-list' ).addLiveEvent( 'click', '.load-link', this.loadClicked.bind( this ) );
    this.prepare();
  },



  /**
   * Change the element's dom for js
   */
  prepare: function()
  {
    this.$element.getElements( '.route' ).each( function( item, i ){
      ( new Toggable({ element: item, name: 'toggable-route-' + i }) );
    });

  },



  /**
   * Load the route in the database
   */
  loadClicked: function( event )
  {
    event.preventDefault();
    var target = event.target;
    var url = target.get( 'href' ) + '&json=true';
    var request = new Request.JSON({ 
      url: url, 
      method: 'get',
      onComplete: function( json )
      {
        if ( json.result )
        {
          target.getParent( '.route' ).setStyle( 'opacity', 0.5 );
          target.destroy();
        }
      }.bind( this )
    }).send();
  }
});

window.addEvent( 'domready', function(){ 
  ( new HardRoutesList() );
});
