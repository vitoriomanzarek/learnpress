const { addQueryArgs } = wp.url;

const searchUsersOrder = () => {
	const postID = document.getElementById("post_ID").value;

	if (postID === null) {
		return;
	}

	let filterUsers = {
		paged: 1,
		term: "",
		type: "lp_order",
		multiple: "no",
		exclude: [],
		context: "order-items",
		contextID: postID,
	};
	let listUsers = [];
	let timeOutSearch;

	const eleOrder = document.getElementById("learn-press-order");

	if (eleOrder === null) {
		return;
	}

	const eleModal = document.getElementById("modal-search-users");

	if (eleModal === null) {
		return;
	}

	const eleResult = eleModal.querySelector(".search-results");
	const eleNavResult = eleModal.querySelector(".search-nav");
	const inputSearch = eleModal.querySelector("input[name='search']");
	const iconSearch = eleModal.querySelector(".icon-loading");
	const orderDataUser = eleOrder.querySelector(".order-data-user");

	if (
		eleResult === null ||
		eleNavResult === null ||
		inputSearch === null ||
		iconSearch === null ||
		orderDataUser === null
	) {
		return;
	}

	//show modal
	document.addEventListener("click", (e) => {
		if (e.target.classList.contains("change-user")) {
			e.preventDefault();
			const isMulti = e.target.dataset.multiple === "yes";
			if (isMulti) {
				filterUsers.multiple = "yes";
			} else {
				filterUsers.multiple = "no";
			}

			//clear search
			inputSearch.value = "";
			eleResult.innerHTML = "";
			eleNavResult.innerHTML = "";

			//show modal
			eleModal.classList.add("active");

			//exclude user
			excludeUser(filterUsers);

			//event handlers
			closeModal();
			searchUsers(filterUsers);
		}
	});

	//exclude user
	const excludeUser = (filter) => {
		const exclude = [];
		const eleUsers = eleOrder.querySelector("#list-users");
		if (eleUsers === null) {
			const eleUser = eleOrder.querySelector(
				'input[name="order-customer"]'
			);
			if (eleUser !== null) {
				exclude.push(eleUser.value);
			}
		} else {
			const listUser = eleUsers.querySelectorAll("li.lp-user");
			if (listUser.length === 0) {
				return;
			}
			listUser.forEach((user) => {
				const id = user.dataset.id;
				if (id !== null) {
					exclude.push(id);
				}
			});
		}

		filter.exclude = exclude;
	};

	//close modal
	const closeModal = () => {
		const btnClose = eleModal.querySelector(".close-modal");
		if (btnClose === null) {
			return;
		}
		btnClose.addEventListener("click", (e) => {
			e.preventDefault();
			clearTimeout(timeOutSearch);
			eleModal.classList.remove("active");
		});
	};

	//search user
	const searchUsers = (filter) => {
		inputSearch.addEventListener("keyup", (event) => {
			event.preventDefault();

			const s = event.target.value.trim();
			// if (s && s.length > 2) {
			if (!s || (s && s.length > 2)) {
				iconSearch.classList.add("loading");

				if (undefined !== timeOutSearch) {
					clearTimeout(timeOutSearch);
				}

				timeOutSearch = setTimeout(function () {
					filter.term = s;
					filter.paged = 1;

					requestUsers({ ...filter });
				}, 800);
			}
		});
	};

	//request users
	const requestUsers = async (filter) => {
		try {
			const response = await wp.apiFetch({
				path: addQueryArgs("lp/v1/admin/order/search-users", filter),
				method: "GET",
			});
			const { html, nav, status, message } = response;

			iconSearch.classList.remove("loading");

			if (status === "success") {
				eleResult.innerHTML = html;
				eleNavResult.innerHTML = nav;

				//event handler
				paginationUsers(filter);
				addUserOrder(filter);
				addMultiUserOrder(filter);
			} else {
				eleResult.innerHTML = message;
				eleNavResult.innerHTML = "";
			}
		} catch (e) {
			console.log(e);
		}
	};

	//add user

	const addUserOrder = (filter) => {
		const listUser = eleResult.querySelectorAll(".lp-result-item");

		if (listUser.length === 0) {
			return;
		}

		if (filter.multiple === "yes") {
			return;
		}

		listUser.forEach((user) => {
			user.addEventListener("click", (e) => {
				e.preventDefault();
				const userData = JSON.parse(user.dataset.data);

				if (userData === null) {
					return;
				}

				const display =
					userData.display_name + "(" + userData.email + ")";

				const html = `
					<div class="order-data-field order-data-user">
						<label>Customer</label>
						<div class="order-users">
							${display}
							<input type="hidden" name="order-customer" id="order-customer" value="${userData.id}">
						</div>
						<a href="" class="change-user">Change</a>
					</div>
				`;
				orderDataUser.innerHTML = html;
				eleModal.classList.remove("active");
			});
		});
	};

	//pagination
	const paginationUsers = (filter) => {
		const listPage = eleNavResult.querySelectorAll("a.page-numbers");

		if (listPage.length === 0) {
			return;
		}

		const current = eleNavResult.querySelector("span.page-numbers.current");

		listPage.forEach((page) => {
			page.addEventListener("click", (e) => {
				e.preventDefault();
				iconSearch.classList.add("loading");
				let paged = page.innerText;

				if (e.target.classList.contains("prev")) {
					paged = Number(current.innerText) - 1;
				}

				if (e.target.classList.contains("next")) {
					paged = Number(current.innerText) + 1;
				}

				if (paged === null) {
					return;
				}

				filter.paged = paged;
				requestUsers({ ...filter });
			});
		});
	};

	//add multi user
	const addMultiUserOrder = (filter) => {
		if (filter.multiple === "no") {
			return;
		}
		const btnAdd = eleModal.querySelector(".add-user");

		if (btnAdd === null) {
			return;
		}

		const listUser = eleResult.querySelectorAll(".lp-result-item");

		if (listUser.length === 0) {
			return;
		}

		listUser.forEach((user) => {
			user.addEventListener("click", (e) => {
				e.preventDefault();
				const userData = JSON.parse(user.dataset.data);

				if (userData === null) {
					return;
				}

				const index = listUsers.findIndex(
					(item) => item.id === userData.id
				);
				const input = user.querySelector("input[type=checkbox]");

				if (index === -1) {
					listUsers.push(userData);
					input.checked = true;
				} else {
					listUsers.splice(index, 1);
					input.checked = false;
				}
			});
		});
	};

	//event click add multi users
	document.addEventListener("click", (e) => {
		if (e.target.classList.contains("add-user")) {
			e.preventDefault();

			if (listUsers.length === 0) {
				alert("Please select users");
				return;
			}
			const elemWrap = `
				<div class="order-data-field order-data-user">
					<label>Customer</label>
					<div class="order-users">
						<ul id="list-users" class="advanced-list"></ul>
					</div>
					<a href="" class="change-user" data-multiple="yes">Add multiple users</a>
				</div>
			`;

			orderDataUser.innerHTML = elemWrap;

			const html = listUsers.map((user) => {
				return `
					<li data-id="${user.id}">
					<span class="remove-item"></span><span>${user.display_name} (${user.email})</span>
					<input type="hidden" name="order-customer[]" value="${user.id}">
					</li>
				`;
			});

			const eleUsers = document.getElementById("list-users");

			if (eleUsers === null) {
				return;
			}

			eleUsers.innerHTML = html.join("");
			eleModal.classList.remove("active");
		}
	});

	//event remove item multi users
	document.addEventListener("click", (e) => {
		if (e.target.classList.contains("remove-item")) {
			const eleUsers = document.getElementById("list-users");
			if (eleUsers === null) {
				return;
			}

			const eleRemove = e.target;
			const parent = eleRemove.parentElement;
			if (parent === null) {
				return;
			}

			parent.remove();

			//check if remove all users
			const listUsers = eleUsers.querySelectorAll("li.lp-user");
			if (listUsers.length === 0) {
				const html = `
					<li class="user-guest">Guest</li>
					<input type="hidden" name="order-customer" id="order-customer" value="0">
				`;
				eleUsers.innerHTML = html;
			}
		}
	});
};

document.addEventListener("DOMContentLoaded", function (e) {
	searchUsersOrder();
});
