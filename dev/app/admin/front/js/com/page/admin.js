define([
    '../lib/z',
    '../lib/z.selector'
], function(z, s) {
    s('.hide-off-canvas').on('click', function() {
        s('.off-canvas-wrapper-inner').removeClass('is-off-canvas-open is-open-left');
        s('.off-canvas').removeClass('is-open');
    });
    s('.show-off-canvas').on('click', function() {
        s('.off-canvas-wrapper-inner').addClass('is-off-canvas-open is-open-left');
        s('.off-canvas').addClass('is-open');
    });
});
