/*
 * taken from Yannick Croissant : http://dev.k1der.net/dev/live-events-pour-moootools
 * Attach an event to an element and use bubbling to fire the event on the childs who match a CSS selector
 * 
 * Usage : parentElement.addLiveEvent(event, selector, function)
 * 
 * parentElement - The parent to bind
 * event - The event name to monitor ('click', 'mouseover', etc) without the prefix 'on'
 * selector - The CSS Selector the childs need to match
 * function - The function to execute
 * 
 * Example :
 * $(document.body).addLiveEvent('click', 'a', function(e){ alert('Alert'); });
 * 
 */
Element.implement({
        addLiveEvent: function(event, selector, fn){
                this.addEvent(event, function(e){
                        var t = $(e.target);
                        if (!t.match(selector)) return false;
                        fn.apply(t, [e]);
                }.bindWithEvent(this, selector, fn));
        }
});
