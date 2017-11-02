<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="control-group">
    <label class="control-label"><?php echo $label; ?></label>
    <?php if ($description): ?>
        <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?php echo $description; ?>"></i>
    <?php endif; ?>
    <div class="controls sg-controls">
        <div class="ccm-ui">
            <?php echo $view->inc('form.php', ['view' => $view]); ?>
        </div>
    </div>
</div>

<?php // Some ui fixes for composer ?>
<style>
    div#ccm-panel-detail-page-composer div.ccm-panel-detail-content .sg-controls ul.nav-tabs {
        padding-left: 0;
    }
    div#ccm-panel-detail-page-composer .sg-controls {
        background: #fff;
        padding: 9px 20px 17px;
        border: 1px solid #eaeaea;
        margin-bottom: 30px;
    }
    div#ccm-panel-detail-page-composer .sg-controls fieldset {
        margin-left: 0;
        margin-right: 0;
        padding-left: 0;
        padding-right: 0;
        border-radius: 2px;
    }
    div#ccm-panel-detail-page-composer .sg-controls fieldset legend {
        background: #eaeaea;
        text-align: center;
        color: #757575;
        font-weight: normal;
        padding: 9px;
        font-size: 20px;
        border-radius: 3px;
    }
    div.ccm-panel-detail .sg-controls hr {
        margin-left: 0;
        margin-right: 0;
        border-bottom-width: 0;
    }
</style>