/* eslint-disable @wordpress/no-unused-vars-before-return */
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';

export default function courseReviewSkeleton() {
	//loader template tab
	const skeletonTab = () => {
		const elementCourseReview = document.querySelector( '.learnpress-course-review' );

		if ( ! elementCourseReview ) {
			return;
		}

		getResponse( elementCourseReview );
	};
	const getResponse = async ( ele ) => {
		const skeleton = ele.querySelector( '.lp-skeleton-animation' );

		try {
			const response = await apiFetch( {
				path: addQueryArgs( 'lp/v1/lazy-load/course-review', {
					courseId: lpGlobalSettings.post_id || '',
				} ),
				method: 'GET',
			} );

			const { data, status, message } = response;

			if ( status === 'error' ) {
				throw new Error( message || 'Error' );
			}

			data && ele.insertAdjacentHTML( 'beforeend', data );
		} catch ( error ) {
			ele.insertAdjacentHTML( 'beforeend', `<div class="lp-ajax-message error" style="display:block">${ error.message || 'Error: Query lp/v1/lazy-load/course-review' }</div>` );
		}

		skeleton && skeleton.remove();
	};

	skeletonTab();

	// create review and submit
	// let submitting = false;
	// const reviewForm = $( '#course-review' );
	// const btnReview = $( 'button.write-a-review' );
	// const stars = $( '.review-fields ul > li span' ).each( function( i ) {
	// 	$( this ).hover( function() {
	// 		if ( submitting ) {
	// 			return;
	// 		}
	// 		stars.map( function( j ) {
	// 			$( this ).toggleClass( 'hover', j <= i );
	// 		} );
	// 	}, function() {
	// 		if ( submitting ) {
	// 			return;
	// 		}
	// 		const selected = reviewForm.find( 'input[name="rating"]' ).val();
	// 		if ( selected ) {
	// 			stars.map( function( j ) {
	// 				$( this ).toggleClass( 'hover', j < selected );
	// 			} );
	// 		} else {
	// 			stars.removeClass( 'hover' );
	// 		}
	// 	} ).on( 'click', function( e ) {
	// 		if ( submitting ) {
	// 			return;
	// 		}
	// 		e.preventDefault();
	// 		reviewForm.find( 'input[name="rating"]' ).val( stars.index( $( this ) ) + 1 );
	// 	} );
	// } );

	// const emptyForm = function() {
	// 	$( 'button, input[type="text"], textarea', reviewForm ).prop( 'disabled', false );
	// 	reviewForm.removeClass( 'submitting' ).data( 'selected', '' );
	// 	stars.removeClass( 'hover' );
	// };

	// const closeForm = () => {
	// 	reviewForm.find( 'input[name="rating"]' ).val( '' );
	// 	reviewForm.fadeOut( emptyForm );
	// };

	// const submitReview = async ( id, rate, title, content ) => {
	// 	try {
	// 		const response = await wp.apiFetch( {
	// 			path: 'learnpress/v1/review/submit',
	// 			method: 'POST',
	// 			data: { id, rate, title, content },
	// 		} );

	// 		const { status, message } = response;

	// 		if ( status == 'success' ) {
	// 			submitting = false;
	// 			closeForm();
	// 			LP.reload();
	// 		} else {
	// 			reviewForm.find( 'ul.review-fields' ).append( `<li class="lp-ajax-message error" style="display:block" >${ message }</li>` );
	// 		}
	// 	} catch ( error ) {
	// 		reviewForm.find( 'ul.review-fields' ).append( `<li class="lp-ajax-message error" style="display:block" >${ error }</li>` );
	// 	}
	// };

	// btnReview.on( 'click', function( e ) {
	// 	e.preventDefault();
	// 	reviewForm.fadeIn();
	// } );

	// reviewForm.on( 'click', '.review-actions .close', function( e ) {
	// 	e.preventDefault();
	// 	closeForm();
	// } );

	// reviewForm.on( 'click', '.review-actions .submit-review', function( e ) {
	// 	e.preventDefault();
	// 	const reviewTitle = reviewForm.find( 'input[name="review_title"]' ).val();
	// 	const reviewContent = reviewForm.find( 'textarea[name="review_content"]' ).val();
	// 	const rating = reviewForm.find( 'input[name="rating"]' ).val();
	// 	const courseID = $( this ).attr( 'data-id' );
	// 	const emptyTitle = reviewForm.find( 'input[name="empty_title"]' ).val();
	// 	const emptyContent = reviewForm.find( 'input[name="empty_content"]' ).val();
	// 	const emptyRating = reviewForm.find( 'input[name="empty_rating"]' ).val();

	// 	if ( 0 == reviewTitle.length ) {
	// 		alert( emptyTitle );
	// 		return;
	// 	}

	// 	if ( 0 == reviewContent.length ) {
	// 		alert( emptyContent );
	// 		return;
	// 	}

	// 	if ( 0 == rating.length ) {
	// 		alert( emptyRating );
	// 		return;
	// 	}
	// 	submitReview( courseID, rating, reviewTitle, reviewContent );
	// } );

	// load more review course

	const showMoreReview = async ( ele, id, page, btnLoadReview = false ) => {
		try {
			const response = await wp.apiFetch( {
				path: addQueryArgs( 'learnpress/v1/review/course/' + id, { page } ),
				method: 'GET',
			} );

			if ( response.status === 'success' && response.data ) {
				ele.innerHTML += response.data.template;
			} else {
				ele.innerHTML += `<li class="lp-ajax-message error" style="display:block">${ response.message && response.message }</li>`;
			}

			if ( btnLoadReview ) {
				btnLoadReview.classList.remove( 'loading' );

				const paged = btnLoadReview.dataset.paged;
				const numberPage = btnLoadReview.dataset.number;

				if ( numberPage <= paged ) {
					btnLoadReview.remove();
				}

				btnLoadReview.dataset.paged = parseInt( paged ) + 1;
			}
		} catch ( error ) {
			ele.innerHTML += `<li class="lp-ajax-message error" style="display:block">${ error }</li>`;
		}
	};

	document.addEventListener( 'click', function( e ) {
		const btnLoadReview = document.querySelector( '.course-review-load-more' );
		btnLoadReview.classList.add( 'loading' );
		const paged = btnLoadReview && btnLoadReview.dataset.paged;
		const courseID = btnLoadReview && btnLoadReview.dataset.id;
		const element = document.querySelector( '.course-reviews-list' );

		showMoreReview( element, courseID, paged, btnLoadReview );
	} );
}
