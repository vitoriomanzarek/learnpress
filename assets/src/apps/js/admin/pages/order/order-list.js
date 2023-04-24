import adminAPI from '../../api';
import { addQueryArgs } from '@wordpress/url';

const orderWrap = document.querySelector( '.lp-order-list' );
let container, skeleton;

const orderList = () => {
	if ( ! orderWrap ) {
		return;
	}
	container = orderWrap.querySelector( '#the-list' );

	skeleton = container.innerHTML;
	const params = {
		paged: 1,
	};

	getOrders( params, true );
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
		if ( res.status === 'success' && res.data ) {
			container.innerHTML = res.data;
		}
	} ).catch( ( err ) => {
		console.log( err );
	} );
};

export default orderList;
