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
 * Class Toggable
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Javascript
 */
var Toggable = new Class({
  Implements: [ Options, Events ],

  options: {
    name: 'toggable',
    element: '',
    toggable_switch: '.toggable-switch',
    toggable_body: '.toggable-body',
    toggable_switch_display: 'inline'
  },

  initialize: function( options )
  {
    this.setOptions( options );

    /* attributes */
    this.$element         = $( this.options.element );
    this.$toggable_switch = this.$element.getElement( this.options.toggable_switch );
    this.$toggable_body   = this.$element.getElement( this.options.toggable_body );
    this.pagename         = window.location.href.match( /\/(.*?)\.(html|php)/ )[1];
    this.cookie_name      = this.pagename + '_' + this.options.name;
    this.toggle_state     = 'hidden';
    this.show_text         = 'Show';
    this.hide_text         = 'Hide';

    var class_str = this.$toggable_switch.get( 'class' );
    classes = class_str.split( ' ' );
    classes.each( function( item ){
      if ( ( match = item.match( /show-(.*)/ ) ) )
      {
        this.show_text = match[1];
      }

      else 
      {
        if ( ( match = item.match( /hide-(.*)/ ) ) )
        {
          this.hide_text = match[1];
        }
      }
    }.bind( this ));

    /* fx */
    this.toggle_fx = new Fx.Morph( this.$toggable_body, { link: 'cancel' });


    /* events */
    this.$toggable_switch.addEvent( 'click', this.toggle.bind( this ) );
    this.toggle_fx.addEvent( 'complete', this.toggleCompleted.bind( this ) );


    this.prepare();
  },



  /**
   * Change the element's dom for js
   */
  prepare: function()
  {
    this.$toggable_switch.setStyle( 'display', this.options.toggable_switch_display );
    var cookie = Cookie.read( this.cookie_name );
    if ( cookie )
    {
      this.toggle_state = cookie;
    }

    if ( this.toggle_state == 'hidden' )
    {
      this.toggle_fx.set({opacity: 0});
      this.$toggable_body.setStyle( 'display', 'none' );
    }

    else
    { 
      this.$toggable_switch.set( 'text', this.hide_text );
    }

  },



  /**
   * Toggle the content
   */
  toggle: function( event )
  {
    event.preventDefault();

    if ( this.toggle_state == 'hidden' )
    {
      this.$toggable_switch.set( 'text', this.hide_text );
      this.$toggable_body.setStyle( 'display', 'block' );
      this.toggle_fx.start({ 'opacity': 1 });
      Cookie.write( this.cookie_name, 'shown' );
      this.toggle_state = 'shown';
    }

    else
    {
      this.$toggable_switch.set( 'text', this.show_text );
      this.toggle_fx.start({ 'opacity': 0 });
      Cookie.write( this.cookie_name, 'hidden' );
      this.toggle_state = 'hidden';
    }
  },



  /**
   * Hide the content if faded out
   */
  toggleCompleted: function( event )
  {
    if ( this.$toggable_body.getStyle( 'visibility' ) == 'hidden' )
    {
      this.$toggable_body.setStyle( 'display', 'none' );
    }
  }
});
