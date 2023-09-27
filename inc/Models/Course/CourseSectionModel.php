<?php
/**
 * Class CourseSectionModel
 *
 * @since 4.2.4
 * @version 1.0.0
 */

namespace LearnPress\Models\Course;

use stdClass;

class CourseSectionModel {
	/**
	 * @var int primary key
	 */
	public $section_id;
	/**
	 * @var int course_id foreign key
	 */
	public $section_course_id;
	/**
	 * @var string
	 */
	public $section_name;
	/**
	 * @var string
	 */
	public $section_description;
	/**
	 * @var int for order section
	 */
	public $section_order;
	/**
	 * @var CourseSectionItemModel[]
	 */
	public $items;

	/**
	 * Mapper stdclass to model
	 *
	 * @param stdClass $object
	 * @return CourseSectionModel
	 */
	public function map_stdclass( stdClass $object ): self {
		$course_section = new self();

		foreach ( $object as $key => $value ) {
			if ( property_exists( $course_section, $key ) ) {
				$course_section->{$key} = $value;

				if ( 'items' === $key ) {
					/**
					 * @var stdClass $item
					 */
					foreach ( $course_section->items as $k_item => $item ) {
						$course_section->items->{$k_item} = ( new CourseSectionItemModel() )->map_stdclass( $item );
					}
				}

				//Todo: For old data of LP version lower 4.2.4, when run along time, will remove this code
				switch ( $key ) {
					case 'id':
						$course_section->section_id = $value;
						break;
					case 'order':
						$course_section->section_order = $value;
						break;
					case 'title':
						$course_section->section_name = $value;
						break;
					case 'description':
						$course_section->section_description = $value;
						break;
				}
			}
		}

		return $course_section;
	}
}
