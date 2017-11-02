$(function() {

    Concrete.event.bind('open.block.simple-gallery', function(e, data) {

        var uniqueID      = data.uniqueID;
        var formContainer = $('#form-container-'+uniqueID);

        formContainer.on('change', '.js-lightbox-caption', function(e2) {

            e2.preventDefault();

            var lightboxCaption = $(this).val();

            if (lightboxCaption=='common') {

                formContainer.find('.js-common-caption-wrapper').show();

            } else {

                formContainer.find('.js-common-caption-wrapper').hide();

            }

        });

        formContainer.on('change', '.js-fileset-id', function(e2) {

            e2.preventDefault();

            var filesetID = parseInt($(this).val());

            if (filesetID) {

                var filesetDetail = formContainer.find('.js-fileset-detail-url').val();
                formContainer.find('.js-text-fileset-selected a').attr('href', filesetDetail+'/'+filesetID);

                formContainer.find('.js-text-fileset-selected').show();
                formContainer.find('.js-text-fileset-not-selected').hide();

            } else {

                formContainer.find('.js-text-fileset-selected').hide();
                formContainer.find('.js-text-fileset-not-selected').show();

            }

        });

    });

});