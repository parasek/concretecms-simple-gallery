<?php namespace Concrete\Package\SimpleGallery;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends Package
{

    protected $pkgHandle = 'simple_gallery';
    protected $appVersionRequired = '8.2.1';
    protected $pkgVersion = '1.0.7';

    public function getPackageName() {
        return t('Simple Gallery');
    }

    public function getPackageDescription() {
        return t('Create image gallery based on File Set.');
    }

    public function on_start() {

    }

    public function install() {

        $pkg = parent::install();

        // Install blocks
        if ( ! is_object(BlockType::getByHandle('simple_gallery'))) {
            BlockType::installBlockType('simple_gallery', $pkg);
        }

    }

    public function uninstall() {

        parent::uninstall();

        $db = $this->app->make('database')->connection();

        // Delete package tables
        $db->executeQuery('DROP TABLE IF EXISTS btSimpleGallery');

    }

}