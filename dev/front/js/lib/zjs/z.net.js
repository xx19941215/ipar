define([
    'zjs/z',
    'z.config'
], function (z, config) {
    'use strict';
    var net;

    net = {
        xhr: function () {
            var xhr;
            if (window.XMLHttpRequest) {
                xhr = new XMLHttpRequest();
            } else if (window.ActiveXObject) {
                try {
                    xhr = new window.ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                    try {
                        xhr = new window.ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e1) {
                    }
                }
            }
            if (!xhr) {
                //alert('Cannot create an XMLHTTP instance');
                return false;
            } else {
                return xhr;
            }
        },

        routeGet: function (route, args, callback) {
            return net.routeRequest({
                route: route,
                callback: callback,
                method: 'get',
                args: args
            });
        },
        routePost: function (route, fd, callback) {
            if (z.type(fd) !== 'formdata') {
                fd = z.toFormData(fd);
            }
            fd.append('_token', z.get_token());
            return net.routeRequest({
                route: route,
                callback: callback,
                fd: fd,
                method: 'post'
            });
        },
        routeRequest: function (opts) {
            var url = z.config('api')[opts.route];
            if (!url) {
                console.log('route-not-found');
                return;
            }
            if (opts.args) {
                url += '?' + z.http_build_query(opts.args);
            }
            return net.ajax({
                dataType: 'json',
                url: url,
                method: opts.method || 'post',
                withCredentials: true
            })
            .done(function (data) {
                if (opts.callback) {
                    opts.callback.call(this, z.pack(data));
                }
            })
            .send(opts.fd);
        },
        getPack: function (route, args, callback) {
            return net.requestPack({
                route: route,
                callback: callback,
                method: 'get',
                args: args
            });
        },
        postPack: function (route, fd, callback) {
            if (z.type(fd) !== 'formdata') {
                fd = z.toFormData(fd);
            }
            fd.append('_token', z.get_token());
            return net.requestPack({
                route: route,
                callback: callback,
                fd: fd,
                method: 'post'
            });
        },
        requestPack: function (opts) {
            var route = config.route[opts.route] || {},
                locale_key = z.config('locale_key'),
                url = 'http://' + route.host + '/' + locale_key + route.pattern;

            if (opts.args) {
                url += '?' + z.http_build_query(opts.args);
            }
            return net.ajax({
                dataType: 'json',
                url: url,
                method: opts.method || 'post',
                withCredentials: true
            })
            .done(function (data) {
                if (opts.callback) {
                    opts.callback.call(this, z.pack(data));
                }
            })
            .send(opts.fd);
        },
        ajax: function (opts) {
            var method = (opts.method || 'get').toUpperCase(),
                async = opts.async === false ? false : true,
                url = opts.url || '',
                dataType = opts.dataType || 'html',
                user = opts.user,
                password = opts.password,

                doneCallback,
                failCallback,
                alwaysCallback,

                zXHR,

                xhr = net.xhr();

            if (!xhr) {
                return false;
            }

            if (opts.withCredentials) {
                xhr.withCredentials = true;
            }

            /*
                accepts: {
                    "*": allTypes,
                    text: "text/plain",
                    html: "text/html",
                    xml: "application/xml, text/xml",
                    json: "application/json, text/javascript"
                },
                xhr.responseType
                ""               DOMString (default)
                "arraybuffer"    ArrayBuffer
                "blob"           Blob
                "document"       Document
                "json"           JSON
                "text"           DOMString
            */
            zXHR = {
                readyState: function () {
                    return xhr.readyState;
                },
                abort: function () {
                    xhr.abort();
                    return this;
                },
                getAllResponseHeaders: function () {
                    return xhr.getAllResponseHeaders();
                },
                getResponseHeader: function (header) {
                    return xhr.getResponseHeader(header);
                },
                overrideMimeType: function (mime) {
                    xhr.overrideMimeType(mime);
                    return this;
                },
                send: function (data) {
                    xhr.send(data);
                    return this;
                },
                setRequestHeader: function (header, value) {
                    xhr.setRequestHeader(header, value);
                    return this;
                },
                done: function (callback) {
                    doneCallback = callback;
                    return this;
                },
                fail: function (callback) {
                    failCallback = callback;
                    return this;
                },
                always: function (callback) {
                    alwaysCallback = callback;
                    return this;
                }
            };

            xhr.onload = function (e) {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        if (!doneCallback) {
                            return;
                        }
                        var contentType = xhr.getResponseHeader('Content-Type');
                        if (contentType === 'application/json') {
                            doneCallback.call(zXHR, z.parseJSON(xhr.responseText));
                        } else {
                            doneCallback.call(zXHR, xhr.responseText);
                        }
                    } else {
                        if (!failCallback) {
                            return;
                        }
                        failCallback.call(zXHR);
                    }
                    if (!alwaysCallback) {
                        return;
                    }
                    alwaysCallback.call(zXHR);
                }
            };

            xhr.onerror = function (e) {
                if (failCallback) {
                    failCallback(zXHR);
                }
            };

            //xhr.onprogress
            //xhr.onabort
            xhr.open(method, url, async, user, password);

            return zXHR;
        }
    };

    return net;
});
