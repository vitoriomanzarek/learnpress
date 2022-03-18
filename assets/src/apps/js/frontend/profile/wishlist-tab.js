import { cog } from '@wordpress/icons/build-types';
import { addQueryArgs } from '@wordpress/url';

// Rest API load content course wishlist in page profile - Minhpd.
const courseWishList = () => {

	const elements = document.querySelector( '.profile-wishlist' );

	if ( ! elements ) {
		return;
	}

	const getResponse = ( ele, dataset , append = false, viewMoreEle = false ) => {
		wp.apiFetch( {
			path: addQueryArgs( 'learnpress/v1/wishlist', dataset ),
			method: 'GET',
		} ).then( ( response ) => {

			const skeleton = ele.querySelector( '.lp-skeleton-animation' );
			if( skeleton ) {
				skeleton.style.display = 'none';
			}

			if ( response.status === 'success' && response.data ) {
				if ( append ) {
					ele.innerHTML += response.data.template;
				} else {
					ele.innerHTML = response.data.template;
				}
			} else if ( append ) {
				ele.innerHTML += `<div class="learn-press-message error" style="display:block">${ response.message && response.message }</div>`;
			}else {
				ele.innerHTML = `<div class="learn-press-message error" style="display:block">${ response.message && response.message }</div>`;
			}

			if ( viewMoreEle ) {
				viewMoreEle.classList.remove( 'loading' );

				const paged = viewMoreEle.dataset.pagedWishlist;
				const numberPage = viewMoreEle.dataset.number;

				if ( numberPage <= paged ) {
					viewMoreEle.remove();
				}
				localStorage.setItem('pageCurrent', parseInt(viewMoreEle.dataset.pagedWishlist) + 1 );
				viewMoreEle.dataset.pagedWishlist = localStorage.getItem('pageCurrent');

			} else {
				viewMoreWishlist( ele, dataset );
			}
			//remove course
			removeWishlist();

		} ).catch( ( err ) => {
			if ( append ) {
				ele.innerHTML += `<div class="learn-press-message error" style="display:block">${ err }</div>`;
			}else {
				ele.innerHTML = `<div class="learn-press-message error" style="display:block">${ err }</div>`;
			}
		} );
	};

	const elArgWishlist = document.querySelector( '[name="args_query_user_courses_wishlist"]' );

	if ( ! elArgWishlist ) {
		return;
	}

	const dataParams = JSON.parse( elArgWishlist.value );

	localStorage.setItem('pageCurrent', 1 );

	getResponse( elements, dataParams , true, false );

	const viewMoreWishlist = (ele,dataset) => {
		const viewMoreEle = document.querySelector( 'button.view-more-wishlist' );

		if ( viewMoreEle ) {
			viewMoreEle.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				const paged = viewMoreEle && viewMoreEle.dataset.pagedWishlist;

				viewMoreEle.classList.add( 'loading' );
				getResponse( ele.querySelector( '.learn-press-courses' ), { ...dataset, ...{ paged } }, true , viewMoreEle );
			} );
		}
	};

	const removeWishlist = () =>{
		const btnRemoveWishlist = document.querySelectorAll( '.course-remove-wishlist' );

		if ( btnRemoveWishlist.length > 0 ) {
			[...btnRemoveWishlist].map( ( ele )=>{

				const submit = async ( id , append = false ) => {
					try {
						const response = await wp.apiFetch( {
							path: 'learnpress/v1/wishlist/toggle',
							method: 'POST',
							data: { id },
						} );

						const type = response.data.type;

						if ( response.status === 'success' && type == 'remove' ){
							//append new course when remove old course
							if ( append ) {
								alert(response.message);
								dataParams.paged = 1;
								const pageCurrent = localStorage.getItem('pageCurrent');
								getResponse(elements,{ ...dataParams , ...{ pageCurrent } }, true, false );
							}
						}else{
							alert(response.message);
							window.location.reload();
						}

					} catch ( error ) {
						console.log(error);
					}
				};

				ele.addEventListener( 'click', ( event ) => {
					event.preventDefault();
					const id = ele.dataset.id;
					const skeleton = elements.querySelector('ul.lp-skeleton-animation');
					skeleton.style.display = 'block';
					const listCourse = elements.querySelector('.lp-archive-courses');
					const viewMoreEle = elements.querySelector( '.lp_profile_course_progress__nav' );

					elements.removeChild(listCourse);
					if ( viewMoreEle ) {
						elements.removeChild(viewMoreEle);
					}
					submit( id , true );
				} );
			});
		}
	}
};
export default courseWishList;
