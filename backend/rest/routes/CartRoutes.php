<?php

require_once __DIR__ . '/../services/CartService.class.php';
require_once __DIR__ . '/../../middleware.php';


Flight::group('/carts', function () {


    Flight::route('GET /test-route', function () {
        $user = Flight::get('user');

        if (!$user) {
            Flight::json(['error' => 'User data not found'], 404);
        } else {
            Flight::json(['user' => $user]);
        }
    });


    Flight::route('GET /user-cart', function () {
        $user = Flight::get('user');
        if (!$user['userId']) {
            Flight::halt(401, 'Unauthorized');
        }

        $cart_service = new CartService();
        $carts = $cart_service->get_carts_with_products_by_user($user['userId']);

        $output = ['items' => []];
        foreach ($carts as $cart) {
            $output['items'][] = [
                'id' => $cart['id'],
                'name' => $cart['title'],
                'price' => $cart['price'],
                'image' => $cart['image_url'],
                'quantity' => $cart['quantity'],
            ];
        }

        Flight::json($output);
    });

    Flight::route('PATCH /update-quantity/@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        $data = Flight::request()->data->getData();
        $new_quantity = $data['new_quantity'];

        $cart_service = new CartService();
        $cart = $cart_service->get_cart_by_id($cart_id);

        if (!$cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }

        $result = $cart_service->update_cart_quantity($cart_id, $new_quantity);
        if ($result) {
            Flight::json(['success' => true, 'message' => 'Quantity updated successfully']);
        } else {
            Flight::halt(400, 'Failed to update cart quantity');
        }
    });

    Flight::route('GET /', function () {
        authorize("ADMIN");
        $cart_service = new CartService();
        $order = Flight::request()->query['order'] ?? '-id';
        $carts = $cart_service->get_all_carts($order);
        Flight::json($carts);
    });

    Flight::route('GET /@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        $cart_service = new CartService();
        $cart = $cart_service->get_cart_by_id($cart_id);
        if (!$cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        Flight::json($cart);
    });

    Flight::route('POST /carts/add', function () {
        $data = Flight::request()->data->getData();
        $cart_service = new CartService();

        $user = Flight::get('user');

        $result = $cart_service->add_cart([
            'user_id' => $user['userId'],
            'product_id' => $data['productId'],
            'quantity' => $data['quantity']
        ]);
        if ($result) {
            Flight::json($result, 201);
        } else {
            Flight::halt(400, 'Failed to add to cart');
        }
    });



    Flight::route('PUT /@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        $cart_service = new CartService();
        $cart = Flight::request()->data->getData();
        $existing_cart = $cart_service->get_cart_by_id($cart_id);
        if (!$existing_cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $existing_cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }
        $updated_cart = $cart_service->update_cart($cart_id, $cart);
        if ($updated_cart) {
            Flight::json($updated_cart);
        } else {
            Flight::halt(400, 'Failed to update cart');
        }
    });

    Flight::route('DELETE /@cart_id', function ($cart_id) {
        $user = Flight::get('user');
        if (!$user || $user['userId'] == null) {
            Flight::halt(401, 'Unauthorized');
            return;
        }

        $cart_service = new CartService();
        $cart = $cart_service->get_cart_by_id($cart_id);
        if (!$cart) {
            Flight::halt(404, 'Cart not found');
            return;
        }

        if ($user['role'] !== 'ADMIN' && $user['userId'] != $cart['user_id']) {
            Flight::halt(403, 'Access Denied');
            return;
        }

        $success = $cart_service->delete_cart_by_id($cart_id);
        if ($success) {
            Flight::json(['success' => true, 'message' => 'Cart successfully deleted'], 200);
        } else {
            Flight::halt(400, 'Failed to delete cart');
        }
    });


    Flight::route('GET /user/by_user_id', function ($user_id) {
        $user = Flight::get('user');
        if ($user['role'] !== 'ADMIN' && $user['userId'] != $user_id) {
            Flight::halt(403, 'Access Denied');
        }
        $cart_service = new CartService();
        $carts = $cart_service->get_carts_by_user($user_id);
        Flight::json($carts);
    });
});
