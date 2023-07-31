import API from './api';
import { lpAddQueryArgs, lpFetchAPI, lpGetCurrentURLNoParam } from '../utils/utils';

if ( undefined === lpGlobalSettings ) {
	console.log( 'lpGlobalSettings is undefined' );
}

const currentUrl = lpGetCurrentURLNoParam();
const urlQueryString = window.location.search;
const urlSearchParams = new URLSearchParams( urlQueryString );
let filterCourses = {};
let skeleton;
//let skeletonClone;
let isLoading = false;
let firstLoad = 1;
let elNoLoadAjaxFirst;
let elArchive;
let elListCourse;
let dataHtml;
let paginationHtml;

const urlParams = {};
for ( const [ key, val ] of urlSearchParams.entries() ) {
	urlParams[ key ] = val;
}

window.localStorage.setItem( 'lp_filter_courses', JSON.stringify( urlParams ) );

if ( ! lpGlobalSettings.lpArchiveLoadAjax ) {
	console.log( 'Option load courses ajax is disabled' );
}

// Add events when load done.
const lpArchiveCourse = () => {
	// Case load ajax when reload enable.
	if ( ! lpGlobalSettings.lpArchiveNoLoadAjaxFirst ) {
		if ( skeleton ) {
			skeleton.insertAdjacentHTML( 'beforebegin', dataHtml );
			skeleton.style.display = 'none';
		}

		const pagination = paginationHtml;
		const paginationEle = document.querySelector( '.learn-press-pagination' );
		if ( paginationEle ) {
			paginationEle.remove();
		}

		if ( typeof pagination !== 'undefined' ) {
			const paginationHTML = new DOMParser().parseFromString( pagination, 'text/html' );
			const paginationNewNode = paginationHTML.querySelector( '.learn-press-pagination' );

			if ( paginationNewNode ) {
				elListCourse.after( paginationNewNode );
			}
		}
	}

	lpArchivePaginationCourse();
	lpArchiveSearchCourse();
};

// Call API load courses.
window.lpArchiveRequestCourse = ( args, callBackSuccess ) => {
	if ( isLoading ) {
		return;
	}
	isLoading = true;

	// Change url by params filter courses
	const urlPush = lpAddQueryArgs( currentUrl, args );
	window.history.pushState( '', '', urlPush );

	// Append skeleton to list.
	/*if ( skeletonClone ) {
		elListCourse.append( skeletonClone );
	}*/
	if ( skeleton ) {
		skeleton.style.display = 'block';
	}

	filterCourses = args;
	// Save filter courses to Storage
	window.localStorage.setItem( 'lp_filter_courses', JSON.stringify( args ) );
	const urlCourseArchive = lpAddQueryArgs( API.apiCourses, args );
	const url = API.apiCourses + urlCourseArchive.search;
	let paramsFetch = {
		method: 'GET',
	};

	if ( 0 !== lpGlobalSettings.user_id ) {
		paramsFetch = {
			...paramsFetch,
			headers: {
				'X-WP-Nonce': lpGlobalSettings.nonce,
			},
		};
	}

	fetch( url, paramsFetch )
		.then( ( response ) => response.json() )
		.then( ( response ) => {
			dataHtml = response.data.content || '';
			paginationHtml = response.data.pagination || '';

			/*if ( ! skeletonClone && skeleton ) {
				skeletonClone = skeleton.cloneNode( true );
			}*/

			if ( ! firstLoad ) {
				const elLis = elListCourse.querySelectorAll( ':not(.lp-archive-course-skeleton)' );
				elLis.forEach( ( elLi ) => {
					const parent = elLi.closest( '.lp-archive-course-skeleton' );
					if ( parent ) {
						return;
					}
					elLi.remove();
				} );

				if ( skeleton ) {
					skeleton.insertAdjacentHTML( 'beforebegin', dataHtml );
					skeleton.style.display = 'none';
				}

				//elListCourse.innerHTML = dataHtml;

				const pagination = paginationHtml;
				const paginationEle = document.querySelector( '.learn-press-pagination' );
				if ( paginationEle ) {
					paginationEle.remove();
				}

				if ( typeof pagination !== 'undefined' ) {
					const paginationHTML = new DOMParser().parseFromString( pagination, 'text/html' );
					const paginationNewNode = paginationHTML.querySelector( '.learn-press-pagination' );

					if ( paginationNewNode ) {
						elListCourse.after( paginationNewNode );
						lpArchivePaginationCourse();
					}
				}
			}

			wp.hooks.doAction( 'lp-js-get-courses', response );

			if ( typeof callBackSuccess === 'function' ) {
				callBackSuccess( response );
			}
		} ).catch( ( error ) => {
			elListCourse.innerHTML += `<div class="lp-ajax-message error" style="display:block">${ error.message || 'Error: Query lp/v1/courses/archive-course' }</div>`;
		} )
		.finally( () => {
			isLoading = false;
			const btnSearchCourses = document.querySelector( 'form.search-courses button' );
			if ( btnSearchCourses ) {
				btnSearchCourses.classList.remove( 'loading' );
			}

			if ( ! firstLoad ) {
				// Scroll to archive element
				const optionScroll = { behavior: 'smooth' };
				elArchive.scrollIntoView( optionScroll );
			} else {
				firstLoad = 0;
			}
		} );
};

// Call API load courses when js loaded.
if ( ! lpGlobalSettings.lpArchiveNoLoadAjaxFirst ) {
	//lpArchiveRequestCourse( { ...lpGlobalSettings.lpArchiveSkeleton, ...urlParams } );
} else {
	firstLoad = 0;
}

const lpArchiveSearchCourse = () => {
	const searchForm = document.querySelectorAll( 'form.search-courses' );
	//filterCourses = JSON.parse( window.localStorage.getItem( 'lp_filter_courses' ) ) || {};

	searchForm.forEach( ( s ) => {
		const search = s.querySelector( 'input[name="c_search"]' );
		const btn = s.querySelector( '[type="submit"]' );
		let timeOutSearch;

		search.addEventListener( 'keyup', ( event ) => {
			/*if ( skeleton ) {
				skeleton.style.display = 'block';
			}*/
			event.preventDefault();

			const s = event.target.value.trim();

			if ( ! s || ( s && s.length > 2 ) ) {
				if ( undefined !== timeOutSearch ) {
					clearTimeout( timeOutSearch );
				}

				timeOutSearch = setTimeout( function() {
					btn.classList.add( 'loading' );

					filterCourses.c_search = s;
					filterCourses.paged = 1;

					lpArchiveRequestCourse( filterCourses );
				}, 800 );
			}
		} );

		s.addEventListener( 'submit', ( e ) => {
			e.preventDefault();

			const eleSearch = s.querySelector( 'input[name="c_search"]' );
			eleSearch && eleSearch.dispatchEvent( new Event( 'keyup' ) );
		} );
	} );
};

const lpArchivePaginationCourse = () => {
	const paginationEle = document.querySelectorAll( '.lp-archive-courses .learn-press-pagination .page-numbers' );

	paginationEle.length > 0 && paginationEle.forEach( ( ele ) => ele.addEventListener( 'click', ( event ) => {
		event.preventDefault();
		event.stopPropagation();

		if ( ! elArchive ) {
			return;
		}

		/*if ( skeleton ) {
			skeleton.style.display = 'block';
		}*/

		// Scroll to archive element
		elArchive.scrollIntoView( { behavior: 'smooth' } );

		//filterCourses = JSON.parse( window.localStorage.getItem( 'lp_filter_courses' ) ) || {};

		const urlString = event.currentTarget.getAttribute( 'href' );

		if ( urlString ) {
			const current = [ ...paginationEle ].filter( ( el ) => el.classList.contains( 'current' ) );
			const paged = event.currentTarget.textContent || ( ele.classList.contains( 'next' ) && parseInt( current[ 0 ].textContent ) + 1 ) || ( ele.classList.contains( 'prev' ) && parseInt( current[ 0 ].textContent ) - 1 );
			filterCourses.paged = paged;

			lpArchiveRequestCourse( filterCourses );
		}
	} ) );
};

const lpArchiveGridListCourse = () => {
	const layout = LP.Cookies.get( 'courses-layout' );

	const switches = document.querySelectorAll( '.lp-courses-bar .switch-layout [name="lp-switch-layout-btn"]' );

	switches.length > 0 && [ ...switches ].map( ( ele ) => ele.value === layout && ( ele.checked = true ) );
};

const lpArchiveGridListCourseHandle = () => {
	const gridList = document.querySelectorAll( '.lp-archive-courses input[name="lp-switch-layout-btn"]' );

	gridList.length > 0 && gridList.forEach( ( element ) => element.addEventListener( 'change', ( e ) => {
		e.preventDefault();

		const layout = e.target.value;

		if ( layout ) {
			const dataLayout = document.querySelector( '.lp-archive-courses .learn-press-courses[data-layout]' );

			dataLayout && ( dataLayout.dataset.layout = layout );
			LP.Cookies.set( 'courses-layout', layout );
		}
	} ) );
};

const LPArchiveCourseInit = () => {
	lpArchiveCourse();
	lpArchiveGridListCourseHandle();
	lpArchiveGridListCourse();
};

// document.addEventListener( 'DOMContentLoaded', function( event ) {
// 	LPArchiveCourseInit();
// } );

/*const detectedElArchive = setInterval( function() {
	skeleton = document.querySelector( '.lp-archive-course-skeleton' );
	elArchive = document.querySelector( '.lp-archive-courses' );
	if ( elArchive ) {
		elListCourse = elArchive.querySelector( '.learn-press-courses' );
	}
	let canLoad = false;

	if ( elListCourse && skeleton ) {
		if ( lpGlobalSettings.lpArchiveNoLoadAjaxFirst ) {
			canLoad = true;
		} else if ( dataHtml ) {
			canLoad = true;
		}

		if ( canLoad ) {
			LPArchiveCourseInit();
			clearInterval( detectedElArchive );
		}
	}
}, 1 );*/

// Events on change sort by.
document.addEventListener( 'change', function( e ) {
	const target = e.target;

	window.lpCourseList.onChangeSortBy( e, target );
} );

document.addEventListener( 'click', function( e ) {
	const target = e.target;

	window.lpCourseList.clickLoadMore( e, target );
	window.lpCourseList.clickNumberPage( e, target );
} );

document.addEventListener( 'scroll', function( e ) {
	const target = e.target;

	window.lpCourseList.scrollInfinite( e, target );
} );

let isLoadingInfinite = false;
const isPaged = 1;

window.lpCourseList = ( () => {
	const classArchiveCourse = 'lp-archive-courses';
	const classPaginationCourse = 'learn-press-pagination';
	const currentUrl = lpGetCurrentURLNoParam();
	let filterCourses = {};
	const typePagination = lpGlobalSettings.lpArchivePaginationType || 'number';
	let typeEventBeforeFetch;
	const fetchAPI = ( args, callBack = {} ) => {
		console.log( 'Fetch API Courses' );
		const url = lpAddQueryArgs( API.apiCourses, args );
		let paramsFetch = {};

		if ( 0 !== lpGlobalSettings.user_id ) {
			paramsFetch = {
				headers: {
					'X-WP-Nonce': lpGlobalSettings.nonce,
				},
			};
		}

		lpFetchAPI( url, paramsFetch, callBack );
	};
	return {
		init: () => {
			const urlParams = {};
			for ( const [ key, val ] of urlSearchParams.entries() ) {
				urlParams[ key ] = val;
			}

			filterCourses = { ...lpGlobalSettings.lpArchiveSkeleton, ...urlParams };
			filterCourses.paged = parseInt( filterCourses.paged || 1 );
			window.localStorage.setItem( 'lp_filter_courses', JSON.stringify( filterCourses ) );
		},
		updateEventTypeBeforeFetch: ( type ) => {
			typeEventBeforeFetch = type;
		},
		onChangeSortBy: ( e, target ) => {
			if ( ! target.classList.contains( 'course-order-by' ) ) {
				return;
			}

			e.preventDefault();
			let filterCoursesParams = window.localStorage.getItem( 'lp_filter_courses' );
			filterCoursesParams = JSON.parse( filterCoursesParams ) || {};
			filterCoursesParams.order_by = target.value;
			window.location.href = lpAddQueryArgs( currentUrl, filterCoursesParams );
		},
		clickNumberPage: ( e, target ) => {
			const parent = target.closest( '.page-numbers' );
			if ( target.classList.contains( 'page-numbers' ) ) {
				e.preventDefault();

				const pageCurrent = parseInt( filterCourses.paged || 1 );

				if ( parent.classList.contains( 'prev' ) ) {
					filterCourses.paged = pageCurrent - 1;
				} else if ( parent.classList.contains( 'next' ) ) {
					filterCourses.paged = pageCurrent + 1;
				} else {
					filterCourses.paged = parseInt( target.textContent );
				}

				typeEventBeforeFetch = 'number';
				window.lpCourseList.triggerFetchAPI( filterCourses );
			} else if ( parent ) {
				e.preventDefault();
				parent.click();
			}
		},
		clickLoadMore: ( e, target ) => {
			if ( ! target.classList.contains( 'courses-btn-load-more' ) ) {
				return;
			}

			e.preventDefault();
			++filterCourses.paged;
			typeEventBeforeFetch = 'load-more';
			window.lpCourseList.triggerFetchAPI( filterCourses );
		},
		scrollInfinite: ( e, target ) => {
			const elArchive = document.querySelector( '.lp-archive-courses' );
			if ( ! elArchive ) {
				return;
			}

			const elInfinite = elArchive.querySelector( '.courses-load-infinite' );
			if ( ! elInfinite ) {
				return;
			}

			// Create an IntersectionObserver object.
			const observer = new IntersectionObserver( function( entries ) {
				for ( const entry of entries ) {
					// If the entry is intersecting, load the image.
					if ( entry.isIntersecting ) {
						if ( isLoadingInfinite ) {
							return;
						}

						++filterCourses.paged;
						typeEventBeforeFetch = 'infinite';
						window.lpCourseList.triggerFetchAPI( filterCourses );

						//observer.unobserve( entry.target );
					}
				}
			} );

			observer.observe( elInfinite );
		},
		triggerFetchAPI: ( args ) => { // For case, click on pagination, filter.
			const elArchive = document.querySelector( '.lp-archive-courses' );
			if ( ! elArchive ) {
				return;
			}
			elListCourse = elArchive.querySelector( '.learn-press-courses' );
			if ( ! elListCourse ) {
				return;
			}

			filterCourses = args;

			let callBack;
			switch ( typeEventBeforeFetch ) {
			case 'load-more':
				callBack = window.lpCourseList.callBackPaginationTypeLoadMore( args, elArchive, elListCourse );
				break;
			case 'infinite':
				callBack = window.lpCourseList.callBackPaginationTypeInfinite( elArchive, elListCourse );
				break;
			case 'filter':
				callBack = window.lpCourseList.callBackFilter( args, elArchive, elListCourse );
				break;
			case 'custom':
				callBack = args.customCallBack || false;
				break;
			default: // number
				// Change url by params filter courses
				const urlPush = lpAddQueryArgs( currentUrl, args );
				window.history.pushState( '', '', urlPush );

				// Save filter courses to Storage
				window.localStorage.setItem( 'lp_filter_courses', JSON.stringify( args ) );

				callBack = window.lpCourseList.callBackPaginationTypeNumber( elListCourse );
				break;
			}

			if ( ! callBack ) {
				return;
			}

			console.log( 'Args', args );

			fetchAPI( args, callBack );
		},
		callBackFilter: ( args, elArchive, elListCourse ) => {
			if ( ! elListCourse ) {
				return;
			}

			const skeleton = elListCourse.querySelector( '.lp-archive-course-skeleton' );
			if ( ! skeleton ) {
				return;
			}

			return {
				before: () => {
					args.paged = 1;
					window.history.pushState( '', '', lpAddQueryArgs( currentUrl, args ) );
					window.localStorage.setItem( 'lp_filter_courses', JSON.stringify( args ) );
					if ( skeleton ) {
						skeleton.style.display = 'block';
					}
				},
				success: ( res ) => {
					// Remove all items before insert new items.
					const elLis = elListCourse.querySelectorAll( ':not(.lp-archive-course-skeleton)' );
					elLis.forEach( ( elLi ) => {
						const parent = elLi.closest( '.lp-archive-course-skeleton' );
						if ( parent ) {
							return;
						}
						elLi.remove();
					} );

					// Insert new items.
					elListCourse.insertAdjacentHTML( 'afterbegin', res.data.content || '' );

					// Check if Pagination exists will remove.
					const elPagination = document.querySelector( `.${ classPaginationCourse }` );
					if ( elPagination ) {
						elPagination.remove();
					}

					// Insert Pagination.
					const pagination = res.data.pagination || '';
					elListCourse.insertAdjacentHTML( 'afterend', pagination );
				},
				error: ( error ) => {
					elListCourse.innerHTML += `<div class="lp-ajax-message error" style="display:block">${ error.message || 'Error' }</div>`;
				},
				completed: () => {
					if ( skeleton ) {
						skeleton.style.display = 'none';
					}
					// Scroll to archive element
					const optionScroll = { behavior: 'smooth' };
					elListCourse.closest( '.lp-archive-courses' ).scrollIntoView( optionScroll );
				},
			};
		},
		callBackPaginationTypeNumber: ( elListCourse ) => {
			if ( ! elListCourse ) {
				return;
			}
			const skeleton = elListCourse.querySelector( '.lp-archive-course-skeleton' );

			return {
				before: () => {
					skeleton.style.display = 'block';
				},
				success: ( res ) => {
					// Remove all items before insert new items.
					const elLis = elListCourse.querySelectorAll( ':not(.lp-archive-course-skeleton)' );
					elLis.forEach( ( elLi ) => {
						const parent = elLi.closest( '.lp-archive-course-skeleton' );
						if ( parent ) {
							return;
						}
						elLi.remove();
					} );

					// Insert new items.
					skeleton.insertAdjacentHTML( 'beforebegin', res.data.content || '' );

					// Delete Pagination if exists.
					skeleton.style.display = 'block';
					const paginationEle = document.querySelector( `.${ classPaginationCourse }` );
					if ( paginationEle ) {
						paginationEle.remove();
					}
					// Insert Pagination.
					const pagination = res.data.pagination || '';
					elListCourse.insertAdjacentHTML( 'afterend', pagination );
				},
				error: ( error ) => {
					elListCourse.innerHTML += `<div class="lp-ajax-message error" style="display:block">${ error.message || 'Error' }</div>`;
				},
				completed: () => {
					skeleton.style.display = 'none';
					// Scroll to archive element
					const optionScroll = { behavior: 'smooth' };
					elListCourse.closest( '.lp-archive-courses' ).scrollIntoView( optionScroll );
				},
			};
		},
		callBackPaginationTypeLoadMore: ( args, elArchive, elListCourse ) => {
			console.log( 'callBackPaginationTypeLoadMore' );
			if ( ! elListCourse ) {
				return false;
			}
			const btnLoadMore = elArchive.querySelector( '.courses-btn-load-more' );
			let elLoading;
			if ( btnLoadMore ) {
				elLoading = btnLoadMore.querySelector( '.lp-loading-circle' );
			}
			const skeleton = document.querySelector( '.lp-archive-course-skeleton' );

			if ( args.eventFilter === 1 ) {
				window.history.pushState( '', '', lpAddQueryArgs( currentUrl, args ) );
			}

			if ( args.paged === 1 ) {
				if ( btnLoadMore ) {
					btnLoadMore.remove();
				}

				if ( skeleton ) {
					skeleton.style.display = 'block';
				}
			}

			return {
				before: () => {
					if ( btnLoadMore ) {
						elLoading.classList.remove( 'hide' );
					}
				},
				success: ( res ) => {
					skeleton.style.display = 'none';
					if ( args.paged === 1 ) {
						elListCourse.innerHTML = skeleton.outerHTML;
					}

					elListCourse.insertAdjacentHTML( 'beforeend', res.data.content || '' );
					elListCourse.insertAdjacentHTML( 'afterend', res.data.pagination || '' );
				},
				error: ( error ) => {
					elListCourse.innerHTML += `<div class="lp-ajax-message error" style="display:block">${ error.message || 'Error' }</div>`;
				},
				completed: () => {
					if ( btnLoadMore ) {
						elLoading.classList.add( 'hide' );
						btnLoadMore.remove();
					}

					if ( args.eventFilter === 1 ) {
						// Scroll to archive element
						const optionScroll = { behavior: 'smooth' };
						elListCourse.closest( '.lp-archive-courses' ).scrollIntoView( optionScroll );
					}
				},
			};
		},
		callBackPaginationTypeInfinite: ( elArchive, elListCourse ) => {
			console.log( 'callBackPaginationTypeInfinite' );
			if ( ! elListCourse ) {
				return;
			}

			const elInfinite = elArchive.querySelector( '.courses-load-infinite' );
			if ( ! elInfinite ) {
				return;
			}
			const loading = elInfinite.querySelector( '.lp-loading-circle' );

			isLoadingInfinite = true;

			elInfinite.classList.remove( 'courses-load-infinite' );

			return {
				before: () => {
					loading.classList.remove( 'hide' );
				},
				success: ( res ) => {
					elListCourse.insertAdjacentHTML( 'beforeend', res.data.content || '' );

					if ( res.data.pagination ) {
						elListCourse.insertAdjacentHTML( 'afterend', res.data.pagination || '' );
					}
				},
				error: ( error ) => {
					elListCourse.innerHTML += `<div class="lp-ajax-message error" style="display:block">${ error.message || 'Error' }</div>`;
				},
				completed: () => {
					elInfinite.remove();
					isLoadingInfinite = false;
				},
			};
		},
		ajaxEnableLoadPage: () => { // For case enable AJAX when load page.
			const countTime = 0;

			if ( ! lpGlobalSettings.lpArchiveNoLoadAjaxFirst ) {
				let detectedElArchivex;
				const callBack = {
					success: ( res ) => {
						detectedElArchivex = setInterval( function() {
							const skeleton = document.querySelector( '.lp-archive-course-skeleton' );
							const elArchive = document.querySelector( `.${ classArchiveCourse }` );
							if ( elArchive ) {
								elListCourse = elArchive.querySelector( '.learn-press-courses' );
							}

							if ( countTime > 5000 ) {
								clearInterval( detectedElArchivex );
							}

							if ( elListCourse && skeleton ) {
								clearInterval( detectedElArchivex );
								elListCourse.insertAdjacentHTML( 'afterbegin', res.data.content || '' );
								skeleton.style.display = 'none';

								const pagination = res.data.pagination || '';
								elListCourse.insertAdjacentHTML( 'afterend', pagination );
							}
						}, 1 );
					},
				};

				if ( 'number' !== typePagination ) {
					filterCourses.paged = 1;
				}
				fetchAPI( filterCourses, callBack );
			}
		},
	};
} )();

window.lpCourseList.init();
window.lpCourseList.ajaxEnableLoadPage();
