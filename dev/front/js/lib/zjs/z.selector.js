define([
    './z',
    './var/arr',
    './var/document'
], function(z, arr, document) {
    'use strict';

    var Zselector,
        fn;

    Zselector = function(selector) {
        this.uids = {};
        this.length = 0;

        if (!selector) {
            return;
        }
        if (typeof selector === 'string') {
            Zselector.fn.push.apply(this, arr.slice.call(document.querySelectorAll(selector)));
            return;
        } else if (selector.nodeType) {
            this.push(selector);
            return;
        } else if (z.isFunction(selector)) {
            z.ready(selector);
            return;
        } else if (selector.zselector) {
            return selector;
        }
        this.push(selector);
    };

    Zselector.fn = Zselector.prototype = {
        zselector: z.version,
        version: z.version,
        constructor: Zselector,
        selector: "",
        length: 0,
        extend: z.extend,
        toArray: function() {
            return arr.slice.call(this);
        },
        get: function(num) {
            return (num !== null && num !== undefined) ?
                (num < 0 ? this[num + this.length] : this[num]) :
                arr.slice.call(this);
        },
        push: function() {
            var i,
                elem = null,
                len = arguments.length;
            if (0 === len) {
                return false;
            }
            for (i = 0; i < len; i += 1) {
                elem = arguments[i];

                if (elem && elem.nodeType) {
                    if (!elem.uid) {
                        elem.uid = z.generateUid();
                    }
                    if (!this.uids.hasOwnProperty(elem.uid)) {
                        this.uids[elem.uid] = this.length;
                        this[this.length] = elem;
                        this.length += 1;
                    }
                }

            }
        },
        pop: function() {
            var elem = null;
            if (this.length > 0) {
                elem = this[this.length - 1];
                delete this[this.length - 1];
                this.length -= 1;
            }
            return elem;
        },
        sort: arr.sort,
        splice: arr.splice,
        each: arr.forEach,

        find: function(selector) {
            var i,
                len = this.length,
                ret = [],
                elem = null,
                zs = new Zselector();

            for (i = 0; i < len; i += 1) {
                elem = this[i];
                Zselector.fn.push.apply(zs, elem.querySelectorAll(selector));
            }
            zs.selector = this.selector ? this.selector + " " + selector : selector;
            return zs;
        },
        parentByClasses: function(classes) {
            var i,
                len = this.length,
                elem = null,
                parent = null,
                zs = new Zselector();

            for (i = 0; i < len; i += 1) {
                elem = this[i];
                parent = elem.parentNode;
                while (parent && parent.nodeType !== 11) {
                    if (z.hasClass(parent, classes)) {
                        zs.push(parent);
                        break;
                    }
                    parent = parent.parentNode;
                }
            }
            return zs;
        },
        parent: function() {
            var i,
                len = this.length,
                elem = null,
                parent = null,
                zs = new Zselector();

            for (i = 0; i < len; i += 1) {
                elem = this[i];
                parent = elem.parentNode;
                if (parent && parent.nodeType !== 11) {
                    zs.push(parent);
                }
            }
            return zs;
        },
        on: function(type, handler, useCapture) {
            this.each(function(elem, index) {
                z.addEvent(elem, type, handler, useCapture);
            });
            return this;
        },
        style: function(name, value) {
            if (value !== null && value !== undefined) {
                this.each(function(elem) {
                    elem.style[name] = value;
                });
                return this;
            } else {
                if (this.length > 0) {
                    return z.getStyle(this[0], name);
                    //return this[0].style[name];
                }
            }
        },
        attr: function(name, value) {
            if (value !== null && value !== undefined) {
                this.each(function(elem) {
                    elem.setAttribute(name, value.toString());
                });
                return this;
            } else if (this.length > 0) {
                return this[0].getAttribute(name);
            }
        },
        prop: function(name, value) {
            if (value !== null && value !== undefined) {
                this.each(function(elem) {
                    elem[name] = value;
                });
                return this;
            } else if (this.length > 0) {
                return this[0][name];
            }
        },
        contains: function(otherElem) {
            if (otherElem.nodeType) {
                var elem, i,
                    len = this.length;
                for (i = 0; i < len; i += 1) {
                    elem = this[i];
                    if (z.contains(elem, otherElem)) {
                        return true;
                    }
                }
            }
            return false;
        },
        remove: function() {
            var elem;
            do {
                elem = this.pop();
                if (elem && elem.nodeType && elem.parentNode) {
                    elem.parentNode.removeChild(elem);
                }
            } while (elem);
            return this;
        },
        append: function(newElem) {
            if (!newElem) {
                return;
            }
            var i,
                elem = null,
                len = this.length,
                self = this;

            if (newElem.nodeType) {
                for (i = 0; i < len; i += 1) {
                    elem = this[i];
                    if (i > 0) {
                        newElem = newElem.cloneNode(true);
                    }
                    elem.appendChild(newElem);
                }
            } else if (newElem.zselector) {
                newElem.each(function(el) {
                    self.append(el);
                });
            }
            return this;
        },

        prepend: function(newElem) {
            if (!newElem) {
                return;
            }
            var i,
                elem = null,
                len = this.length,
                self = this;

            if (newElem.nodeType) {
                for (i = 0; i < len; i += 1) {
                    elem = this[i];
                    if (i > 0) {
                        newElem = newElem.cloneNode(true);
                    }
                    //z.prepend(elem, newElem);
                    elem.insertBefore(newElem, elem.firstChild);
                }
            } else if (newElem.zselector) {
                newElem.each(function(el) {
                    self.prepend(el);
                });
            }
            return this;
        },

        focus: function() {
            if (this.length > 0) {
                this[0].focus();
            }
            return this;
        },
        html: function(content) {
            if (this.length > 0) {
                if (content !== null && content !== undefined) {
                    this.each(function(elem) {
                        elem.innerHTML = content;
                    });
                    return this;
                } else {
                    return this[0].innerHTML;
                }
            }
        },
        text: function() {
            return this[0].innerText;
        },
        show: function() {
            z.showHide(this, true);
            return this;
        },
        hide: function() {
            z.showHide(this, false);
            return this;
        },
        toggle: function(state) {
            if (typeof state === "boolen") {
                return state ? this.show() : this.hide();
            }
            return this.each(function(elem) {
                if (z.isHidden(elem)) {
                    z.selector(elem).show();
                } else {
                    z.selector(elem).hide();
                }
            });
        },
        isOffScreen: function() {
            var i,
                len = this.length;
            for (i = 0; i < len; i += 1) {
                if (!z.isOffScreen(this[i])) {
                    return false;
                }
            }
            return true;
        },
        isHidden: function() {
            var i,
                len = this.length;
            for (i = 0; i < len; i += 1) {
                if (!z.isHidden(this[i])) {
                    return false;
                }
            }
            return true;
        },
        hasClass: function(className) {
            var i,
                len = this.length;

            for (i = 0; i < len; i += 1) {
                if (z.hasClass(this[i], className)) {
                    return true;
                }
            }
            return false;
        },

        removeClass: function(className) {
            var i,
                len = this.length;
            for (i = 0; i < len; i += 1) {
                z.removeClass(this[i], className);
            }
            return this;
        },

        addClass: function(className) {
            var i,
                len = this.length;
            for (i = 0; i < len; i += 1) {
                z.addClass(this[i], className);
            }
            return this;
        },

        toggleClass: function(className) {
            var i,
                len = this.length;
            for (i = 0; i < len; i += 1) {
                z.toggleClass(this[i], className);
            }
            return this;
        },

        trigger: function(eventName) {
            var i, len = this.length;
            for (i = 0; i < len; i += 1) {
                z.trigger(this[i], eventName);
            }
            return this;
        },

        autosize: function() {
            var self = this;

            function resize(e) {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            }

            function delayResize(e) {
                var _this = this;
                window.setTimeout(function () {
                    resize.call(_this, e);
                }, 0);
            }

            this.on('change', resize)
                .on('cut', delayResize)
                .on('mousedown', delayResize)
                .on('paste', delayResize)
                .on('drop', delayResize)
                .on('keydown', delayResize);
            return this;

        },
    };

    fn = function(selector) {
        return new Zselector(selector);
    };

    fn.create = function(tagName) {
        var elem = document.createElement(tagName);
        return new Zselector(elem);
    };

    return fn;

});
