<?php
/*
 * @package Joomla 2.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component XGallery Component
 * @copyright Copyright (C) Dana Harris optikool.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */ 
defined('_JEXEC') or die ('Restricted access.');
$scroller_id = $params->get('scroller_id');
$moduleclass_sfx = $params->get('moduleclass_sfx');
$listCount = 0;
$numberOfItems = count($lists);
$isActiveItem = true;

if($numberOfItems > 0) { ?>
<div id="<?php echo $scroller_id; ?>" class="carousel slide <?php echo $moduleclass_sfx; ?>" data-ride="carousel">
  	<div class="carousel-inner">
	<?php foreach($lists as $item) {
		$link = JRoute::_("index.php?option=com_xgallery&view=single&catid={$item->catid}:{$item->catalias}&id={$item->id}:{$item->alias}&Itemid={$item->itemId}");
				
		if($isActiveItem) { ?>
		<div class="item active">
		<?php 
			$isActiveItem = false;
		} else { ?>
		<div class="item">
		<?php } ?>
			<a href="<?php echo $link ;?>">
			<?php 
				$collString = "file=".'/'.urlencode($item->thumb)."&amp;tn=g";
			?>
				<img class="d-block w-100" src="<?php echo JURI::root(true); ?>/components/com_xgallery/helpers/img.php?file=<?php echo urlencode($item->thumb); ?>&amp;tn=0" alt="<?php echo htmlspecialchars($item->title); ?>" />
			</a>
			<?php if($params->get('show_collection_name') || $params->get('show_date') || $params->get('show_hits') || $params->get('show_category') || $params->get('show_quicktake') || $params->get('show_submitter')) { ?>
			<div class="carousel-caption">
				<?php if($params->get('show_collection_name')) { ?>
				<h4><?php echo $item->title; ?></h4>
				<?php } ?>
				<?php if($params->get('show_date') || $params->get('show_hits') || $params->get('show_category') || $params->get('show_submitter')) { ?>
				<ul class="inline">
					<?php if($params->get('show_category')) { ?>
					<li><strong><?php echo JTEXT::_('MOD_XGALLERY_JSCROLLER_CATEGORY'); ?>:</strong> <?php echo $item->cattitle; ?></li>	
					<?php } ?>
					<?php if($params->get('show_submitter')) { ?>
					<li><strong><?php echo JTEXT::_('MOD_XGALLERY_JSCROLLER_SUBMITTER'); ?>:</strong> <?php echo $item->submitter; ?></li>	
					<?php } ?>
					<?php if($params->get('show_hits')) { ?>
					<li><strong><?php echo JTEXT::_('MOD_XGALLERY_JSCROLLER_HITS'); ?>:</strong> <?php echo $item->hits; ?></li>	
					<?php } ?>
					<?php if($params->get('show_date')) { ?>
					<li><strong><?php echo JTEXT::_('MOD_XGALLERY_JSCROLLER_DATE_ADDED'); ?>:</strong> <?php echo JHTML::Date($item->creation_date, 'm-d-Y'); ?></li>	
					<?php } ?>
					<?php if($params->get('show_tags')) {?>
					<li class="tag-list">
					<?php
					$item->tags = new JHelperTags;
					$item->tags->getItemTags('com_xgallery.collection' , $item->id);
					$item->tagLayout = new JLayoutFile('joomla.content.tags');
					?>
					<strong><?php echo JTEXT::_('MOD_XGALLERY_JSCROLLER_TAGS'); ?>:</strong> <?php echo $item->tagLayout->render($item->tags->itemTags); ?>
					</li>
					<?php } ?>
				</ul>	
				<?php } ?>
				<?php if($params->get('show_quicktake')) { ?>
				<p><?php echo $item->introtext; ?></p>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	<?php } ?>
	</div>
  	<!-- Carousel nav -->
	<a class="carousel-control left" href="#<?php echo $scroller_id; ?>" data-slide="prev">&lsaquo;</a>
    <a class="carousel-control right" href="#<?php echo $scroller_id; ?>" data-slide="next">&rsaquo;</a>
</div>
<?php } ?>
