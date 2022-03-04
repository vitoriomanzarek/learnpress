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

		addReviewStar();

	};

	skeletonTab();

	const addReviewStar = () => {
		const reviewStar = document.querySelector('ul.review-stars');
		if( ! reviewStar ) {
			return;
		}
		const stars = reviewStar.querySelectorAll('li.review-title');
		let clicked = false;

		const addClassReview = (stars, title) => {
			[...stars].map( (starHover) => {
				const titleHover = starHover.getAttribute('title');

				if ( titleHover <= title ) {
					starHover.querySelector('span').classList.add('hover');
				} else {
					starHover.querySelector('span').classList.remove('hover');
				}
			});
		}

		stars && stars.forEach( (star,index) => {
			const title = star.getAttribute('title');
			star.addEventListener('mouseover', () => {
				if( ! clicked ) {
					addClassReview( stars, title )
				};
			});

			star.addEventListener('click', () => {
				console.log('object');
				clicked = true;
				addClassReview( stars, title );
				document.querySelector( 'ul.review-fields li.review-actions input[name="rating"]').value = title;
			});
		});

		reviewStar.addEventListener('mouseout', () => {
			stars.forEach( (starHover) => {
				!clicked && starHover.querySelector('span').classList.remove('hover');
			});
		})
	}

	const submitReview = async ( btnReviewForm ) => {
		const parenNode = document.querySelector('.learnpress-course-review ul.review-fields');
		if ( ! parenNode ) {
			return;
		}

		const reviewTitle = parenNode.querySelector('input[name="review_title"]').value || '';
		const reviewContent = parenNode.querySelector('textarea[name="review_content"]').value || '';
		const rating = parenNode.querySelector( 'input[name="rating"]' ).value;
		const courseID = btnReviewForm.dataset.id;
		const emptyTitle = parenNode.querySelector( 'input[name="empty_title"]' ).value;
		const emptyContent = parenNode.querySelector( 'input[name="empty_content"]' ).value;
		const emptyRating = parenNode.querySelector( 'input[name="empty_rating"]' ).value;

		if ( 0 == reviewTitle.length ) {
			alert(emptyTitle);
			return;
		}

		if ( 0 == reviewContent.length ) {
			alert(emptyContent);
			return;
		}

		if ( 0 == rating ) {
			alert(emptyRating);
			return;
		}

		try {
			const response = await wp.apiFetch( {
				path: 'learnpress/v1/review/submit',
				method: 'POST',
				data: { id:courseID, rate:rating, title:reviewTitle, content:reviewContent },
			} );

			const { status, message } = response;

			if ( status == 'success' ) {
				LP.reload();
			} else {
				parenNode.innerHTML += `<li class="lp-ajax-message error" style="display:block">${ message }</li>`;
			}
		} catch ( error ) {
			parenNode.innerHTML += `<li class="lp-ajax-message error" style="display:block">${ error }</li>`;
		}
	};

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

		if ( btnLoadReview &&  btnLoadReview.contains( e.target)  ) {
			btnLoadReview.classList.add( 'loading' );
			const paged = btnLoadReview && btnLoadReview.dataset.paged;
			const courseID = btnLoadReview && btnLoadReview.dataset.id;
			const element = document.querySelector( '.course-reviews-list' );
			showMoreReview( element, courseID, paged, btnLoadReview );
		}

		const btnReviewForm = document.querySelector('li.review-actions .submit-review');

		if ( btnReviewForm &&  btnReviewForm.contains( e.target)  ) {
			submitReview( btnReviewForm );
		}

	} );
}
