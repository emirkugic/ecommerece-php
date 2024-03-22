document.addEventListener("DOMContentLoaded", function () {
	fetchCartContents();
});

function fetchCartContents() {
	fetch("../../data/cart.json")
		.then((response) => response.json())
		.then((cart) => renderCart(cart.items))
		.catch((error) => console.error("Error fetching cart:", error));
}

function renderCart(items) {
	const tbody = document.querySelector(".site-blocks-table table tbody");
	tbody.innerHTML = "";

	items.forEach((item) => {
		const row = `
      <tr>
        <td class="product-thumbnail">
          <img src="${item.image}" alt="Image" class="img-fluid" />
        </td>
        <td class="product-name">
          <h2 class="h5 text-black">${item.name}</h2>
        </td>
        <td>$${item.price.toFixed(2)}</td>
        <td>
          <div class="input-group mb-3 d-flex align-items-center quantity-container" style="max-width: 120px">
            <div class="input-group-prepend">
              <button class="btn btn-outline-black decrease" type="button" data-id="${
								item.id
							}">&minus;</button>
            </div>
            <input type="text" class="form-control text-center quantity-amount" value="${
							item.quantity
						}" aria-label="Example text with button addon" aria-describedby="button-addon1" />
            <div class="input-group-append">
              <button class="btn btn-outline-black increase" type="button" data-id="${
								item.id
							}">&plus;</button>
            </div>
          </div>
        </td>
        <td>$${(item.price * item.quantity).toFixed(2)}</td>
        <td><a href="#" class="btn btn-black btn-sm" data-id="${
					item.id
				}">X</a></td>
      </tr>
    `;
		tbody.insertAdjacentHTML("beforeend", row);
	});
	updateCartTotal(items);
}

const totalElement = document.querySelector(
	".col-md-6.text-right strong.text-black"
);

function updateCartTotal(items) {
	let total = 0;
	items.forEach((item) => {
		total += item.price * item.quantity;
	});

	const totalElement = document.querySelector(
		".col-md-6.text-right strong.text-black"
	);
	if (totalElement) {
		totalElement.textContent = `$${total.toFixed(2)}`;
	} else {
		console.error("The total price element was not found in the DOM.");
	}

	console.log("Calculated total:", total);
	console.log("Total Element:", totalElement);
}