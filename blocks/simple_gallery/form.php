<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div id="form-container-<?php echo $uniqueID; ?>" class="simple-gallery-form">

    <?php
    echo $app->make('helper/concrete/ui')->tabs([
        ['basic-information-'.$uniqueID, t('Basic information'), true],
        ['dimensions-'.$uniqueID, t('Dimensions')]
    ]);
    ?>

    <div class="js-tab-content ccm-tab-content" id="ccm-tab-content-basic-information-<?php echo $uniqueID; ?>">

        <div class="form-group">
            <?php echo $form->label($view->field('filesetID'), t('File Set').' *'); ?>
            <p class="small text-muted help-text js-text-fileset-selected" <?php if (!$filesetID): ?>style="display: none;"<?php endif; ?>><?php echo t('Order of Images in File Set can be changed <a href="%s" class="js-link-to-file-set" target="_blank" rel="noopener">here</a>.', $app->make('url/manager')->resolve(['/dashboard/files/sets/view_detail/'.$filesetID])); ?></p>
            <p class="small text-muted help-text js-text-fileset-not-selected" <?php if ($filesetID): ?>style="display: none;"<?php endif; ?>><?php echo t('You can create/assign images to File Set in <a href="%s" target="_blank" rel="noopener">File Manager</a>. List of File Sets can be found <a href="%s" class="js-link-to-file-set" target="_blank" rel="noopener">here</a>.', $app->make('url/manager')->resolve(['/dashboard/files/search']), $app->make('url/manager')->resolve(['/dashboard/files/sets'])); ?></p>
            <input type="hidden" class="js-fileset-detail-url" value="<?php echo $app->make('url/manager')->resolve(['/dashboard/files/sets/view_detail/']); ?>">
            <?php echo $form->select($view->field('filesetID'), $filesetID_options, $filesetID, ['class' => 'js-fileset-id']); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label($view->field('lightboxCaption'), t('Lightbox caption')); ?>
            <?php echo $form->select($view->field('lightboxCaption'), $lightboxCaption_options, $lightboxCaption, ['class' => 'js-lightbox-caption']); ?>
        </div>

        <div class="form-group js-common-caption-wrapper" <?php if ($lightboxCaption!='common'): ?>style="display: none;"<?php endif; ?>>
            <?php echo $form->label($view->field('commonCaption'), t('Common caption')); ?>
            <?php echo $form->text($view->field('commonCaption'), $commonCaption, ['maxlength'=>'255']); ?>
        </div>

    </div>


    <div class="js-tab-content ccm-tab-content" id="ccm-tab-content-dimensions-<?php echo $uniqueID; ?>">

        <h4 class="custom-subheading"><?php echo t('Thumbnails'); ?></h4>

        <div class="row">

            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <?php echo $form->label($view->field('thumbnailWidth'), t('Width')); ?>
                    <div class="input-group">
                        <?php $thumbnailWidth = $thumbnailWidth ? $thumbnailWidth : ''; ?>
                        <?php echo $form->number($view->field('thumbnailWidth'), $thumbnailWidth, ['min'=>0, 'max'=>10000]); ?>
                        <span class="input-group-addon">px</span>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <?php echo $form->label($view->field('thumbnailHeight'), t('Height')); ?>
                    <div class="input-group">
                        <?php $thumbnailHeight = !empty($thumbnailHeight) ? $thumbnailHeight : ''; ?>
                        <?php echo $form->number($view->field('thumbnailHeight'), $thumbnailHeight, ['min'=>0, 'max'=>10000]); ?>
                        <span class="input-group-addon">px</span>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <span class="small text-muted help-text-display-original-images"><?php echo t('Leave fields empty to display original Images.'); ?></span>
                </div>
            </div>

        </div>

        <div class="form-group crop-checkbox">
            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox($view->field('thumbnailCrop'), 1, $thumbnailCrop); ?> <?php echo t('Crop (requires width and height)'); ?>
                </label>
            </div>
        </div>

        <hr/>

        <h4 class="custom-subheading"><?php echo t('Fullscreen Images'); ?></h4>

        <div class="row">

            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <?php echo $form->label($view->field('fullscreenWidth'), t('Width')); ?>
                    <div class="input-group">
                        <?php $fullscreenWidth = $fullscreenWidth ? $fullscreenWidth : ''; ?>
                        <?php echo $form->number($view->field('fullscreenWidth'), $fullscreenWidth, ['min'=>0, 'max'=>10000]); ?>
                        <span class="input-group-addon">px</span>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <?php echo $form->label($view->field('fullscreenHeight'), t('Height')); ?>
                    <div class="input-group">
                        <?php $fullscreenHeight = !empty($fullscreenHeight) ? $fullscreenHeight : ''; ?>
                        <?php echo $form->number($view->field('fullscreenHeight'), $fullscreenHeight, ['min'=>0, 'max'=>10000]); ?>
                        <span class="input-group-addon">px</span>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <span class="small text-muted help-text-display-original-images"><?php echo t('Leave fields empty to display original Images.'); ?></span>
                </div>
            </div>

        </div>

        <div class="form-group crop-checkbox">
            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox($view->field('fullscreenCrop'), 1, $fullscreenCrop); ?> <?php echo t('Crop (requires width and height)'); ?>
                </label>
            </div>
        </div>

        <hr/>

        <h4 class="custom-subheading"><?php echo t('Number of columns'); ?></h4>

        <div class="row">

            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <?php echo $form->label($view->field('columnsPhone'), t('Phone (0-575px)').' *'); ?>
                    <?php echo $form->number($view->field('columnsPhone'), $columnsPhone, ['min'=>1, 'max'=>10]); ?>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <?php echo $form->label($view->field('columnsTablet'), t('Tablet (576-991px)').' *'); ?>
                    <?php echo $form->number($view->field('columnsTablet'), $columnsTablet, ['min'=>1, 'max'=>10]); ?>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4">
                <?php echo $form->label($view->field('columnsDesktop'), t('Desktop (992px+)').' *'); ?>
                <?php echo $form->number($view->field('columnsDesktop'), $columnsDesktop, ['min'=>1, 'max'=>10]); ?>
            </div>

        </div>

        <hr/>

        <h4 class="custom-subheading"><?php echo t('Other'); ?></h4>

        <div class="row">

            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <?php echo $form->label($view->field('margin'), t('Space between Images')); ?>
                    <div class="input-group">
                        <?php echo $form->number($view->field('margin'), $margin, ['min'=>0, 'max'=>100]); ?>
                        <span class="input-group-addon">px</span>
                    </div>
                </div>
            </div>

        </div>


    </div>

    <hr/>

    <p class="small text-muted">* <?php echo t('Required fields'); ?></p>

    <script>

        Concrete.event.publish('open.block.simple-gallery', {
            'uniqueID' : '<?php echo $uniqueID; ?>'
        });

    </script>

</div>