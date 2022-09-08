$(function () {

    var simpleGalleryMagnificPopup = (function ($, window, document, undefined) {

        var initMagnificPopup = function (e) {

            $('.js-sg').each(function () {

                $(this).magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    mainClass: 'mfp-img-mobile',
                    image: {
                        verticalFit: true,
                        titleSrc: function (item) {
                            return _.escape(item.el.attr('title'));
                        },
                        tError: _.escape(sgi18n.imageNotLoaded)
                    },
                    tClose: _.escape(sgi18n.close),
                    tLoading: _.escape(sgi18n.loading),
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                        preload: [0, 1],
                        tPrev: _.escape(sgi18n.previous),
                        tNext: _.escape(sgi18n.next),
                        tCounter: _.escape(sgi18n.counter)
                    },
                    removalDelay: 500, //delay removal by X to allow out-animation
                    callbacks: {
                        beforeOpen: function () {
                            // just a hack that adds mfp-anim class to markup
                            this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
                            this.st.mainClass = this.st.el.attr('data-effect');
                        }
                    }
                });

            });

        };

        var init = function () {
            initMagnificPopup();
        };

        return {
            init: init
        };

    })(jQuery, window, document);

    simpleGalleryMagnificPopup.init();

});
