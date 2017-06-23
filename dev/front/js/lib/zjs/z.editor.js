/**
 * https://developer.mozilla.org/en-US/docs/Web/API/Document/execCommand#Example
 * https://developer.mozilla.org/en-US/docs/Rich-Text_Editing_in_Mozilla
 * http://codepen.io/netsi1964/full/QbLLGW/
 */
/*jslint browser: true*/
define([
    './z',
    './z.selector',
    './z.net'
], function(z, s, net) {
    'use strict';

    var Zeditor, fn;

    Zeditor = function(selector, features) {
        var i, len, elem;

        if (selector === null || selector === undefined) {
            window.console.log('error, Zeditor selector could not be empty');
            return false;
        }

        this.$zs = s(selector);
        features && (this.features = features);

        len = this.$zs.length;

        if (len === 0) {
            window.console.log('error, Zeditor $zs no selected node');
            return false;
        }

        for (i = 0; i < len; i += 1) {
            elem = this.$zs[i];
            this.initZeditor(elem);
        }
        this.$content = this.$zs.find('.zeditor-content');
        this.$btnsWrap = this.$zs.find('.zeditor-btns-wrap');
        this.btnsWrapHeight = this.$btnsWrap.prop('offsetHeight');
    };

    Zeditor.fn = Zeditor.prototype = {

        $zs: '',

        loaded: false,

        max_height: 42,

        $content: null,

        $btnsWrap: null,

        btnsWrapHeight: 0,

        features: ['image'],

        initZeditor: function(zeditor) {
            var $zeditor = s(zeditor),
                $btnsWrap = s.create('div')
                .attr('class', 'zeditor-btns-wrap'), //.style('visibility', 'hidden'),

                $content = s.create('div')
                .attr('class', 'zeditor-content')
                .prop('contentEditable', true),

                $input = s.create('textarea').style('display', 'none'),
                $btn,

                $inputImg = s.create('input')
                .attr('type', 'file')
                .attr('multiple', 'multiple')
                .attr('accept', 'image/x-png, image/gif, image/jpeg')
                .addClass('zinput-img'),

                $msg = s.create('span').addClass('zmsg hide'),
                msgTimeoutId = null,

                btns = {},
                selectedRange = null,

                form,
                data,

                _token,

                self = this;

            function getSelectedRange() {
                var sel;
                if (window.getSelection) {
                    sel = window.getSelection();
                    if (sel.getRangeAt && sel.rangeCount) {
                        selectedRange = sel.getRangeAt(0);
                    }
                } else if (document.selection && document.selection.createRange) {
                    selectedRange = document.selection.createRange();
                }
                return selectedRange || null;
            }

            function restoreSelection() {
                var sel;
                if (selectedRange) {
                    if (window.getSelection) {
                        sel = window.getSelection();
                        sel.removeAllRanges();
                        sel.addRange(selectedRange);
                    } else if (document.selection && selectedRange.select) {
                        selectedRange.select();
                    }
                }
            }

            function hideMessage() {
                $msg.addClass('hide');
            }

            function message(msg, type) {
                $msg.html(msg);
                if (msgTimeoutId) {
                    window.clearTimeout(msgTimeoutId);
                }
                if (type === 'success') {
                    $msg.prop('className', 'zmsg zmsg-success');
                    msgTimeoutId = window.setTimeout(hideMessage, 3000);
                } else if (type === 'error') {
                    $msg.prop('className', 'zmsg zmsg-error');
                    msgTimeoutId = window.setTimeout(hideMessage, 3000);
                } else {
                    $msg.prop('className', 'zmsg');
                }
            }

            function handleKeyup(e) {
                self.resize();
                //$input.attr('value', $content.html());
            }

            function handleBlur(e) {
                //getSelectedRange();

            }

            function uploadImg(imgId, file) {
                var formData = new window.FormData(),
                    filename = file.name,
                    token = getToken();

                message(z.trans('uploading-image'));
                if (!filename) {
                    if (file.type) {
                        if (file.type === 'image/png' || file.type === 'image/x-png') {
                            filename = imgId + '.png';
                        } else if (file.type === 'image/gif') {
                            filename = imgId + '.gif';
                        } else if (file.type === 'image/jpeg' || file.type === 'image/jpg') {
                            filename = imgId + '.jpg';
                        }
                    }
                }

                formData.append('img', file, filename);

                net.postPack('ipar-rest-img-upload_figure_post', formData, function (pack) {
                    var src, $img, img, protocol, host, site;
                    $img = s('#' + imgId);
                    //pack = z.pack(data);
                    if (pack.isOk()) {
                        img = pack.getItem('img');
                        protocol = img.protocol || 'http://';
                        site = img.site || 'static';
                        host = z.config('site')[site].host;
                        src = protocol + host + img.dir + '/' + img.name;
                        if ($content.prop('offsetWidth') > 371) {
                            //src += '-large.' + img.ext;
                            src += '.' + img.ext;
                        } else {
                            src += '-medium.' + img.ext;
                        }
                        $img.attr('src', src).removeClass('prvw')
                            .attr('data-dir', img.dir)
                            .attr('data-name', img.name)
                            .attr('data-ext', img.ext);
                        if (img.protocol) {
                            $img.attr('data-protocol', img.protocol);
                        }
                        if (img.site) {
                            $img.attr('data-site', img.site);
                        }
                        //$img.attr('img', z.stringify(img)).attr('src', src).removeClass('prvw');
                        message(z.trans('image-upload-successed'), 'success');
                    } else {
                        $img.remove();
                        message(z.trans('image-upload-failed'), 'error');
                        if (typeof data === 'string') {
                            window.console.log(data);
                        }
                    }

                });
            }

            function insertImg(file) {
                var reader = new window.FileReader(),
                    img_id = 'zimg-' + z.generateUid(),
                    img_html = '<img src="" class="zimg prvw" id="' + img_id + '">';

                $content[0].focus();
                document.execCommand('insertHTML', false, img_html);
                //console.log(img_id);

                if (file) {
                    reader.readAsDataURL(file);
                    uploadImg(img_id, file);
                }

                reader.onload = function (e) {
                    s('#' + img_id).attr('src', e.target.result);
                    self.resize();
                }
                return;

                /*
                var reader = new window.FileReader(),
                    $img = s.create('img').addClass('zimg prvw'),
                    imgId = 'zimg-' + $img.prop('uid'),
                    range = getSelectedRange();
                $img.attr('id', imgId);
                reader.onload = function(e) {
                    $img.attr('src', e.target.result);
                    if (range && $content.contains(range.commonAncestorContainer)) {
                        range.deleteContents();
                        range.insertNode($img[0]);
                        //restoreSelection();
                    } else {
                        $content.append($img);
                    }
                    self.resize();
                };
                //reader.readAsDataURL(input.files[0]);
                if (file) {
                    reader.readAsDataURL(file);
                    uploadImg(imgId, file);
                }
                */
            }

            function execCmd(cmd, aShowDefaultUI, arg) {
                if (cmd === 'image') {
                    //getSelectedRange();
                    $inputImg.trigger('click');
                } else {
                    document.execCommand(cmd, aShowDefaultUI, arg);
                }
            }

            function newLine(evt) {
                /**
                 * determine if the cursor is at the start or end of the content
                 * http://stackoverflow.com/questions/7451468/contenteditable-div-how-can-i-determine-if-the-cursor-is-at-the-start-or-end-o
                 * http://jsfiddle.net/YA3Pu/1/
                 *
                 * change behavior of contenteditable blocks after on enter pressed in various browsers
                 * http://stackoverflow.com/questions/2735672/how-to-change-behavior-of-contenteditable-blocks-after-on-enter-pressed-in-vario
                 *
                 */
                var sel, range, testRange, br, br2, addedBr = false,
                    charCode;
                evt = evt || window.event;
                charCode = evt.keyCode || evt.which;
                if (charCode === 13) {
                    if (typeof window.getSelection !== "undefined") {
                        sel = window.getSelection();
                        if (sel.getRangeAt && sel.rangeCount) {
                            range = sel.getRangeAt(0);
                            range.deleteContents();
                            br = document.createElement("br");
                            range.insertNode(br);
                            range.setEndAfter(br);
                            range.setStartAfter(br);
                            sel.removeAllRanges();
                            sel.addRange(range);
                            addedBr = true;

                            testRange = range.cloneRange();
                            //testRange.selectNodeContents(this);
                            testRange.selectNodeContents($content[0]);
                            testRange.setStart(range.endContainer, range.endOffset);
                            if (testRange.toString() === '') {
                                br2 = document.createElement("br");
                                range.insertNode(br2);
                                range.setEndAfter(br2);
                                range.setStartAfter(br2);
                                sel.removeAllRanges();
                                sel.addRange(range);
                            }
                        }
                    } else if (typeof document.selection !== "undefined") {
                        sel = document.selection;
                        if (sel.createRange) {
                            range = sel.createRange();
                            range.pasteHTML("<br>");
                            range.select();
                            addedBr = true;
                        }
                    }

                    // If successful, prevent the browser's default handling of the keypress
                    if (addedBr) {
                        if (typeof evt.preventDefault !== "undefined") {
                            evt.preventDefault();
                        } else {
                            evt.returnValue = false;
                        }
                    }
                }
            }

            function handlePaste(e) {
                var text, copiedData, items, clipboardData, i, len, blob = null;

                z.cancelEvent(e);

                clipboardData = e.clipboardData || e.originalEvent.clipboardData;

                if (typeof clipboardData === 'undefined') {
                    return;
                }

                items = clipboardData.items;
                len = items.length;
                for (i = 0; i < len; i++) {
                    if (items[i].kind === 'file' && items[i].type.indexOf('image') === 0) {
                        blob = items[i].getAsFile();
                    }
                }
                if (blob !== null) {
                    insertImg(blob);
                } else {
                    text = '<p>' + clipboardData.getData("text/plain").replace(/(\n|\r\n)+/ig, '</p><p>') + '</p>';
                    execCmd("insertHTML", false, text);
                    return;

                    if (!self.hasFeature('pasteHTML')) {
                        text = clipboardData.getData("text/plain").replace(/(\n|\r\n)+/ig, '<br>');
                        execCmd("insertHTML", false, text);
                        return;
                    }

                    var raw = clipboardData.getData("text/html");
                    raw = raw.substr(raw.indexOf('>') + 1); // remove meta tag
                    var html = document.createElement('div');
                    html.innerHTML = raw;

                    var range = getSelectedRange();
                    walkDom(html, function (node) {
                        if ((node.tagName && node.tagName.toUpperCase()) === 'IMG') {
                            var url = node.dataset.src || node.src;

                            if (node.style.height) node.setAttribute('height', node.style.height);
                            if (node.style.width) node.setAttribute('width', node.style.width);

                            var fd = new FormData();
                            fd.append('imageUrl', url);

                            net.routePost('retrieve_image', fd, function(data) {
                                var img = data.items.img,
                                    imgUrl = '//' + z.config('site').static.host + img.dir + '/' + img.name + '.' + img.ext;

                                console.log(imgUrl);
                                node.src = imgUrl;
                            });
                        }

                        if ((node.tagName && node.tagName.toUpperCase()) === 'A') {
                            node.removeAttribute('href');
                        }

                        if (node.nodeType == 1) {
                            node.removeAttribute('class');
                            node.removeAttribute('style');
                        }
                    });

                    range.insertNode(html);
                    self.resize();
                    // execCmd("insertHTML", false, html);
                }

                function walkDom(node, func) {
                    func(node);
                    node = node.firstChild;
                    while (node) {
                        walkDom(node, func);
                        node = node.nextSibling;
                    }
                }

                function getImgCanvas(imageUrl, func) {
                    var image = new Image();
                    image.src = imageUrl;
                    image.onload = function () {
                        var canvas = document.createElement("canvas");
                        canvas.width =this.width;
                        canvas.height =this.height;

                        var ctx = canvas.getContext("2d");
                        ctx.drawImage(this, 0, 0);
                        func(canvas);
                    }
                }

                /*
                    copiedData = items[index];
                    if (copiedData.kind === 'file') {
                        insertImg(copiedData.getAsFile());
                    } else {
                        text = e.clipboardData.getData("text/plain").replace(/(\n|\r\n)+/ig, '<br>');
                        execCmd("insertHTML", false, text);
                    }
                //types = clipboardData.types;
                //len = types.length;
                copiedData = items[0];

                //if (len === 1) {
                if (copiedData.type.indexOf('image') === 0) {
                    insertImg(copiedData.getAsFile());
                } else {
                    z.cancelEvent(e);
                    text = e.clipboardData.getData("text/plain").replace(/(\n|\r\n)+/ig, '<br>');
                    execCmd("insertHTML", false, text);
                }
                //}
                */
            }

            function handleDrop(e) {
                var file, x, y, pos, range, i, len;
                file = e.dataTransfer.files[0];
                if (file && file.type.match('image.*')) {
                    z.cancelEvent(e);
                    x = e.clientX;
                    y = e.clientY;
                    if (document.caretPositionFromPoint) {
                        // Try the standards-based way first. This works in FF
                        pos = document.caretPositionFromPoint(x, y);
                        range = document.createRange();
                        range.setStart(pos.offsetNode, pos.offset);
                        range.collapse();
                        selectedRange = range;
                    } else if (document.caretRangeFromPoint) {
                        // Next, the WebKit way. This works in Chrome.
                        selectedRange = document.caretRangeFromPoint(x, y);
                    }
                }

                len = e.dataTransfer.files.length;
                for (i = 0; i < len; i++) {
                    file = e.dataTransfer.files[i];
                    if (file && file.type.match('image.*')) {
                        insertImg(file);
                    }
                }

            }

            function handleClick(e) {
                //getSelectedRange();
                /*
                var target, $target, parent, styles;
                getSelectedRange();
                target = e.target;
                $target = s(target);
                $btn.removeClass('selected');

                // toggle zimg
                if ($target.hasClass('zimg')) {
                    $target.toggleClass('small');
                    self.resize();
                }

                // selected execCmd btns
                while (target && target.nodeType !== 11 && !target.zeditor_content) {
                    styles = z.getStyles(target);
                    if (styles.getPropertyValue('font-weight') === 'bold') {
                        btns.bold.addClass('selected');
                    }
                    if (styles.getPropertyValue('font-style') === 'italic') {
                        btns.italic.addClass('selected');
                    }
                    if (styles.getPropertyValue('text-decoration') === 'underline') {
                        btns.underline.addClass('selected');
                    }
                    parent = target.parentNode;
                    target = parent;
                }
                */
            }

            function handleSubmit(e) {
                $content.find('.zimg.prvw').remove();
                $content.find('.zimg').each(function (img) {
                    s(img).removeClass('small').attr('src', z.img_src(img, 'small'));
                });
                //$content.find('.zimg').removeClass('small').attr('src', '/loading.gif');
                $content.find('p.empty').each(function (el) {
                    var html = el.innerHTML;
                    html = html.replace(/\u200d/, '');
                    el.innerHTML = html;
                    el.removeAttribute('class');
                });

                if ($zeditor.attr('data-required')) {
                    if (!$content.text().trim()) {
                        message('为你添加的产品说几句话吧  *>.<*', 'error');
                        z.cancelEvent(e);
                        z.stopEvent(e);
                        return false;
                    }
                }

                $input.html($content.html());
                return false;
            }

            function getForm() {
                var form = zeditor.parentNode;
                while (form && form.nodeType) {
                    if (form.tagName && form.tagName.toLowerCase() === 'form') {
                        return form;
                    }
                    form = form.parentNode;
                }
                return null;
            }

            function getToken() {
                var $input;
                if (!_token) {
                    $input = s(getForm()).find('input[name="_token"]');
                    if (!$input) {
                        return false;
                    }
                    _token = $input.prop('value');
                }
                return _token;
            }


            // $zeditor

            $input.attr('name', $zeditor.attr('data-name'));
            data = $zeditor.html();
            $input.html(data);
            $content.html(data);
            $zeditor.html('');

            $zeditor
                .append($content)
                .append($btnsWrap)
                .append($input)
                .append($inputImg)
                .append($msg);

            // put an empty p tag in $content
            var p = "<p class='empty'>&zwj;</p>";
            var div = document.createElement('div');
            div.innerHTML = p;
            var frag = new DocumentFragment();
            frag.appendChild(div.firstChild);
            $content[0].appendChild(frag);

            //
            // .zeditor-btns
            //
            s(document).on('click', function(e) {
                var elem = e.target || event.srcElement;
                if (!$zeditor.contains(elem)) {
                    $btnsWrap.style('visibility', 'hidden');
                } else {
                    $btnsWrap.style('visibility', 'visible');
                }
            });

            [{
                cmd: 'image',
                awe: 'icon icon-image'
            },{
                cmd: 'bold',
                awe: 'icon icon-bold'
            },{
                cmd: 'insertUnorderedList',
                awe: 'icon icon-list'
            },{
                cmd: 'justifyLeft',
                awe: 'icon icon-left'
            },{
                cmd: 'justifyCenter',
                awe: 'icon icon-center'
            }, {
                cmd: 'formatBlock',
                menu: true,
                awe: 'icon icon-paragraph',
                list: [{
                    cmd: 'formatBlock',
                    arg: 'H1',
                    text: 'Heading 1'
                }, {
                    cmd: 'formatBlock',
                    arg: 'H2',
                    text: 'Heading 2'
                }, {
                    cmd: 'formatBlock',
                    arg: 'H3',
                    text: 'Heading 3'
                }, {
                    cmd: 'formatBlock',
                    arg: 'p',
                    text: 'paragraph'
                }]
            }].forEach(function(item) {
                var $a = s.create('a');


                renderButton($a, item);

                if (item.menu) {
                    var frag = new DocumentFragment();
                    item.list.forEach(function(item) {
                        var $subAnchor = s.create('a');
                        renderButton($subAnchor, item);
                        frag.appendChild($subAnchor[0]);
                    });

                    self.renderTooltip($a)
                        .appendChild(frag);
                }

                $btnsWrap.append($a);

                function renderButton($node, item) {
                    if (!self.hasFeature(item.cmd)) return;

                    $node.attr('data-cmd', item.cmd);
                    $node.attr('href', 'javascript:;');
                    if (item.arg) $node.attr('data-arg', item.arg);
                    if (item.text) $node.html(item.text);

                    if (!item.menu) {
                        $node.attr('class', 'zeditor-btn zeditor-btn-' + item.cmd)
                            .append(s.create('i').attr('class', item.awe));

                        $node.on('click', function() {
                            var $btn = s(this),
                                cmd = $btn.attr('data-cmd'),
                                arg = $btn.attr('data-arg');
                            $btn.toggleClass('selected');
                            execCmd(cmd, null, arg);
                            self.resize();
                        });

                    } else {
                        $node.attr('class', 'zeditor-btn zeditor-btn-' + item.arg)
                            .append(s.create('i').attr('class', item.awe));
                    }

                    return $node;
                }
            });

            $inputImg.on('change', function() {
                //reader.readAsDataURL(input.files[0]);
                var len = this.files.length,
                    i = 0;
                for (i = 0; i < len; i++) {
                    insertImg(this.files[i]);
                }
            });

            $btn = $zeditor.find('.zeditor-btn');

            // $content
            $content.attr('data-placeholder', $zeditor.attr('data-placeholder'));
            $content.prop('zeditor_content', 1);

            $content
                .on('focus', function() {
                    $btnsWrap.style('visibility', 'visible');
                })
                .on('drop', handleDrop)
                .on('paste', handlePaste)
                .on('keyup', handleKeyup)
                .on('blur', handleBlur)
                .on('click', handleClick);
                // .on('keydown', newLine);


            // form
            form = getForm();
            if (form) {
                z.addEvent(form, 'submit', handleSubmit);
            }
        },

        prepare: function() {
            var elem,
                $elem,
                $content,
                $input;

            for (i = 0; i < len; i += 1) {
                elem = this.$zs[i];
                $elem = s(elem);
                $content = $elem.find('.zeditor-content'),
                    $input = $elem.find('textarea');
                $content.find('.zimg.prvw').remove();
                $content.find('.zimg').removeClass('small');
                $input.html($content.html());
            }
        },

        focus: function() {
            this.$zs.find('.zeditor-content').focus();
            return this;
        },

        clear: function() {
            //this.$zs.find('input').attr('value', '');
            //this.$zs.find('.zeditor-content').html('');
            this.val('');
            return this;
        },

        val: function(val) {
            if (!val.trim()) {
                val = "<p class='empty'>&zwj;</p>";
            }
            this.$zs.find('input').attr('value', val);
            this.$zs.find('.zeditor-content').html(val);
            return this;
        },
        text: function () {
            return this.$zs.find('.zeditor-content').text();
        },
        autoheight: function(opts) {
            var offset = (opts && opts.offset) ? opts.offset : 100,
                max_height = window.innerHeight - offset,
                min_height = 42,
                self = this;
            if (max_height < min_height) {
                max_height = min_height;
            }
            this.max_height = max_height;
            this.resize();
            z.addEvent(window, 'resize', function() {
                self.autoheight(opts);
            });
        },

        resize: function() {
            var offsetHeight = this.$content.prop('offsetHeight'),
                scrollHeight = this.$content.prop('scrollHeight'),
                cssHeight = this.$content.style('height'),
                height = parseFloat(cssHeight),
                paddingHeight = 6,
                max_height = this.max_height - this.btnsWrapHeight;

            if (scrollHeight <= max_height) {
                if (cssHeight !== 'auto') {
                    this.$content.style('height', 'auto');
                }
            } else {
                this.$content.style('height', (max_height - paddingHeight) + 'px');
            }
        },

        setUploadUrl: function(uploadUrl) {
            this.uploadUrl = uploadUrl;
            return this;
        },

        renderTooltip: function($button) {
            var $tooltip;
            $tooltip = s.create('div')
                .addClass('tooltip');
            $button.append($tooltip);
            return $tooltip[0];
        },

        hasFeature: function(feature) {
             return this.features.indexOf(feature) >= 0;
        }

        //bind: function(data) {}

    };

    fn = function(selector, features) {
        return new Zeditor(selector, features);
    };

    return fn;

});
