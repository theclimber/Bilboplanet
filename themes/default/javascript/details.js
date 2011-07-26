/*
 Author: Remy Sharp
 Contact: remy [at] remysharp.com
*/
if (!('open' in document.createElement('details'))) {
(function () {
  // general cross browser add event function
  var addEvent = (function () {
    if (document.addEventListener) {
      return function (el, type, fn) {
        if (el.length) {
          for (var i = 0; i < el.length; i++) {
            addEvent(el[i], type, fn);
          }
        } else {
			if (el.length == null) { // el is not an array
				el.addEventListener(type, fn, false);
			}
        }
      };
    } else {
      return function (el, type, fn) {
        if (el.length) {
          for (var i = 0; i < el.length; i++) {
            addEvent(el[i], type, fn);
          }
        } else {
			if (el.length == null) {
				el.attachEvent('on' + type, function () { return fn.call(el, window.event); });
			}
        }
      };
    }
  })();

  // find the first /real/ node
  function firstNode(source) {
    var node = null;
    if (source.firstChild.nodeName != "#text") {
      return source.firstChild;
    } else {
      source = source.firstChild;
      do {
        source = source.nextSibling;
      } while (source && source.nodeName == '#text');

      return source || null;
    }
  }

  function toggleDetails(event) {
    // console.log(event);
    // more sigh - need to check the clicked object
    var keypress = event.type == 'keypress',
        target = event.target || event.srcElement;
    if (keypress || target.legend == true) {
      if (keypress) {
        // if it's a keypress, make sure it was enter or space
        keypress = event.which || event.keyCode;
        // console.log(keypress);
        if (keypress == 32 || keypress == 13) {
          // all's good, go ahead and toggle
        } else {
          return;
        }
      }
      var open = !!!(this.getAttribute('open'));
      if (open) {
        this.setAttribute('open', 'open');
      } else {
        this.removeAttribute('open');
      }
      this.className = open ? 'open' : ''; // Lame
    }
  }

  var details = document.getElementsByTagName('details'),
      i = details.length,
      first = null,
      label = document.createElement('summary');

  label.appendChild(document.createTextNode('Details'));

  while (i--) {
	  console.debug('brol');
    first = firstNode(details[i]);

    if (first != null && first.nodeName.toUpperCase() == 'SUMMARY') {
      // we've found that there's a details label already
    } else {
      // first = label.cloneNode(true); // cloned nodes weren't picking up styles in IE - random
      first = document.createElement('summary');
      first.appendChild(document.createTextNode('Details'));
      if (details[i].firstChild) {
        details[i].insertBefore(first, details[i].firstChild);
      } else {
        details[i].appendChild(first);
      }
    }

    first.legend = true;
    first.tabIndex = 0;
  }

  addEvent(details, 'click', toggleDetails);
  addEvent(details, 'keypress', toggleDetails);
})();
}
