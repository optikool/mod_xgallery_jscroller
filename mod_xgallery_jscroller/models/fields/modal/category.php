<?php
/*
 * @package Joomla 2.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component XMovie Component
 * @copyright Copyright (C) Dana Harris optikool.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */ 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldCategory extends JFormField {
	protected $type = 'Category';
	
	protected function getInput() {
		$db =& JFactory::getDBO();
		
		$query = "SELECT id, title AS name, level, published FROM #__categories WHERE published=1 AND extension = 'com_xgallery' ORDER BY lft ASC";
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++) {
			if ($options[$i]->published == 1) {
				$options[$i]->name = str_repeat('- ', $options[$i]->level). $options[$i]->name ;
			} else {
				$options[$i]->name = str_repeat('- ', $options[$i]->level). '[' .$options[$i]->name. ']';
			}
		}
			
		return JHtmlSelect::genericlist($options, $this->name, 'class="inputbox" multiple="multiple" size="10"', 'id', 'name', $this->value);
	}
}
