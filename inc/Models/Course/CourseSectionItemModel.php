<?php
/**
 * Class CourseSectionItemModel
 *
 * @since 4.2.4
 * @version 1.0.0
 */

namespace LearnPress\Models\Course;

class CourseSectionItemModel {
	/**
	 * @var int primary key,
	 */
	public $section_item_id;
	/**
	 * @var int section_id foreign key,
	 */
	public $section_id;
	/**
	 * @var int item_id foreign key,
	 */
	public $item_id;
	/**
	 * @var int for order section,
	 */
	public $item_order;
	/**
	 * @var string type of item.
	 */
	public $item_type;
}
