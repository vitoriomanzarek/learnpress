const modalCoursesOrder = () => {
	const postID = document.getElementById( 'post_ID' ).value;

	if ( postID === null ) {
		return;
	}

	const filterCourses = {
		paged: 1,
		term: '',
		type: 'lp_course',
		exclude: [],
		context: 'order-items',
		contextID: postID,
	};
	let listCourses = [];
	let timeOutSearch;

	const eleOrder = document.getElementById( 'learn-press-order' );

	if ( eleOrder === null ) {
		return;
	}

	const eleModal = document.getElementById( 'modal-search-items' );

	if ( eleModal === null ) {
		return;
	}

	const eleResult = eleModal.querySelector( '.search-results' );
	const eleNavResult = eleModal.querySelector( '.search-nav' );
	const inputSearch = eleModal.querySelector( "input[name='search']" );
	const iconSearch = eleModal.querySelector( '.icon-loading' );
	const btnAdd = eleModal.querySelector( '.add-items' );

	if (
		eleResult === null ||
		eleNavResult === null ||
		inputSearch === null ||
		iconSearch === null
	) {
		return;
	}

	const btnAddItems = document.getElementById( 'learn-press-add-order-item' );

	if ( btnAddItems === null ) {
		return;
	}

	//show modal
	btnAddItems.addEventListener( 'click', ( e ) => {
		e.preventDefault();

		//clear search
		inputSearch.value = '';
		eleResult.innerHTML = '';
		eleNavResult.innerHTML = '';

		//show modal
		eleModal.classList.add( 'active' );

		//exclude courses
		excludeCourses( { ...filterCourses } );

		//event handlers
		iconSearch.classList.add( 'loading' );
		requestCourses( { ...filterCourses } );
		closeModal();
		searchCourses( { ...filterCourses } );
	} );

	const excludeCourses = ( filter ) => {
		const items = eleOrder.querySelectorAll( 'tr.order-item-row' );
		if ( items.length > 0 ) {
			items.forEach( ( item ) => {
				const id = item.dataset.id;
				if ( id ) {
					filter.exclude.push( id );
				}
			} );
		}
	};

	//search user
	const searchCourses = ( filter ) => {
		inputSearch.addEventListener( 'keyup', ( event ) => {
			event.preventDefault();

			const s = event.target.value.trim();
			// if (s && s.length > 2) {
			if ( ! s || ( s && s.length > 2 ) ) {
				iconSearch.classList.add( 'loading' );

				if ( undefined !== timeOutSearch ) {
					clearTimeout( timeOutSearch );
				}

				timeOutSearch = setTimeout( function() {
					filter.term = s;
					filter.paged = 1;

					requestCourses( { ...filter } );
				}, 800 );
			}
		} );
	};

	//close modal
	const closeModal = () => {
		const btnClose = eleModal.querySelector( '.close-modal' );
		if ( btnClose === null ) {
			return;
		}
		btnClose.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			clearTimeout( timeOutSearch );
			listCourses = [];
			eleModal.classList.remove( 'active' );
		} );
	};

	//request search courses
	const requestCourses = async ( filter ) => {
		try {
			const response = await wp.apiFetch( {
				path: addQueryArgs( 'lp/v1/admin/order/search-courses', filter ),
				method: 'GET',
			} );
			const { html, nav, status, message } = response;

			iconSearch.classList.remove( 'loading' );

			if ( status === 'success' ) {
				eleResult.innerHTML = html;
				eleNavResult.innerHTML = nav;

				//event handler
				paginationCourses( filter );
				selectCourses();
			} else {
				eleResult.innerHTML = message;
				eleNavResult.innerHTML = '';
			}
		} catch ( e ) {
			console.log( e );
		}
	};

	const paginationCourses = ( filter ) => {
		const listPage = eleNavResult.querySelectorAll( 'a.page-numbers' );

		if ( listPage.length === 0 ) {
			return;
		}

		const current = eleNavResult.querySelector( 'span.page-numbers.current' );

		listPage.forEach( ( page ) => {
			page.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				iconSearch.classList.add( 'loading' );
				let paged = page.innerText;

				if ( e.target.classList.contains( 'prev' ) ) {
					paged = Number( current.innerText ) - 1;
				}

				if ( e.target.classList.contains( 'next' ) ) {
					paged = Number( current.innerText ) + 1;
				}

				if ( paged === null ) {
					return;
				}

				filter.paged = paged;
				requestCourses( { ...filter } );
			} );
		} );
	};

	const selectCourses = ( filter ) => {
		const listCourse = eleResult.querySelectorAll( '.lp-result-item' );

		if ( listCourse.length === 0 ) {
			return;
		}

		listCourse.forEach( ( user ) => {
			user.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				const userID = user.dataset.id;

				if ( userID === null ) {
					return;
				}

				const index = listCourses.findIndex( ( id ) => id === userID );
				const input = user.querySelector( 'input[type=checkbox]' );

				if ( index === -1 ) {
					listCourses.push( userID );
					input.checked = true;
				} else {
					listCourses.splice( index, 1 );
					input.checked = false;
				}
			} );
		} );
	};

	//add courses
	const addCourses = async ( btnAdd ) => {
		try {
			const response = await wp.apiFetch( {
				path: 'lp/v1/admin/order/add-courses',
				data: {
					items: listCourses,
					orderID: postID,
				},
				method: 'POST',
			} );

			btnAdd.classList.remove( 'loading' );
			const { status, item_html, order_data, message } = response;

			if ( status === 'success' ) {
				const tableOrderItems =
					eleOrder.querySelector( '.list-order-items' );

				if ( tableOrderItems !== null ) {
					const eleNoOrder =
						tableOrderItems.querySelector( '.no-order-items' );

					if ( eleNoOrder !== null ) {
						eleNoOrder.remove();
					}

					tableOrderItems.querySelector( 'tbody' ).insertAdjacentHTML( 'beforeend', item_html );
					const totalEle = tableOrderItems.querySelector( 'span.order-total' );
					const subtotalEle = tableOrderItems.querySelector( 'span.order-subtotal' );

					subtotalEle.innerHTML = order_data.subtotal_html;
					totalEle.innerHTML = order_data.total_html;

					eleModal.classList.remove( 'active' );
					listCourses = [];
				}
			} else {
				alert( message );
			}
		} catch ( e ) {
			console.log( e );
		}
	};

	//event handle add courses
	btnAdd.addEventListener( 'click', ( e ) => {
		e.preventDefault();
		btnAdd.classList.add( 'loading' );
		if ( listCourses.length === 0 ) {
			alert( 'Please select items to add to order' );
			return;
		}
		addCourses( btnAdd );
	} );

	//remove course
	const removeCourse = async ( id, parent ) => {
		try {
			const response = await wp.apiFetch( {
				path: 'lp/v1/admin/order/remove-courses',
				data: {
					itemID: [ id ],
					orderID: postID,
				},
				method: 'POST',
			} );

			const { message, status, order_data } = response;

			if ( status === 'success' ) {
				const tableOrderItems =
					eleOrder.querySelector( '.list-order-items' );

				if ( tableOrderItems !== null ) {
					const eleNoOrder =
						tableOrderItems.querySelector( '.no-order-items' );

					if ( eleNoOrder !== null ) {
						eleNoOrder.remove();
					}

					parent.remove();
					const totalEle = tableOrderItems.querySelector( 'span.order-total' );
					const subtotalEle = tableOrderItems.querySelector( 'span.order-subtotal' );

					subtotalEle.innerHTML = order_data.subtotal_html;
					totalEle.innerHTML = order_data.total_html;
					if ( tableOrderItems.querySelectorAll( 'tbody tr' ).length === 0 ) {
						const htmlNoitems = `<tr class="no-order-items" style="">
											     \<td colspan="4">There are no order items</td>
											</tr>`;
						tableOrderItems.querySelector( 'tbody' ).innerHTML = htmlNoitems;
					}
				}
			} else {
				alert( message );
			}
		} catch ( e ) {
			console.log( e );
		}
	};

	//event handle remove course
	document.addEventListener( 'click', function( e ) {
		if ( e.target.parentNode.classList.contains( 'remove-order-item' ) ) {
			e.preventDefault();
			const parent = e.target.closest( 'tr.order-item-row' );
			if ( parent !== null ) {
				const id = parent.dataset.item_id;
				removeCourse( id, parent );
			} else {
				alert( 'Can not remove this item' );
			}
		}
	} );
};

document.addEventListener( 'DOMContentLoaded', function( e ) {
	modalCoursesOrder();
} );
