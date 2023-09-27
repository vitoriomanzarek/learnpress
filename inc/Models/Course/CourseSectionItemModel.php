<?php
/**
 * Class CourseSectionItemModel
 *
 * @since 4.2.4
 * @version 1.0.0
 */

namespace LearnPress\Models\Course;

use stdClass;

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

	/**
	 * Mapper stdclass to model
	 *
	 * @param stdClass $object
	 * @return CourseSectionItemModel
	 */
	public function map_stdclass( stdClass $object ): self {
		$course_section_item = new self();

		foreach ( $object as $key => $value ) {
			if ( property_exists( $course_section_item, $key ) ) {
				$course_section_item->{$key} = $value;
			}

			//Todo: For old data of LP version lower 4.2.4, when run along time, will remove this code
			switch ( $key ) {
				case 'id':
					$course_section_item->item_id = $value;
					break;
				case 'order':
					$course_section_item->item_order = $value;
					break;
				case 'type':
					$course_section_item->item_type = $value;
					break;
			}
		}

		return $course_section_item;
	}
}
