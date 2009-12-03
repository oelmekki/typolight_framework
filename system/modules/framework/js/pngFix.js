/*
Script: FixPNG.js
	Extends the Browser hash object to include methods useful in managing the window location and urls.

License:
	http://www.clientcide.com/wiki/cnet-libraries#license
*/
$extend(Browser, {
  fixPNG: function(el) 
  {
    try 
    {
      if (Browser.Engine.trident4)
      {
        el = $(el);
        if (!el)
        {
          return el;
        }
        
        if (el.get('tag') == "img" ) 
        {
          var vis = ( el.getStyle( 'visibility' ) != 'hidden' && el.getStyle( 'display' ) != 'hidden' );
          var origin_styles = el.getStyles('top', 'left', 'right', 'bottom', 'margin', 'padding', 'vertical-align', 'text-align' );
          var msg = '';
          for ( style in origin_styles )
          {
            msg += style + ' : ' + origin_styles[ style ] + " \n";
          }

          try 
          { //safari sometimes crashes here, so catch it
            dim = el.getSize();

            if ( el.get( 'width' ) )
            {
              dim.x = el.get( 'width' );
            }

            if ( el.get( 'height' ) )
            {
              dim.y = el.get( 'height' );
            }
          }
          catch(e){}

          if (!vis)
          {
            var before = {};
            //use this method instead of getStyles 
            ['visibility', 'display', 'position'].each(function(style){
              before[style] = this.style[style]||'';
            }, this);
            //this.getStyles('visibility', 'display', 'position');
            this.setStyles({
              visibility: 'hidden',
              display: 'block',
              position:'absolute'
            });


            dim = el.getSize(); //works now, because the display isn't none
            if ( el.get( 'width' ) )
            {
              dim.x = el.get( 'width' );
            }

            if ( el.get( 'height' ) )
            {
              dim.y = el.get( 'height' );
            }


            this.setStyles(before); //put it back where it was
            el.setStyle( 'display', 'none' );
          }

          var replacement = new Element('span', {
            id:(el.id)?el.id:'',
            'class':(el.className)?el.className:'',
            title:(el.title)?el.title:(el.alt)?el.alt:'',
            src: el.src
          });

          var styles = {
            display: vis?'inline-block':'none',
            width: dim.x,
            height: dim.y,
            filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader (src='" 
                    + el.src + "', sizingMethod='scale');"
          };


          replacement.setStyles( origin_styles );
          replacement.setStyles( styles );
          if (el.style.cssText) 
          {
            try 
            {
              var styles = {};
              var s = el.style.cssText.split(';');
              s.each(function(style){
                      var n = style.split(':');
                      styles[n[0]] = n[1];
              });
              replacement.setStyle(styles);
            } catch(e){ alert( 'problème 1' ); }
          }

          if (replacement.cloneEvents)
          {
            replacement.cloneEvents(el);
          }

          replacement.replaces(el);
        } 
        
        else if (el.get('tag') != "img") 
        {
          var imgURL = el.getStyle('background-image');
          var filter;



          if (imgURL.test(/\((.+)\)/))
          {
            if ( el.getStyle( 'background-repeat' ).test( /^repeat/ ) )
            {
              filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled='true', sizingMethod='scale', src=" + imgURL.match(/\((.+)\)/)[1] + ")";
            }

            else
            {
              filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled='true', sizingMethod='crop', src=" + imgURL.match(/\((.+)\)/)[1] + ")";
            }

            el.setStyles({
              background: '',
              filter: filter
            });
          }
        }
      }
    } 
    catch(e) { alert( 'problème 2' ); }
  },



  pngTest: /\.png$/, // saves recreating the regex repeatedly


  scanForPngs: function(el, className)
  {
    className = className||'fixPNG';

    if (document.getElements) // more efficient but requires 'selectors'
    {
      el = $(el||document.body);
      el.getElements('img[src$=.png]').addClass(className);
    } 
    
    else  // scan the whole page
    {
      var els = $$('img').each(function(img) {
        if (Browser.pngTest(img.src))
        {
          img.addClass(className);
        }
      });
    }
  }
});


if (Browser.Engine.trident4) 
{
  window.addEvent('domready', function(){$$('.fixPNG').each(Browser.fixPNG); });
}
