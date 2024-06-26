<?php

require_once __DIR__ . '/BaseDao.class.php';

class CartDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("cart");
    }

    public function get_carts($order = "-id")
    {
        list($order_column, $order_direction) = self::parse_order($order);
        $query = "SELECT *
                  FROM cart
                  ORDER BY {$order_column} {$order_direction}";
        return $this->query($query, []);
    }

    public function get_cart_by_id($cart_id)
    {
        return $this->query_unique("SELECT * FROM cart WHERE id = :id", ["id" => $cart_id]);
    }

    public function add_cart($cart)
    {
        return $this->insert('cart', $cart);
    }

    public function update_cart($cart_id, $cart)
    {
        return $this->execute_update('cart', $cart_id, $cart);
    }

    public function delete_cart_by_id($cart_id)
    {
        return $this->delete('cart', $cart_id);
    }

    public function get_carts_by_user($user_id, $order = "-id")
    {
        list($order_column, $order_direction) = self::parse_order($order);
        $query = "SELECT *
                  FROM cart
                  WHERE user_id = :user_id
                  ORDER BY {$order_column} {$order_direction}";
        return $this->query($query, ["user_id" => $user_id]);
    }


    public function update_cart_quantity($cart_id, $quantity)
    {
        $query = "UPDATE cart SET quantity = :quantity WHERE id = :cart_id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute(['cart_id' => $cart_id, 'quantity' => $quantity]);
        return $stmt->rowCount();
    }


    // used to get cart contents by user id
    public function get_carts_with_products_by_user($user_id)
    {
        $query = "SELECT c.id, p.title, p.price, p.image_url, c.quantity
              FROM cart c
              JOIN products p ON p.id = c.product_id
              WHERE c.user_id = :user_id";
        return $this->query($query, ['user_id' => $user_id]);
    }
}
