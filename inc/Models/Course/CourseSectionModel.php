<?php
/**
 * Class CourseSectionModel
 *
 * @since 4.2.4
 * @version 1.0.0
 */

namespace LearnPress\Models\Course;

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
}
