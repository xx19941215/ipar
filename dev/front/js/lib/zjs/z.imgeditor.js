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

        this.width = parseFloat(opts.width);
        this.height = parseFloat(opts.height);

        this.wrap_width = this.width + 60;
        this.wrap_height = this.height + 60;

        this.current_file = null;

        //this.$target = s(target);
        this.$container = s(opts.container || 'body');
        this.$input_img = this.createInputImg();

        this.pop_panel = this.createPopPanel(opts.pop_panel);
        this.$pop_panel = this.pop_panel.$panel;

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

            $input_img = s.create('input').attr('type', 'file').attr('accept', 'image/x-png, image/gif, image/jpeg').addClass('zimgeditor-input-img');
            this.$container.append($input_img);

            $input_img.on('change', function () {
                var file = this.files[0];
                if (!file) {
                    return;
                }
                self.current_file = file;

                self.$pop_panel
                    .style('width', self.wrap_width + 'px')
                    .style('margin-left', (-self.wrap_width / 2) + 'px');

                self.$photo_out_wrap
                    .style('height', (self.wrap_height) + 'px')
                    .style('width', (self.wrap_width) + 'px')
                    .style('left', '0px');
                self.$photo_in_wrap
                    .style('height', self.height + 'px')
                    .style('width', self.width + 'px');

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
                    '<div class="opes right">',
                    '<a class="button primary tiny btn-update">',
                    z.trans('update'),
                    '</a>',
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
                    self.new_width = self.width;
                    self.new_height = self.height;
                    self.$photo_out.attr('src', this.src);
                    self.$photo_in.attr('src', this.src);
                    self.img_width = this.width;
                    self.img_height = this.height;

                    if (self.width * self.img_height / self.img_width > self.height) {
                        self.new_width = self.width;
                        self.new_height = self.width * self.img_height / self.img_width;
                        self.$photo_out
                            .style('width', self.new_width + 'px').style('height', self.new_height + 'px')
                            .style('top', ((self.height - self.new_height) / 2 + 30) + 'px')
                            .style('left', '30px');
                        self.$photo_in
                            .style('width', self.new_width + 'px').style('height', self.new_height + 'px')
                            .style('top', ((self.height - self.new_height) / 2 + 30) + 'px')
                            .style('left', '30px');
                    } else {
                        self.new_height = self.height;
                        self.new_width = self.height * self.img_width / self.img_height;
                        self.$photo_out
                            .style('width', self.new_width + 'px').style('height', self.new_height + 'px')
                            .style('left', ((self.width - self.new_width) / 2 + 30) + 'px')
                            .style('top', '30px');
                        self.$photo_in
                            .style('width', self.new_width + 'px').style('height', self.new_height + 'px')
                            .style('left', ((self.width - self.new_width) / 2 + 30) + 'px')
                            .style('top', '30px');
                    }
                    self.min_width = self.new_width;
                    self.min_height = self.new_height;
                };
            };
            if (self.current_file) {
                reader.readAsDataURL(self.current_file);
            }
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

        initZooming: function ($pop_panel) {
            var temp_width,
                temp_height,
                rate,
                $photo_out = $pop_panel.find('.photo-out'),
                $photo_in = $pop_panel.find('.photo-in'),
                //photo_out = $photo_out[0],
                //photo_in = $photo_in[0],
                self = this;
            $pop_panel.find('.zoom-in').on('click', function () {
                if (self.new_width > self.width && self.new_height > self.height) {
                    temp_width = self.new_width - self.img_width / 10;
                    if (temp_width < self.min_width) {
                        temp_width = self.min_width;
                    }
                    rate = temp_width / self.new_width;
                    self.new_width = temp_width;
                    self.new_height = self.new_width * self.img_height / self.img_width;
                    self.updatePhotoPosWidth(rate);
                }
            });
            $pop_panel.find('.zoom-out').on('click', function () {
                if (self.new_width < self.img_width) {
                    temp_width = self.new_width + self.img_width / 10;
                    if (temp_width > self.img_width) {
                        temp_width = self.img_width;
                    }
                    rate = temp_width / self.new_width;
                    self.new_width = temp_width;
                    self.new_height = self.new_width * self.img_height / self.img_width;
                    self.updatePhotoPosWidth(rate);
                }
            });
        },

        initSubmitting: function ($pop_panel) {
            var $updating = $pop_panel.find('.opes .updating'),
                $error = $pop_panel.find('.error'),
                self = this,
                $btn_update = $pop_panel.find('.btn-update');
            $btn_update.on('click', function (e) {
                var x, y, w, h, pt, form_data;

                pt = self.img_width / self.new_width;
                x = (30 - parseFloat(self.$photo_in.style('left'))) * pt;
                y = (30 - parseFloat(self.$photo_in.style('top'))) * pt;
                w = self.width * pt;
                h = self.height * pt;
                form_data = new window.FormData();

                form_data.append('_token', z.get_token());
                form_data.append('src_x', x);
                form_data.append('src_y', y);
                form_data.append('src_w', w);
                form_data.append('src_h', h);
                form_data.append('dst_w', self.width);
                form_data.append('dst_h', self.height);

                form_data.append('img', self.current_file, self.current_file.name);
                /*
                form_data.append(
                    'resize',
                    JSON.stringify({
                        big: {width: 168, height: 168},
                        mid: {width: 98, height: 98},
                        small: {width: 28, height: 28}
                    })
                );
                */

                $btn_update.addClass('hide');
                $updating.removeClass('hide');
                $error.addClass('hide');

                net.ajax({
                    url: self.upload_action,
                    method: 'post',
                    dataType: 'json',
                    withCredentials: true
                }).done(function (data) {
                    var pack = z.pack(data),
                        avt_url;

                    $btn_update.removeClass('hide');
                    $updating.addClass('hide');
                    if (pack.isOk()) {
                        avt_url = pack.getItem('avt_url');
                        if (avt_url) {
                            self.doneCallback.call(self, avt_url);
                        }
                    } else {
                        window.console.log(data);
                    }
                    self.hide();
                }).send(form_data);
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
