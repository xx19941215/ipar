define ([
    './var/arr',
    './var/obj',
    './var/document'
], function (arr, obj, document) {

    var version = "0.0.1",
        hasOwn = obj.hasOwnProperty,
        jsUrls = null,
        cssUrls = null,
        z;

    z = {
        zscript: version,

        version: version,

        isArray: Array.isArray,

        UID: 0,

        javascript: 'javascript',

        generateUid: function () {
            var d = new Date();
            z.UID += 1;
            return d.getTime().toString() + '-' + z.UID.toString();
        },

        type: function (obj) {
            if (obj === null) {
                return 'null';
            }
            if (obj === undefined) {
                return 'undefined';
            }
            return Object.prototype.toString.call(obj).slice(8, -1).toLowerCase();
        },

        isPlainObject: function (obj) {
            if (z.type(obj) !== "object" || obj.nodeType || z.isWindow(obj)) {
                return false;
            }
            if (obj.constructor && !hasOwn.call(obj.constructor.prototype, "isPrototypeOf")) {
                return false;
            }
            return true;
        },

        isFunction: function (obj) {
            return z.type(obj) === 'function';
        },

        extend: function () {
            var options, name, src, copy, copyIsArray, clone,
                i = 0,
                j = 0,
                target = {},
                //target = arguments[0] || {},
                //i = 1,
                length = arguments.length,
                deep = false;

            target = arguments[i] || {};
            i += 1;

            if (typeof target === 'boolean') {
                deep = target;
                target = arguments[i] || {};
                i += 1;
            }
            if (typeof target !== "object" && !z.isFunction(target)) {
                target = {};
            }
            if (i === length) {
                target = this;
                i -= 1;
            }
            for (j = i; j < length; j += 1) {
                options = arguments[j];
                if (options !== null && options !== undefined) {
                    for (name in options) {
                        if (options.hasOwnProperty(name)) {
                            src = target[name];
                            copy = options[name];
                            if (target !== copy) {
                                copyIsArray = z.isArray(copy);
                                if (deep && copy && (z.isPlainObject(copy) || copyIsArray)) {
                                    if (copyIsArray) {
                                        copyIsArray = false;
                                        clone = src && z.isArray(src) ? src : [];
                                    } else {
                                        clone = src && z.isPlainObject(src) ? src : {};
                                    }
                                    target[name] = z.extend(deep, clone, copy);
                                } else if (copy !== undefined) {
                                    target[name] = copy;
                                }
                            }
                        }
                    }
                }
            }
            return target;
        },

        addEvent: function (elem, type, handler, useCapture) {
            if (elem.addEventListener) {
                elem.addEventListener(type, handler, useCapture);
            } else if (elem.attachEvent) {
                elem.attachEvent("on" + type, handler);
            }
        },

        stopEvent: function (e) {
            if (!e) {
                e = window.event;
            }
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            e.cancelBubble = true;
        },

        cancelEvent: function (e) {
            if (!e) {
                e = window.event;
            }
            if (e.preventDefault) {
                e.preventDefault();
            } else {
                e.returnValue = false;
            }
        },

        ready: function (fn) {
            if (document.readyState !== 'loading') {
                fn();
            } else if (document.addEventListener) {
                document.addEventListener('DOMContentLoaded', fn);
            } else {
                document.attachEvent('onreadystatechange', function () {
                    if (document.readyState !== 'loading') {
                        fn();
                    }
                });
            }
        },

        showHide: function (elems, show) {
            var elem,
                index,
                len = elems.length;
            for (index = 0; index < len; index += 1) {
                elem = elems[index];
                if (elem.style) {
                    if (show) {
                        elem.style.display = "block";
                    } else {
                        elem.style.display = "none";
                    }
                }
            }
        },

        isHidden: function (elem, el) {
            elem = el || elem;
            return z.getStyle(elem, 'display') === 'none' || !z.contains(elem.ownerDocument, elem);
        },

        getStyles: function (elem) {
            if (elem.ownerDocument.defaultView.opener) {
                return elem.ownerDocument.defaultView.getComputedStyle(elem, null);
            }
            return window.getComputedStyle(elem, null);
        },
        getStyle: function (elem, name) {
            return z.getStyles(elem).getPropertyValue(name);
        },

        contains: function (el, child) {
            if (el === child) {
                return false;
            }

            if (el.contains) {
                return el.contains(child);
            }

            var parent = child;
            do {
                parent = parent.parentNode;
                if (parent === el) {
                    return true;
                }
            } while (parent && parent.nodeType !== 11);
            return false;
        },

        parseJSON: function (data) {
            return JSON.parse(data.toString());
        },

        stringify: function (obj) {
            return JSON.stringify(obj);
        },

        appendElement: function (el, newEl) {
            el.appendChild(newEl);
        },

        prependElement: function (el, newEl) {
            el.insertBefore(newEl, el.firstChild);
        },

        appendCsses: function (csses) {
            var url;
            for (url in csses) {
                if (csses.hasOwnProperty(url)) {
                    z.appendCss(url);
                }
            }
        },

        appendCss: function (cssUrl) {
            if (!z.hasCss(cssUrl)) {
                var l = document.createElement('link');
                l.setAttribute('rel', 'stylesheet');
                l.setAttribute('type', 'text/css');
                l.setAttribute('href', cssUrl);
                document.getElementsByTagName('head')[0].appendChild(l);
                cssUrls[cssUrl] = 1;
            }
        },

        hasCss: function (cssUrl) {
            var i, len, ss;
            if (!cssUrls) {
                cssUrls = {};
                ss = document.styleSheets;
                len = ss.length;
                if (len > 0) {
                    for (i = 0; i < len; i += 1) {
                        cssUrls[ss[0].href] = 1;
                    }
                }
            }
            return cssUrls.hasOwnProperty(cssUrl);
        },

        appendJses: function (scripts) {
            var url;
            for (url in scripts) {
                if (scripts.hasOwnProperty(url)) {
                    z.appendJs(url);
                }
            }
        },

        appendJs: function (jsUrl, callback) {
            if (!z.hasJs(jsUrl)) {
                var s = document.createElement('script'),
                    done;
                s.setAttribute('type', 'text/javascript');
                s.setAttribute('src', jsUrl);
                document.body.appendChild(s);
                jsUrls[jsUrl] = 1;
                if (callback) {
                   s.onload = s.onreadystatechange = function () {
                       if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
                           done = true;
                           callback();
                       }
                   };
                }
            }
        },

        getScript: function (jsUrl, callback) {
            var s = document.createElement('script'),
                done;
            s.setAttribute('type', 'text/javascript');
            s.setAttribute('src', jsUrl);
            document.body.appendChild(s);
            if (callback) {
                s.onload = s.onreadystatechange = function () {
                    if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
                        done = true;
                        callback();
                    }
                };
            }
        },

        hasJs: function (jsUrl) {
            var i, len, ss;
            if (!jsUrls) {
                jsUrls = {};
                ss = document.getElementsByTagName('script');
                len = ss.length;
                if (len > 0) {
                    for (i = 0; i < len; i += 1) {
                        jsUrl[ss[i].src] = 1;
                    }
                }
            }
            return jsUrls.hasOwnProperty(jsUrl);
        },

        hasClass: function (elem, className) {
            var i, len, arr;
            if (!className) {
                return false;
            }
            if (elem.classList) {
                arr = className.split(' ');
                len = arr.length;
                if (len === 1) {
                    return elem.classList.contains(className);
                }

                for (i = 0; i < len; i += 1) {
                    if (!elem.classList.contains(arr[i])) {
                        return false;
                    }
                }
                return true;

            }

            if (elem.className) {
                return (new RegExp('(^| )' + className + '( |$)', 'gi')).test(elem.className);
            }

            return false;
        },

        removeClass: function (elem, className) {
            var i, len, arr;
            if (elem.classList) {
                arr = className.split(' ');
                len = arr.length;
                if (len === 1) {
                    elem.classList.remove(className);
                } else {
                    for (i = 0; i < len; i += 1) {
                        z.removeClass(elem, arr[i]);
                    }
                }
            } else if (elem.className) {
                elem.className = elem.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
            }
        },

        addClass: function (elem, className) {
            var i, len, arr;
            if (!className) {
                return;
            }
            if (z.hasClass(elem, className)) {
                return;
            }
            if (elem.classList) {
                arr = className.split(' ');
                len = arr.length;
                if (len === 1) {
                    elem.classList.add(className);
                } else {
                    for (i = 0; i < len; i += 1) {
                        z.addClass(elem, arr[i]);
                    }
                }
            } else {
                if (elem.className) {
                    elem.className += " " + className;
                } else {
                    elem.className = className;
                }
            }
        },

        toggleClass: function (elem, className) {
            if (elem.classList) {
                elem.classList.toggle(className);
            } else {
                if (z.hasClass(elem, className)) {
                    z.removeClass(elem, className);
                } else {
                    z.addClass(elem, className);
                }
            }
        },

        trigger: function (el, event_name, type, params) {
            var event;
            type = type || 'mouseevent';

            if (type === 'mouseevent') {
                params = params || {view: window, bubbles: true, cancelable: true};
                event = new MouseEvent(event_name, params);
            }
            if (event) {
                el.dispatchEvent(event);
            }
            /*
            if (document.createEvent) {
                var event = document.createEvent('HTMLEvents');
                event.initEvent(e, true, false);
                el.dispatchEvent(event);
            } else {
                el.fireEvent('on' + e);
            }
            */
        },

        ucfirst: function (str) {
            str += '';
            var f = str.charAt(0).toUpperCase();
            return f + str.substr(1);
        },

        isOffScreen: function (el) {
            var scroll_top = z.scrollTop(),
                scroll_left = z.scrollLeft();

            return (
                (el.offsetTop + el.offsetHeight - scroll_top < 0)
                || (el.offsetTop > window.innerHeight + scroll_top)
                || (el.offsetLeft + el.offsetWidth - scroll_left < 0)
                || (el.offsetLeft > window.innerWidth + scroll_left )
            );
        },

        scrollTop: function () {
            return (window.scrollY || window.pageYOffset);
        },

        scrollLeft: function () {
            return (window.scrollX || window.pageXOffset);
        },

        parseStrictUrl: function (url) {
            return (/^(?:(http|https):)?\/\/([0-9.\-A-Za-z]+)(?:([0-9a-zA-Z_\-\/]*))?(?:\?([0-9a-zA-Z&=\-_]*))?(?:#([0-9a-zA-Z_\-]*))?$/).exec(url);
        },

        urldecode: function (str) {
            return decodeURIComponent((String(str))
                .replace(/%(?![\da-f]{2})/gi, function() {
                    return '%25';
                })
                .replace(/\+/g, '%20'));
        },

        time_elapsed_string: function (date, now) {
            var second = 1000,
                minute = second * 60,
                hour = minute * 60,
                day = hour * 24,
                diff_year = 0,
                diff_month = 0,
                diff_day = 0,
                diff_hour = 0,
                diff_minute = 0,
                diff_second = 0,
                diff;
            if (typeof date === 'string') {
                date = new Date(date);
            }
            now = now || new Date();
            if (typeof now === 'string') {
                now = new Date(now);
            }
            diff_year = now.getFullYear() - date.getFullYear();
            if (diff_year > 0) {
                return diff_year + '-year-before';
            }
            diff_month = (now.getFullYear() * 12 + now.getMonth()) - (date.getFullYear() * 12 + now.getMonth());
            if (diff_month > 0) {
                return diff_month + '-month-before';
            }
            diff = now - date;
            if (isNaN(diff)) {
                return NaN;
            }
            diff_day = Math.floor(diff / day);
            if (diff_day > 0) {
                return diff_day + '-day-before';
            }
            diff_hour = Math.floor(diff / hour);
            if (diff_hour > 0) {
                return diff_hour + '-hour-before';
            }
            diff_minute = Math.floor(diff / minute);
            if (diff_minute > 0) {
                return diff_minute + '-minute-before';
            }
            diff_second = Math.floor(diff / second);
            if (diff_second > 0) {
                return diff_second + '-second-before';
            }
            return 'just-now';
        },

        trans: function (str) {
            var dict = window.dict || {};
            if (dict.hasOwnProperty(str)) {
                return dict[str];
            } else {
                if (typeof(untrans) !== 'undefined') {
                    untrans(str);
                    return str;
                }
            }
        },

        get_token: function () {
            return z.config('token');
        },

        config: function (key, default_val) {
            default_val = default_val || {};
            window.config = window.config || {};
            if (!window.config.hasOwnProperty(key)) {
                window.config[key] = default_val;
            }
            return window.config[key];
        },

        http_build_query: function (args) {
            var k, arr = [];
            for (k in args) {
                if (args.hasOwnProperty(k)) {
                    arr.push(k + '=' + args[k]);
                }
            }
            return arr.join('&');
        },

        api_url: function (path, args, protocol) {
            var query;
            protocol = protocol || 'http://';
            if (args) {
                query = '?' + z.http_build_query(args);
            } else {
                query = '';
            }
            return protocol + z.config('site').api.host + '/' + z.config('locale_key') + '/' + path + query;
        },

        pack: function (data) {
            function PackDto(data) {
                if (data) {
                    this.ok = data.ok || 0;
                    this.items = data.items || {};
                    this.errors = data.errors || {};
                }
            }

            PackDto.prototype = {
                isOk: function () {
                    return (this.ok === 1);
                },
                getItem: function (key) {
                    return this.items[key] || '';
                },
                getError: function (key) {
                    return this.errors[key] || '';
                }
            };

            return new PackDto(data);
        },

        img_src: function (img, size) {
            var protocol = img.getAttribute('data-protocol') || 'http://',
                site = img.getAttribute('data-site') || 'static',
                host = z.config('site')[site].host,
                dir = img.getAttribute('data-dir'),
                name = img.getAttribute('data-name'),
                ext = img.getAttribute('data-ext'),
                size = size ? '-' + size : '';

            return protocol + host + dir + '/' + name + size + '.' + ext;
        },

        toFormData: function (data) {
            var fd = new window.FormData(),
                key;

            for (key in data) {
                if (data.hasOwnProperty(key)) {
                    fd.append(key, data[key]);
                }
            }
            return fd;
        }

        /*
        get_route_url: function (route) {
            var url_config = z.config('url') || {};
            if (url_config.hasOwnProperty(route)) {
                return url_config[route];
            } else {
                return '';
            }
        }
        */
    };

    window.z = z;
    return z;
});
