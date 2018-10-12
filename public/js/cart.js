function substractPrice(index, fixedPrice) {
    let qty = parseInt($('.qty_input')[index].value);
    let price = parseInt($('.price')[index].textContent);
    let totalQuantity = parseInt($('#totalQuantity').text());
    let totalPrice = parseInt($('#totalPrice').text());
    qty--;
    if (qty == 0) {
        qty = 1;
        price = fixedPrice;
    } else {
        totalPrice -= fixedPrice;
        price = price - fixedPrice;
        totalQuantity--;
    }
    $('.qty_input')[index].value = qty;
    $('.price')[index].textContent = price + '$';
    $('#totalQuantity').replaceWith('<span id="totalQuantity">' + totalQuantity + '</span>');
    $('#totalPrice').replaceWith('<span id="totalPrice">' + totalPrice + '$' + '</span>');
}

function addPrice(index, fixedPrice) {
    let qty = parseInt($('.qty_input')[index].value);
    let price = parseInt($('.price')[index].textContent);
    let totalQuantity = parseInt($('#totalQuantity').text());
    let totalPrice = parseInt($('#totalPrice').text());
    qty++;
    totalPrice += fixedPrice;
    price = qty * fixedPrice;
    totalQuantity++;
    $('.qty_input')[index].value = qty;
    $('.price')[index].textContent = price + '$';
    $('#totalQuantity').replaceWith('<span id="totalQuantity">' + totalQuantity + '</span>');
    $('#totalPrice').replaceWith('<span id="totalPrice">' + totalPrice + '$' + '</span>');
}

function addToCart() {
    let url = window.location.href;
    let urlArr = url.split('/');
    let productId = urlArr[4];

    $.ajax({
        url: '/cart/product/' + productId,
        method: 'POST',
        dataType: 'json',
        success: function(product) {
            product = JSON.parse(product);
            let redirectButton = parseInt(product.counter) === 1 ? '<div><a href="/cart" class="btn btn-primary mt-3">View Shopping Cart</a></div>' : '';

            productDiv = '<div>' +
                            '<img class="mr-2 img-thumbnail" width="50" height="50" src="' + product.image + '" /><' +
                            'span>' + product.name + ', ' + product.price + '$' + '</span>' +
                        '</div><div class="dropdown-divider"></div>' + redirectButton;

            $('#product-dropdown').prepend(productDiv);
            $('#product-counter').replaceWith('<div class="product-notification"><span id="product-counter">' + (product.counter) + '</span></div>')
            alert('Product has benn added to shopping card');
        }
    });
}