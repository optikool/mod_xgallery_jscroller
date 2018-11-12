<?php
/*
 * @package Joomla 3.0
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component XMovie Component
 * @copyright Copyright (C) Dana Harris optikool.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */ 
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

if(!defined('COM_XGALLERY_BASE_PATH_COLLECTION')) {
	$cfgParams =  JComponentHelper::getParams('com_xgallery');

	$galleryPathIsExternal = $cfgParams->get('image_external');
	$galleryCollectionFolder = $cfgParams->get('image_path', 'xgcollections');
	$comMediaImagePath = JComponentHelper::getParams('com_media')->get('image_path', 'images');
	$galleryBasePathThumbnail = JPATH_ROOT;
	
	if($galleryPathIsExternal) {
		$galleryBasePathCollection = $cfgParams->get('image_external_path');
	} else {
		$galleryBasePathCollection = $galleryBasePathThumbnail . DS . $comMediaImagePath;;
	}
		
	$galleryBasePathCollectionFull = $galleryBasePathCollection . DS . $galleryCollectionFolder;
	define('COM_XGALLERY_BASE_PATH_COLLECTION', $galleryBasePathCollectionFull);
}

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

require_once (JPATH_SITE.DS.'components'.DS.'com_xgallery'.DS.'router.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_xgallery'.DS.'helpers'.DS.'gallery.php');

GalleryHelper::setCookieParams();

$document = JFactory::getDocument();
$lists = modXGalleryJScrollerHelper::getList($params);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

if($params->get('enable_css') || $params->get('enable_jquery')) {
	modXGalleryJScrollerHelper::addStyleScripts($params);
}

if($params->get('init_script', 0)) {
	modXGalleryJScrollerHelper::addInitScript($params);
}

require JModuleHelper::getLayoutPath('mod_xgallery_jscroller', $params->get('layout', 'default'));
?>
