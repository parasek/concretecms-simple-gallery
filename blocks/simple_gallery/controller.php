<?php namespace Concrete\Package\SimpleGallery\Block\SimpleGallery;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Block\BlockController;
use Concrete\Core\File\Set\SetList as FileSetList;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\File\File;
use Concrete\Core\Page\Page;

class Controller extends BlockController
{

    protected $btTable = 'btSimpleGallery';
    protected $btExportTables = ['btSimpleGallery'];
    protected $btInterfaceWidth = '800';
    protected $btInterfaceHeight = '650';
    protected $btWrapperClass = 'ccm-ui';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutputLifetime = 0;

    protected $btDefaultSet = 'multimedia'; // basic, navigation, form, express, social, multimedia

    protected $filesetID;
    protected $lightboxCaption;
    protected $commonCaption;
    protected $fullscreenWidth;
    protected $fullscreenHeight;
    protected $fullscreenCrop;
    protected $columnsPhone;
    protected $columnsTablet;
    protected $columnsDesktop;
    protected $margin;

    private $uniqueID;

    public function getBlockTypeName() {
        return t('Simple Gallery');
    }

    public function getBlockTypeDescription() {
        return t('Create image gallery based on File Set.');
    }

    public function getSearchableContent() {

        $content = [];

        return implode(' ', $content);

    }

    public function on_start() {

        // Unique identifier
        $this->uniqueID = $this->app->make('helper/validation/identifier')->getString(18);
        $this->set('uniqueID', $this->uniqueID);

        $this->set('filesetID', $this->filesetID);
        $this->set('lightboxCaption', $this->lightboxCaption);
        $this->set('commonCaption', $this->commonCaption);
        $this->set('fullscreenWidth', $this->fullscreenWidth);
        $this->set('fullscreenHeight', $this->fullscreenHeight);
        $this->set('fullscreenCrop', $this->fullscreenCrop);
        $this->set('columnsPhone', $this->columnsPhone);
        $this->set('columnsTablet', $this->columnsTablet);
        $this->set('columnsDesktop', $this->columnsDesktop);
        $this->set('margin', $this->margin);

        // File Set (filesetID) options
        $filesetID_options   = [];
        $filesetID_options[] = '----';

        $fileSets = $this->getFileSets();
        foreach ($fileSets as $k => $v) {
            $filesetID_options[$k] = h($v);
        }

        $this->set('filesetID_options', $filesetID_options);

        // Lightbox caption (lightboxCaption) options
        $lightboxCaption_options = [];
        $lightboxCaption_options['none']       = t('Do not display caption');
        $lightboxCaption_options['file_title'] = t('Display caption based on File Title');
        $lightboxCaption_options['page_title'] = t('Display common caption based on Page Name');
        $lightboxCaption_options['common']     = t('Display common caption');

        $this->set('lightboxCaption_options', $lightboxCaption_options);

    }

    public function registerViewAssets($outputContent = '') {

        $al = AssetList::getInstance();

        // Font awesome
        $this->requireAsset('css', 'font-awesome');

        // Localization (we can't just register "javascript-localized" because it breaks combined css in half, when cache is on)
        $sgi18n = array();
        $sgi18n['imageNotLoaded'] = t('%sThe image%s could not be loaded.', '<a href=\"%url%\">', '</a>');
        $sgi18n['close']          = t('Close (Esc)');
        $sgi18n['loading']        = t('Loading...');
        $sgi18n['previous']       = t('Previous (Left arrow key)');
        $sgi18n['next']           = t('Next (Right arrow key)');
        $sgi18n['counter']        = t('%curr% of %total%');
        $content = '';
        $content .= 'var sgi18n = ';
        $content .= json_encode($sgi18n);
        $content .=  ';';
        $this->addFooterItem('<script>' . $content . '</script>');

        // Magnific popup
        $this->requireAsset('javascript', 'feature/imagery/frontend');
        $this->requireAsset('css', 'feature/imagery/frontend');

        // Load underscore for escaping chars in js files
        $al->register('javascript', 'simple-gallery/underscore', 'blocks/simple_gallery/vendor/underscore/underscore-min.js', [], 'simple_gallery');
        $this->requireAsset('javascript', 'simple-gallery/underscore');

        $al->register('javascript', 'simple-gallery/magnific-popup', 'blocks/simple_gallery/js_files/magnific-popup.js', [], 'simple_gallery');
        $this->requireAsset('javascript', 'simple-gallery/magnific-popup');

        // Css
        $al->register('css', 'simple-gallery/styles', 'blocks/simple_gallery/css_files/simple-gallery.css', [], 'simple_gallery');
        $this->requireAsset('css', 'simple-gallery/styles');

        // Inline css
        $inlineCss = $this->renderCss();
        if ($inlineCss) {
            $this->addHeaderItem('<style>' . $inlineCss . '</style>');
        }

    }

    public function add() {

        // Default values when adding block
        $this->set('columnsPhone', 2);
        $this->set('columnsTablet', 3);
        $this->set('columnsDesktop', 4);
        $this->set('margin', 5);
        $this->set('thumbnailWidth', 450);
        $this->set('thumbnailHeight', 300);
        $this->set('thumbnailCrop', 1);

        $this->addEdit();

    }

    public function edit() {

        $this->addEdit();

    }

    public function addEdit() {

        $al = AssetList::getInstance();

        // Load form.css
        $al->register('css', 'simple-gallery/form', 'blocks/simple_gallery/css_files/form.css', [], 'simple_gallery');
        $this->requireAsset('css', 'simple-gallery/form');

        // Make $app available in view
        $this->set('app', $this->app);

    }

    public function view() {

        // Get images
        $images = $this->getImages($this->filesetID);
        $images = $this->processImages($images);
        $this->set('images', $images);

    }

    public function save($args) {

        // Basic fields
        $args['customCaption'] = !empty($args['customCaption']) ? trim($args['customCaption']) : '';

        // Save checkboxes (if unchecked - they are not present in $_POST table)
        $checkboxes = [];
        $checkboxes[] = 'thumbnailCrop';
        $checkboxes[] = 'fullscreenCrop';

        foreach ($checkboxes as $value) {
            $args[$value] = isset($args[$value]) ? 1 : 0;
        }

        // Int fields which are allowed to be empty, should be = 0 in database (strict mode)
        $intFieldsAllowedAsEmpty = [];
        $intFieldsAllowedAsEmpty[] = 'thumbnailWidth';
        $intFieldsAllowedAsEmpty[] = 'thumbnailHeight';
        $intFieldsAllowedAsEmpty[] = 'fullscreenWidth';
        $intFieldsAllowedAsEmpty[] = 'fullscreenHeight';
        $intFieldsAllowedAsEmpty[] = 'margin';

        foreach ($intFieldsAllowedAsEmpty as $value) {
            $args[$value] = !empty($args[$value]) ? $args[$value] : 0;
        }

        parent::save($args);

    }

    public function duplicate($newBlockID) {

        parent::duplicate($newBlockID);

    }

    public function delete() {

    }

    public function validate($args) {

        $error = $this->app->make('helper/validation/error');

        // Required fields
        $requiredFields = [];
        $requiredFields['filesetID']      = t('File Set');
        $requiredFields['columnsPhone']   = t('Phone (0-575px)');
        $requiredFields['columnsTablet']  = t('Tablet (576-991px)');
        $requiredFields['columnsDesktop'] = t('Desktop (992px+)');

        foreach ($requiredFields as $requiredFieldHandle => $requiredFieldLabel) {

            if (empty($args[$requiredFieldHandle])) {
                $error->add(t('Field "%s" is required.', $requiredFieldLabel));
            }

        }

        if (!empty($args['thumbnailCrop']) AND (empty($args['thumbnailWidth']) OR empty($args['thumbnailHeight']))) {
            $error->add(t('To crop Thumbnails you need to specify width and height.'));
        }

        if (!empty($args['fullscreenCrop']) AND (empty($args['fullscreenWidth']) OR empty($args['fullscreenHeight']))) {
            $error->add(t('To crop Fullscreen Images you need to specify width and height.'));
        }

        return $error;

    }

    public function composer() {

        $al = AssetList::getInstance();
        $al->register('javascript', 'simple-gallery/auto-js', 'blocks/simple_gallery/auto.js', [], 'simple_gallery');
        $this->requireAsset('javascript', 'simple-gallery/auto-js');

        $this->edit();

    }

    public function scrapbook() {

        $this->edit();

    }

    private function getFileSets() {

        $fileSetList = new FileSetList();
        $fileSets = $fileSetList->get();

        $fileSetsArray = [];

        foreach($fileSets as $fileSet) {
            $fileSetsArray[$fileSet->getFileSetID()] = $fileSet->getFileSetName();
        }

        return $fileSetsArray;

    }

    private function getImages($filesetID) {

        $images = [];

        $fileSet = FileSet::getByID($filesetID);

        if (is_object($fileSet)) {

            $fileList = new FileList();
            $fileList->filterBySet($fileSet);
            $fileList->filterByType(FileType::T_IMAGE);
            $fileList->sortByFileSetDisplayOrder();

            $images = $fileList->getResults();

        }

        return $images;

    }

    private function processImages(array $images) {

        $ih = $this->app->make('helper/image');

        $c = Page::getCurrentPage();

        $imagesNewArray = [];

        if (is_array($images) AND count($images)>0) {

            foreach ($images as $key => $image) {

                // Thumbnail image
                $thumbnailUrl    = $image->getRelativePath();
                $thumbnailWidth  = $image->getAttribute('width');
                $thumbnailHeight = $image->getAttribute('height');

                if (($this->thumbnailWidth OR $this->thumbnailHeight) AND ($thumbnailWidth>$this->thumbnailWidth OR $thumbnailHeight>$this->thumbnailHeight)) {
                    $thumbnailObject = File::getByID($image->getFileID());
                    if (is_object($thumbnailObject) AND $thumbnailObject->canEdit()) {
                        $thumbnail       = $ih->getThumbnail($thumbnailObject, $this->thumbnailWidth, $this->thumbnailHeight, $this->thumbnailCrop);
                        $thumbnailUrl    = $thumbnail->src;
                        $thumbnailWidth  = $thumbnail->width;
                        $thumbnailHeight = $thumbnail->height;
                    }
                }

                $imagesNewArray[$key]['thumbnailUrl']    = $thumbnailUrl;
                $imagesNewArray[$key]['thumbnailWidth']  = $thumbnailWidth;
                $imagesNewArray[$key]['thumbnailHeight'] = $thumbnailHeight;

                // Fullscreen image
                $fullscreenUrl    = $image->getRelativePath();
                $fullscreenWidth  = $image->getAttribute('width');
                $fullscreenHeight = $image->getAttribute('height');

                if (($this->fullscreenWidth OR $this->fullscreenHeight) AND ($fullscreenWidth>$this->fullscreenWidth OR $fullscreenHeight>$this->fullscreenHeight)) {
                    $fullscreenObject = File::getByID($image->getFileID());
                    if (is_object($fullscreenObject) AND $fullscreenObject->canEdit()) {
                        $fullscreen       = $ih->getThumbnail($fullscreenObject, $this->fullscreenWidth, $this->fullscreenHeight, $this->fullscreenCrop);
                        $fullscreenUrl    = $fullscreen->src;
                        $fullscreenWidth  = $fullscreen->width;
                        $fullscreenHeight = $fullscreen->height;
                    }
                }

                $imagesNewArray[$key]['fullscreenUrl']    = $fullscreenUrl;
                $imagesNewArray[$key]['fullscreenWidth']  = $fullscreenWidth;
                $imagesNewArray[$key]['fullscreenHeight'] = $fullscreenHeight;

                // Link title attribute
                $caption = '';
                if ($this->lightboxCaption=='file_title') {
                    $caption = $image->getTitle();
                }
                if ($this->lightboxCaption=='page_title') {
                    $caption = $c->getCollectionName();
                }
                if ($this->lightboxCaption=='common') {
                    $caption = $this->commonCaption;
                }
                $imagesNewArray[$key]['caption'] = $caption;

                // Image alt attribute
                if ($caption) {
                    $imagesNewArray[$key]['alt'] = $caption;
                } else {
                    if ($image->getTitle()) {
                        $imagesNewArray[$key]['alt'] = $image->getTitle();
                    } else {
                        $imagesNewArray[$key]['alt'] = $image->getFileName();
                    }
                }

            }

        }

        return $imagesNewArray;

    }

    private function renderCss() {

        $uniqueParentContainer = '.sg-'.$this->bID;

        $css = '';

        // 1. Number of columns

        // columnsPhone
        if ($this->columnsPhone AND $this->margin AND ($this->columnsPhone!=2 OR $this->margin!=5)) {

            $calcWidth = '';
            $calcWidth .= (100/$this->columnsPhone).'%';
            if ($this->margin) {
                $calcWidth .= ' - '.($this->margin*2).'px';
            }

            $css .= '@media only screen and (max-width: 575px) {';
                $css .= '.ccm-page '.$uniqueParentContainer.' .sg-item {';
                    $css .= 'width: calc('.$calcWidth.');';
                $css .= '}';
            $css .= '}';

        }

        // columnsTablet
        if ($this->columnsTablet AND $this->margin AND ($this->columnsTablet!=3 OR $this->margin!=5)) {

            $calcWidth = '';
            $calcWidth .= (100/$this->columnsTablet).'%';
            if ($this->margin) {
                $calcWidth .= ' - '.($this->margin*2).'px';
            }

            $css .= '@media only screen and (min-width: 576px) and (max-width: 991px) {';
                $css .= '.ccm-page '.$uniqueParentContainer.' .sg-item {';
                    $css .= 'width: calc('.$calcWidth.');';
                $css .= '}';
            $css .= '}';

        }

        // columnsDesktop
        if ($this->columnsDesktop AND $this->margin AND ($this->columnsDesktop!=4 OR $this->margin!=5)) {

            $calcWidth = '';
            $calcWidth .= (100/$this->columnsDesktop).'%';
            if ($this->margin) {
                $calcWidth .= ' - '.($this->margin*2).'px';
            }

            $css .= '@media only screen and (min-width: 992px) {';
                $css .= '.ccm-page '.$uniqueParentContainer.' .sg-item {';
                    $css .= 'width: calc('.$calcWidth.');';
                $css .= '}';
            $css .= '}';

        }

        // 2. Margin (space between images))

        if ($this->margin AND $this->margin!=5) {

            $css .= '.ccm-page '.$uniqueParentContainer.' {margin-left: -'.$this->margin.'px;';
                $css .= 'margin-right: -'.$this->margin.'px;';
            $css .= '}';
            $css .= '.ccm-page '.$uniqueParentContainer.' .sg-item {';
                $css .= 'margin: '.$this->margin.'px;';
            $css .= '}';

        } elseif (!$this->margin) {

            $css .= '.ccm-page '.$uniqueParentContainer.' {';
                $css .= 'margin-left: 0;';
                $css .= 'margin-right: 0;';
            $css .= '}';
                $css .= '.ccm-page '.$uniqueParentContainer.' .sg-item {';
                $css .= 'margin: 0;';
            $css .= '}';

        }

        return $css;

    }

}
