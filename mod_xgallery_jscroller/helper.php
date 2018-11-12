<?php
/*
 * @package Joomla 3.0
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component XGallery Component
 * @copyright Copyright (C) Dana Harris optikool.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */ 
defined('_JEXEC') or die('Restricted access');

class modXGalleryJScrollerHelper {
	private $_moduleclass_sfx;
	private $_number_collection;	
	private $_number_to_view;
	private $_category_id;
	private $_catids;
	private $_scroll_sort_method;
	private $_scroll_direction;
	private $_scroll_loop;
	private $_scroll_speed;
	private $_scroll_easing;
	private $_show_collection_name;
	private $_show_date;
	private $_show_hits;
	private $_show_category;
	private $_category_list;
	private $_thumb_width;
	private $_thumb_height;
	private $_query;
	private $_catslug;
	private $_collslug;
	private $_catnames;
	private $_enable_css;
	private $_auto_scroll;
	private $_auto_scroll_step;
	private $_auto_scroll_interval;
	private $_auto_scroll_autopause;
	private $_load_jquery;
		
	public function __construct(&$params) {		
		global $scroller_id;
		
		$this->slideid	= ++$scroller_id;			

		$this->_moduleclass_sfx		= $params->get('moduleclass_sfx', '');
		$this->_number_collection	= $params->get('number_collection', 5);	
		$this->_number_to_view		= $params->get('number_to_view', 5);
		$this->_category_id		= $params->get('category_id', '');
		$this->_scroll_sort_method	= $params->get('scroll_sort_method', 'random');
		$this->_scroll_direction	= $params->get('scroll_direction', 'vertical');
		$this->_scroll_loop		= $params->get('scroll_loop', 0);
		$this->_scroll_speed		= $params->get('scroll_speed', 400);
		$this->_scroll_easing		= $params->get('scroll_easing', 'swing');
		$this->_show_collection_name	= $params->get('show_collection_name', 1);
		$this->_show_date		= $params->get('show_date', 0);
		$this->_show_hits		= $params->get('show_hits', 1);
		$this->_show_category		= $params->get('show_category', 0);
		$this->_auto_scroll		= $params->get('auto_scroll', 0);
		$this->_auto_scroll_step	= $params->get('auto_scroll_step', 1);
		$this->_auto_scroll_interval	= $params->get('auto_scroll_interval', 1000);
		$this->_auto_scroll_autopause	= $params->get('auto_scroll_autopause', true);
		$this->_thumb_width 		= $params->get('thumb_width', 75);
		$this->_thumb_height 		= $params->get('thumb_height', 75);
		$this->_enable_css		= $params->get('enable_css', 0);
		$this->_load_jquery		= $params->get('load_jquery', 0);
	}

	public static function getList(&$params) {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$cat_ids = implode(',', $params->get('category_id'));
		$itemids = GalleryHelper::getItemIds();
		$tmpRows = array();
		$cTime = time();
		$sort_method = $params->get('scroll_sort_method', 'random');
		$cLimit = $params->get('number_collection');
		$items = array();
				
		// Create a new query object.
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		// Select required fields from the categories.
		$query->select('a.*');
		$query->from($db->quoteName('#__xgallery').' AS a');
		
		// Join over the categories.
		$query->select('c.alias AS catalias, c.title AS cattitle');
		$query->join('LEFT', $db->quoteName('#__categories') . ' AS c ON c.id = a.catid');
		
		$query->where('a.access IN ('.$groups.')');
		$query->where('a.catid IN ('.$cat_ids.')');
				
		$query->where('a.published = 1');
		
		switch($sort_method) {
			case 'popular':
				$sort_method = "hits DESC";
				break;
			case 'oldest':
				$sort_method = "creation_date ASC";
				break;
			case 'random':
				$sort_method= "RAND()";
				break;
			case 'newest':
			default:
				$sort_method= "creation_date DESC";
				break;
		}
			
		$query->order($sort_method);
		
		$db->setQuery($query, 0, $cLimit);
		$rows = $db->loadObjectList();
		
		if(count($rows) < $cLimit) {
			$cLimit = count($rows);
		}
		
		// Convert the params field into an object, saving original in _params
		for ($i = 0; $i < $cLimit; $i++) {
			$items[$i] = $rows[$i];
			$cid = $items[$i]->catid;			
			$user = JFactory::getUser($items[$i]->user_id);			
			$items[$i]->submitter = $user->username;
			
			if(isset($itemids[$cid]['itemId']) && $itemids[$cid]['itemId'] != '') {
				$items[$i]->itemId = $itemids[$cid]['itemId'];
			} else {
				$items[$i]->itemId = $itemids[0]['itemId'];
			}			
		}
		
		unset($rows);
		$rows = $items;
		
		return $rows;
	}

	public static function addStyleScripts(&$params) {
		$document = JFactory::getDocument();
	
		if($params->get('enable_css')) {
			$document->addStyleSheet(JURI::root(true).'/modules/mod_xgallery_jscroller/css/style.css');
		}
		
	}
	
	public static function addInitScript(&$params) {
		$document = JFactory::getDocument();
		$script = '';				
		$options = array();
		
		/* Gneral */
		if($params->get('bxslider_mode') != 'horizontal') {
			array_push($options, "mode: '" . $params->get('bxslider_mode') . "'");
		}
		
		if((int) $params->get('bxslider_speed') != 500) {
			array_push($options, "speed: " . (int) $params->get('bxslider_speed'));
		}
		
		if((int) $params->get('bxslider_slidemargin') != 0) {
			array_push($options, "slideMargin: " . (int) $params->get('bxslider_slidemargin'));
		}
		
		if((int) $params->get('bxslider_startslide') != 0) {
			array_push($options, "startSlide: " . (int) $params->get('bxslider_startslide'));
		}
		
		if((int) $params->get('bxslider_randomstart') != 0) {
			array_push($options, "randomStart: false");
		}
		
		if($params->get('bxslider_slideselector') != '') {
			array_push($options, "slideSelector: '" . $params->get('bxslider_slideselector') . "'");
		}
		
		if((int) $params->get('bxslider_infiniteloop') != 1) {
			array_push($options, "infiniteLoop: false");
		}
		
		if((int) $params->get('bxslider_hidecontrolonend') != 0) {
			array_push($options, "hideControlOnEnd: true");
		}
		
		if($params->get('bxslider_easing') != '') {
			array_push($options, "easing: '" . $params->get('bxslider_easing') . "'");
		}
		
		if((int) $params->get('bxslider_captions') != 0) {
			array_push($options, "captions: true");
		}
		
		if((int) $params->get('bxslider_ticker') != 0) {
			array_push($options, "ticker: true");
		}
		
		if((int) $params->get('bxslider_tickerhover') != 0) {
			array_push($options, "tickerHover: true");
		}
		
		if((int) $params->get('bxslider_adaptiveheight') != 0) {
			array_push($options, "adaptiveHeight: true");
		}
		
		if((int) $params->get('bxslider_adaptiveheightspeed') != 500) {
			array_push($options, "adaptiveHeightSpeed: " . (int) $params->get('bxslider_adaptiveheightspeed'));
		}
		
		if((int) $params->get('bxslider_video') != 0) {
			array_push($options, "video: true");
		}
		
		if((int) $params->get('bxslider_responsive') != 1) {
			array_push($options, "responsive: false");
		}
		
		if((int) $params->get('bxslider_usecss') != 1) {
			array_push($options, "useCSS: false");
		}
		
		if($params->get('bxslider_preloadimages') != 'visible') {
			array_push($options, "preloadImages: '" . $params->get('bxslider_preloadimages') . "'");
		}
		
		if((int) $params->get('bxslider_touchenabled') != 0) {
			array_push($options, "touchEnabled: false");
		}
		
		if((int) $params->get('bxslider_swipethreshold') != 50) {
			array_push($options, "swipeThreshold: " . (int) $params->get('bxslider_swipethreshold'));
		}
		
		if((int) $params->get('bxslider_onetoonetouch') != 1) {
			array_push($options, "oneToOneTouch: false");
		}
		
		if((int) $params->get('bxslider_preventdefaultswipex') != 1) {
			array_push($options, "preventDefaultSwipeX: false");
		}
		
		if((int) $params->get('bxslider_preventdefaultswipey') != 0) {
			array_push($options, "preventDefaultSwipeY: true");
		}
		
		/* Pager */
		if((int) $params->get('bxslider_pager') != 1) {
			array_push($options, "pager: false");
		}
		
		if($params->get('bxslider_pagertype') != 'full') {
			array_push($options, "pagerType: '" . $params->get('bxslider_pagertype') . "'");
		}
		
		if($params->get('bxslider_pagershortseparator') != '/') {
			array_push($options, "pagerShortSeparator: '" . $params->get('bxslider_pagershortseparator') . "'");
		}
		
		if($params->get('bxslider_pagerselector') != '') {
			array_push($options, "pagerSelector: '" . $params->get('bxslider_pagerselector') . "'");
		}
		
		if($params->get('bxslider_pagercustom') != 'null') {
			array_push($options, "pagerCustom: '" . $params->get('bxslider_pagercustom') . "'");
		}
		
		if($params->get('bxslider_buildpager') != 'null') {
			array_push($options, "buildPager: '" . $params->get('bxslider_buildpager') . "'");
		}
		
		/* Controls */
		array_push($options, "controls: " . $params->get('bxslider_controls'));
		if((int) $params->get('bxslider_controls') != 1) {
			array_push($options, "controls: false");
		}
		
		if($params->get('bxslider_nexttext') != 'Next') {
			array_push($options, "nextText: '" . $params->get('bxslider_nexttext') . "'");
		}
		
		if($params->get('bxslider_prevtext') != 'Prev') {
			array_push($options, "prevText: '" . $params->get('bxslider_prevtext') . "'");
		}
		
		if($params->get('bxslider_nextselector') != 'null') {
			array_push($options, "nextSelector: '" . $params->get('bxslider_nextselector') . "'");
		}
		
		if($params->get('bxslider_prevselector') != 'null') {
			array_push($options, "prevSelector: '" . $params->get('bxslider_prevselector') . "'");
		}
		
		if((int) $params->get('bxslider_autocontrols') != 0) {
			array_push($options, "autoControls: true");
		}
		
		if($params->get('bxslider_starttext') != 'Start') {
			array_push($options, "startText: '" . $params->get('bxslider_starttext') . "'");
		}
		
		if($params->get('bxslider_stoptext') != 'Stop') {
			array_push($options, "stopText: '" . $params->get('bxslider_stoptext') . "'");
		}
		
		if((int) $params->get('bxslider_autocontrolscombine') != 0) {
			array_push($options, "autoControlsCombine: true");
		}
		
		if($params->get('bxslider_autocontrolsselector') != 'null') {
			array_push($options, "autoControlsSelector: '" . $params->get('bxslider_autocontrolsselector') . "'");
		}
		/* Auto */
		if((int) $params->get('bxslider_auto') != 0) {
			array_push($options, "auto: true");
		}
		
		if((int) $params->get('bxslider_pause') != 4000) {
			array_push($options, "pause: true");
		}
		
		if((int) $params->get('bxslider_autostart') != 1) {
			array_push($options, "autoControls: false");
		}
		
		if($params->get('bxslider_autodirection') != 'next') {
			array_push($options, "autoDirection: '" . $params->get('bxslider_autodirection') . "'");
		}
		
		if((int) $params->get('bxslider_autohover') != 0) {
			array_push($options, "autoHover: true");
		}
		
		if((int) $params->get('bxslider_autodelay') != 0) {
			array_push($options, "autoDelay: " . (int) $params->get('bxslider_autodelay') );
		}
		
		/* Carousel */
		if((int) $params->get('bxslider_minslides') != 1) {
			array_push($options, "minSlides: " . (int) $params->get('bxslider_minslides') );
		}
		
		if((int) $params->get('bxslider_maxslides') != 1) {
			array_push($options, "maxSlides: " . (int) $params->get('bxslider_maxslides') );
		}
		
		if((int) $params->get('bxslider_moveslides') != 0) {
			array_push($options, "moveSlides: " . (int) $params->get('bxslider_moveslides') );
		}
		
		if((int) $params->get('bxslider_slidewidth') != 0) {
			array_push($options, "slideWidth: " . (int) $params->get('bxslider_slidewidth') );
		}
		
		$option = implode(',', $options);
		$sfx = '';
		
		if($params->get('moduleclass_sfx') != '') {
			$sfx = $params->get('moduleclass_sfx');	
		}
		
		$script = "jQuery(document).ready(function(){
						jQuery('#slider{$sfx}').bxSlider({
							{$option}
						});
					});";
							
		$document->addScriptDeclaration($script);
	}
}

?>
