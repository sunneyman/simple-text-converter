(function(factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports !== 'undefined') {
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }

}(function($) {

    const SimpleTextConverter = window.SimpleTextConverter;

    new SimpleTextConverter({
      id: 'stc'
    });

    $('body').attr('data-form-style', 'none');
}));
