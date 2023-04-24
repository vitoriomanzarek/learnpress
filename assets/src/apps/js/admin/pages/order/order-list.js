import adminAPI from '../../api';
import { addQueryArgs } from '@wordpress/url';

const orderWrap = document.querySelector( '.lp-order-list' );
let container, skeleton, pagination;
let params = {
	paged: 1,
};

const orderList = () => {
	if ( ! orderWrap ) {
		return;
	}
	container = orderWrap.querySelector( '#the-list' );

	skeleton = container.innerHTML;

	pagination = orderWrap.querySelector( '.pagination-wrapper' );
	getOrders( params, true );
	handlePagination();
	handleStatus();
};

const handlePagination = () => {
	document.addEventListener( 'click', function( event ) {
		const target = event.target;
		const prevPage = target.closest( '.prev-page' );
		const nextPage = target.closest( '.next-page' );
		const firstPage = target.closest( '.first-page' );
		const lastPage = target.closest( '.last-page' );

		if ( ! prevPage && ! nextPage && ! firstPage && ! lastPage ) {
			return;
		}

		event.stopPropagation();
		const paginateLink = target.closest( '.pagination-links' );
		const currentPageInput = paginateLink.querySelector( 'input.current-page' );
		const currentPage = parseInt( currentPageInput.value );
		const totalPage = paginateLink.querySelector( '.total-pages' ).innerHTML;
		if ( prevPage ) {
			params = { ...params, paged: currentPage - 1 };
			getOrders( params, false );
		} else if ( nextPage ) {
			params = { ...params, paged: currentPage + 1 };
			getOrders( params, false );
		} else if ( firstPage ) {
			params = { ...params, paged: 1 };
			getOrders( params, false );
		} else {
			params = { ...params, paged: totalPage };
			getOrders( params, false );
		}
	} );
};

const handleStatus = () => {
	document.addEventListener( 'click', function( event ) {
		const target = event.target;

		if ( target.tagName !== 'A' && target.tagName !== 'SPAN' ) {
			return;
		}

		const orderStatusList = target.closest( '.lp-order-status-list' );

		if ( ! orderStatusList ) {
			return;
		}

		if ( target.classList.contains( 'current' ) ) {
			return;
		}

		event.preventDefault();
		const currentList = orderStatusList.querySelector( '.current' );
		if ( currentList ) {
			currentList.classList.remove( 'current' );
		}

		if ( target.tagName === 'A' ) {
			target.classList.add( 'current' );
		} else {
			target.closest( 'a' ).classList.add( 'current' );
		}

		const status = target.closest( 'li' ).className;
		if ( status !== 'all' ) {
			params = { ...params, post_status: status };
		} else {
			delete params.post_status;
		}

		params = { ...params, paged: 1 };
		getOrders( params, false );
	} );
};

const getOrders = ( params, isFirstLoad = false ) => {
	if ( isFirstLoad === false ) {
		container.innerHTML = skeleton;
	}

	fetch( addQueryArgs( adminAPI.apiAdminOrderList, params ), {
		method: 'GET',
	} ).then( ( res ) => {
		return res.json();
	} ).then( ( res ) => {
		if ( res.status === 'success' ) {
			if ( res.data ) {
				container.innerHTML = res.data;
			}

			if ( res.pagination ) {
				pagination.innerHTML = res.pagination;
			}
		}
	} ).catch( ( err ) => {
		console.log( err );
	} );
};

export default orderList;
