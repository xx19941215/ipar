define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net'
], function(z, s, net) {
    'use strict';

    var $input_img, pop_panel;

    //function Zimgeditor(upload_action, width, height, container) {
    function Zimgeditor(opts) {
        this.upload_action = opts.upload_action;
        this.zoom = opts.zoom;
        this.min_zoom = opts.min_zoom;
        this.max_zoom = opts.max_zoom;

        this.width = parseFloat(opts.width);
        this.height = parseFloat(opts.height);

        this.wrap_width = 'calc(100%)';
        this.wrap_height = 'calc(100%)';

        this.current_file = null;
        this.submit_form = s(opts.submit_form);

        //this.$target = s(target);
        this.$container = s(opts.container || 'body');
        this.$input_img = this.createInputImg();

        this.pop_panel = this.createPopPanel(opts.pop_panel);
        this.$pop_panel = this.pop_panel.$panel;
        this.$layout_wrapper = this.pop_panel.$layout_wrapper;

        this.$photo_out_wrap = this.$pop_panel.find('.photo-out-wrap');
        this.$photo_in_wrap = this.$pop_panel.find('.photo-in-wrap');
        this.$photo_out = this.$pop_panel.find('img.photo-out');
        this.$photo_in = this.$pop_panel.find('img.photo-in');
    }

    z.extend(Zimgeditor.prototype, {
        createInputImg: function () {
            var self = this;

            if ($input_img) {
                return $input_img;
            }

            $input_img = s.create('input').attr('type', 'file').attr('name', 'upfile').attr('accept', 'image/x-png, image/gif, image/jpeg').addClass('zimgeditor-input-img');
            this.$container.append($input_img);
            this.input_img = $input_img;

            $input_img.on('change', function () {
                var file = this.files[0];
                if (!file) {
                    return;
                }
                self.current_file = file;

                self.$pop_panel
                    .style('width', self.wrap_width + 'px')
                    .style('marginLeft', '0px');

                self.$photo_out_wrap
                    .style('height', (self.wrap_height) + 'px')
                    .style('width', (self.wrap_width) + 'px')
                    .style('left', '0px');
                self.$photo_in_wrap
                    .style('height', self.height + 'px')
                    .style('width', self.width + 'px')

                //self.$pop_panel.removeClass('hide');
                self.pop_panel.show();
                self.preview();
                //self.moving();

            });
            return $input_img;
        },
        createPopPanel: function (pop_panel) {
            var self = this,
                $pop_panel;



            pop_panel.html(
                [
                    '<div class="pop-panel-body">',
                    '<div class="photo-out-wrap">',
                    '<img class="photo-out" src="">',
                    '<div class="photo-in-wrap">',
                    '<img class="photo-in" src="">',
                    '</div>',
                    '</div>',
                    '</div>',
                    '<div class="pop-panel-foot clearfix">',
                    '<div class="left">',
                    '<a class="zoom zoom-in" href="javascript:;">-</a>',
                    '<a class="zoom zoom-out" href="javascript:;">+</a>',
                    '</div>',
                    '<div class="error hide">',
                    '</div>',
                    '<div>',
                        '<label>',
                        z.trans('width'),
                        '</label>',
                        '<input type="text" name="width" value=" " required="required">',
                    '</div>',
                    '<div>',
                        '<label>',
                        z.trans('height'),
                        '</label>',
                        '<input type="text" name="height" value=" " required="required">',
                    '</div>',
                    '<div class="opes right">',
                    '<span class="updating hide">' + z.trans('update') + '...</span>',
                    '</div>',
                    '</div>'
                ].join('')
            );
            $pop_panel = pop_panel.$panel;
            //this.$container.append(pop_panel.$panel);
            $pop_panel.find('.icon-close').on('click', function () {
                self.hide();
            });
            this.initMoving($pop_panel);
            this.initZooming($pop_panel);
            this.initSubmitting($pop_panel);
            this.resizeImgSize();
        //    this.changeTemplate($pop_panel, 1);
            return pop_panel;
        },
        preview: function () {
            var reader = new window.FileReader(),
                self = this,
                img;

            reader.onload = function (e) {
                img = new Image();
                img.src = e.target.result;
                img.onload = function () {
                    var img_width = this.width;
                    var img_height = this.height;
                    var img_size = '1';
                    self.origin_width = img_width;
                    self.origin_height = img_height;

                    self.showImgOpts(img_width, img_height, img_size);

                    self.$photo_out.attr('src', this.src);
                    self.$photo_in.attr('src', this.src);
                    self.img_width = this.width;
                    self.img_height = this.height;

                    //图片默认大小
                    self.current_width = parseInt(img_width*self.zoom/self.max_zoom/self.min_zoom);
                    self.current_height = parseInt(img_height*self.zoom/self.max_zoom/self.min_zoom);

                    self.new_width = self.current_width;
                    self.new_height = self.current_height;

                    if (self.width * self.img_height / self.img_width > self.height) {
                      //  self.new_width = self.width;
                      //  self.new_height = self.width * self.img_height / self.img_width;
                        self.$photo_out
                            .style('width', self.new_width + 'px').style('height', self.new_height + 'px')
                            .style('top', ((self.height - self.new_height) / 2 + 30) + 'px')
                            .style('left', '30px');
                        self.$photo_in
                            .style('width', self.new_width + 'px').style('height', self.new_height + 'px')
                            .style('top', ((self.height - self.new_height) / 2 + 30) + 'px')
                            .style('left', '30px');
                    } else {
                      //  self.new_height = self.height;
                      //  self.new_width = self.height * self.img_width / self.img_height;
                        self.$photo_out
                            .style('width', self.new_width + 'px').style('height', self.new_height + 'px')
                            .style('left', ((self.width - self.new_width) / 2 + 30) + 'px')
                            .style('top', '30px');
                        self.$photo_in
                            .style('width', self.new_width + 'px').style('height', self.new_height + 'px')
                            .style('left', ((self.width - self.new_width) / 2 + 30) + 'px')
                            .style('top', '30px');
                    }
                    self.setMinSize();
                    self.showEdit();
                };
            };

            if (self.current_file) {
                reader.readAsDataURL(self.current_file);
            }
        },
        setMinSize: function() {
            var self = this;
            self.min_width = self.new_width / self.max_zoom / self.min_zoom;
            self.min_height = self.new_height / self.max_zoom / self.min_zoom;
        },

        showEdit: function() {
            s('.img-opts-edit').style('visibility', 'visible');
            s('.reload-button').style('visibility', 'visible');
            s('.da-upload-img').hide();
        },

        showImgOpts: function(width, height, size) {
            s('#img-width').html(width);
            s('#img-height').html(height);
            s('#img-size').html(size);
            this.submit_form.find('input[name="width"]').prop('value', this.width);
            this.submit_form.find('input[name="height"]').prop('value', this.height);
            this.resizeImgWidth(this.width);
            this.resizeImgHeight(this.height);
            this.submit_form.find('input[type="submit"]').style('visibility','visible');
        },

        resizeImgSize: function() {
            var change_width = this.submit_form.find('input[name="width"]');
            var change_height = this.submit_form.find('input[name="height"]');
            var self = this;
            change_width.on('change', function () {
                self.resizeImgWidth(s(this).prop('value'));
            });
            change_height.on('change', function () {
                self.resizeImgHeight(s(this).prop('value'));
            });
        },

        resizeImgWidth: function(width) {
            this.width = width;
            this.$photo_in_wrap
                .style('width', width + 'px');
        },

        resizeImgHeight: function(height) {
            this.height = height;
            var wrap_height = parseInt(height) + 60;
            this.$photo_out_wrap.style('height', wrap_height + 'px');
            s('.pop-layout-wrapper').style('height', wrap_height + 'px');
            this.$photo_in_wrap
                .style('height', height + 'px');
        },


        initMoving: function ($pop_panel) {
            var is_moving = false,
                mx,
                my,
                x,
                y,
                $photo_out = $pop_panel.find('.photo-out'),
                $photo_in = $pop_panel.find('.photo-in'),
                photo_out = $photo_out[0],
                photo_in = $photo_in[0],
                self = this;

            $photo_in
                .on('mousedown', function (e) {
                    mx = e.pageX;
                    my = e.pageY;
                    is_moving = true;
                })
                .on('mousemove', function (e) {
                    if (!is_moving) {
                        return false;
                    }
                    z.cancelEvent(e);
                    x = parseFloat(photo_in.style.left);
                    y = parseFloat(photo_in.style.top);
                    x += e.pageX - mx;
                    y += e.pageY - my;
                    if (x > 30) {
                        x = 30;
                    } else if (x < self.wrap_width - 30 - self.new_width) {
                        x =  self.wrap_width - 30 - self.new_width;
                    }
                    if (y > 30) {
                        y = 30;
                    } else if (y < self.wrap_height - 30 - self.new_height) {
                        y = self.wrap_height - 30 - self.new_height;
                    }

                    photo_in.style.left = x + 'px';
                    photo_in.style.top = y + 'px';
                    photo_out.style.left = x + 'px';
                    photo_out.style.top = y + 'px';
                    mx = e.pageX;
                    my = e.pageY;
                })
                .on('dragstart', function (e) {
                    z.cancelEvent(e);
                })
                .on('mouseout', function (e) {
                    if (is_moving) {
                        z.cancelEvent(e);
                        is_moving = false;
                    }
                })
                .on('mouseup', function (e) {
                    is_moving = false;
                });
        },

        zoomDown: function($pop_panel) {
            var temp_width,
                temp_height,
                rate,
                $photo_out = $pop_panel.find('.photo-out'),
                $photo_in = $pop_panel.find('.photo-in'),
                self = this;
                //&& (self.new_width > self.width && self.new_height > self.height)
            if ((self.zoom >= self.min_zoom)) {
                temp_width = self.new_width - self.img_width / self.max_zoom / self.min_zoom;
                if (temp_width < self.min_width) {
                    temp_width = self.min_width;
                }
                rate = temp_width / self.new_width;
                self.new_width = temp_width;
                self.new_height = self.new_width * self.img_height / self.img_width;
                self.updatePhotoPosWidth(rate);
                self.zoom --;
            }
        },

        zoomUp: function($pop_panel) {
            //(self.new_width < self.img_width)
            var temp_width,
                temp_height,
                rate,
                $photo_out = $pop_panel.find('.photo-out'),
                $photo_in = $pop_panel.find('.photo-in'),
                self = this;
            if (self.zoom <= self.max_zoom) {
                temp_width = self.new_width + self.img_width / self.max_zoom / self.min_zoom;;
                if (temp_width > self.img_width) {
                    temp_width = self.img_width;
                }
                rate = temp_width / self.new_width;
                self.new_width = temp_width;
                self.new_height = self.new_width * self.img_height / self.img_width;
                self.updatePhotoPosWidth(rate);
                self.zoom ++;
            }
        },

        changeTemplate: function($pop_panel, template) {
            var $photo_out = $pop_panel.find('.photo-out'),
                $photo_in = $pop_panel.find('.photo-in');
            var temp;
            if(template == 0) {
                $photo_out.style('-webkit-transform', 'rotate(0deg)');
                $photo_in.style('-webkit-transform', 'rotate(0deg)');
            //    this.width
            } else if(template == 1) {
                $photo_out.style('-webkit-transform', 'rotate(90deg)');
                $photo_in.style('-webkit-transform', 'rotate(90deg)');
            }
        },

        setZoom: function($pop_panel, zoom){
            if(this.zoom < zoom) {
                for(;this.zoom < zoom;) {
                    this.zoomUp($pop_panel);
                }
            } else if(this.zoom > zoom) {
                for(;this.zoom > zoom;) {
                    this.zoomDown($pop_panel);
                }
            }
        },


        initZooming: function ($pop_panel) {
            var self = this;
            $pop_panel.find('.zoom-in').on('click', function () {
                self.zoomDown($pop_panel);
            });
            $pop_panel.find('.zoom-out').on('click', function () {
                self.zoomUp($pop_panel);
            });
        },

        initSubmitting: function ($pop_panel) {
            var $updating = $pop_panel.find('.opes .updating'),
                $error = $pop_panel.find('.error'),
                self = this,
                form = this.submit_form,
                $btn_update = this.submit_form.find('.btn-update');
            $btn_update.on('click', function (e) {
                var x, y, w, h, pt, form_data;

                pt = self.img_width / self.new_width;
                x = (30 - parseFloat(self.$photo_in.style('left'))) * pt;
                y = (30 - parseFloat(self.$photo_in.style('top'))) * pt;
                w = self.width * pt;
                h = self.height * pt;
                //form_data = new window.FormData();

                form.find('input[name="src_x"]').prop('value', x);
                form.find('input[name="src_y"]').prop('value', y);
                form.find('input[name="src_w"]').prop('value', w);
                form.find('input[name="src_h"]').prop('value', h);
                form.find('input[name="dst_w"]').prop('value', self.width);
                form.find('input[name="dst_h"]').prop('value', self.height);
                form.find('input[name="_token"]').prop('value', z.get_token());
                form.find('input[type="submit"]').trigger('click');
            });

        },

        updatePhotoPosWidth: function (rate) {
            var x, y;
            this.$photo_out.style('width', this.new_width + 'px');
            this.$photo_out.style('height', this.new_height + 'px');
            this.$photo_in.style('width', this.new_width + 'px');
            this.$photo_in.style('height', this.new_height + 'px');

            x = parseFloat(this.$photo_in.style('left'));
            y = parseFloat(this.$photo_in.style('top'));
            x = this.wrap_width / 2 - (this.wrap_width / 2 - x) * rate;
            y = this.wrap_height / 2 - (this.wrap_height / 2 - y) * rate;
            if (x > 30) {
                x = 30;
            } else if (x < this.wrap_width - 30 - this.new_width) {
                x =  this.wrap_width - 30 - this.new_width;
            }
            if (y > 30) {
                y = 30;
            } else if (y < this.wrap_height - 30 - this.new_height) {
                y = this.wrap_height - 30 - this.new_height;
            }
            this.$photo_in.style('left', x + 'px');
            this.$photo_in.style('top', y + 'px');
            this.$photo_out.style('left', x + 'px');
            this.$photo_out.style('top', y + 'px');
        }
    });

    z.extend(Zimgeditor.prototype, {
        show: function () {
            this.$input_img.trigger('click');
        },
        hide: function () {
            //this.$pop_panel.addClass('hide');
            this.pop_panel.hide();
            this.$input_img.prop('value', '');
        },
        done: function (callback) {
            this.doneCallback = callback;
            return this;
        }
    });

    return function(opts) {
        return new Zimgeditor(opts);
    }
    /*
    z.imgeditor = function (upload_action, width, height, target, container) {
        return new Zimgeditor(upload_action, width, height, target, container);
    };
    */

});
