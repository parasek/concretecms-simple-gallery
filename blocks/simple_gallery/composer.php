<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="form-group">
    <label class="control-label form-label"><?php echo $label; ?></label>
    <?php if ($description): ?>
        <i class="fas fa-question-circle launch-tooltip" title="" data-original-title="<?php echo $description; ?>"></i>
    <?php endif; ?>
    <div class="controls sg-controls">
        <?php echo $view->inc('form.php', ['view' => $view]); ?>
    </div>
</div>

<style>
    div#ccm-panel-detail-page-composer .sg-controls {
        padding: 20px;
        border: 1px solid #eaeaea;
        border-radius: 4px;
    }
</style>
