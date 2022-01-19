const $ = jQuery;

function formatCourse( repo ) {
	if ( repo.loading ) {
		return repo.text;
	}
	const markup =
		"<div class='select2-result-course_title'>" +
		repo.id +
		' - ' +
		repo.title.rendered +
		'</div>';
	return markup;
}

function formatCourseSelection( repo ) {
	return repo.title.rendered || repo.text;
}

function autocompleteWidget( widget = null ) {
	const searchs = $( '.lp-widget_select_course' );
	const wpRestUrl = searchs.data( 'rest-url' );
	const postType = searchs.data( 'post_type' ) || 'lp_course';

	searchs.select2( {
		ajax: {
			method: 'GET',
			url: wpRestUrl + 'wp/v2/' + postType,
			dataType: 'json',
			delay: 250,
			data( params ) {
				return {
					search: params.term,
				};
			},
			processResults( data, params ) {
				params.page = params.page || 1;

				return {
					results: data,
				};
			},
			cache: true,
		},
		escapeMarkup( markup ) {
			return markup;
		},
		minimumInputLength: 2,
		templateResult: formatCourse, // omitted for brevity, see the source of this page
		templateSelection: formatCourseSelection, // omitted for brevity, see the source of this page
	} );
}

function loadWidgetCourseInfo() {
	const elements = $( '#widget-learnpress_widget_course_info-1-display_type' );

	if ( ! elements.length > 0 ) {
		return;
	}
	const displayType = elements.val();

	if ( displayType != 'course_id' ) {
		$( '#widget-learnpress_widget_course_info-1-course_id' ).closest( 'p' ).hide();
	}
}

function changeActionWidgetCourseInfo() {
	$( document ).on(
		'change',
		'#widget-learnpress_widget_course_info-1-display_type',
		function() {
			if ( this.value == 'course_id' ) {
				$( '#widget-learnpress_widget_course_info-1-course_id' ).closest( 'p' ).show();
			} else {
				$( '#widget-learnpress_widget_course_info-1-course_id' ).closest( 'p' ).hide();
			}
		}
	);
}

document.addEventListener( 'DOMContentLoaded', function( event ) {
	if ( document.querySelector( '#widgets-editor' ) ) {
		$( document ).on( 'widget-added', function( event, widget ) {
			autocompleteWidget( widget );
			loadWidgetCourseInfo();
		} );
	} else {
		$( document ).on( 'learnpress/widgets/select', function() {
			autocompleteWidget();
		} );

		autocompleteWidget();
	}
	changeActionWidgetCourseInfo();
} );
