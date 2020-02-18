<?php
namespace Seosa\Analytics;

class GoogleAnalytics
{
    protected $cart;
    protected $product;

    public static $definition = array(
        'id' => '',
        'name' => '',
        'sku' => '',
        'category' => '',
        'price' => '',
        'quantity' => ''
    );

    public static $enhanced_definition = array(
        'name' => '',
        'brand' => '',
        'category' => '',
        'variant' => '',
        'price' => '',
        'quantity' => '',
        'coupon' => '',
        'position' => ''
    );

    public function getDefinition($data_type)
    {
        $params = self::$definition;
        if ($data_type) {
            $params = array_merge($params, self::$enhanced_definition);
        }
        return $params;
    }

    public function getData($cart, $data_type = false)
    {
        $this->cart = $cart;
        $products = $cart->getProducts();
        $this->product = $products[0];
        $rrr = is_callable(array('\Seosa\Analytics\GoogleAnalytics', 'getId'), false, $callable_name);
        $params = $this->getDefinition($data_type);

        foreach ($params as $key => &$value) {
            $value = call_user_func_array(array('\Seosa\Analytics\GoogleAnalytics', 'get'.ucfirst($key)), array());
        }
        return array($params);
    }

    /*
     * get Order id
     */
    public function getId()
    {
        return \Order::getOrderByCartId($this->cart->id);
    }

    /*
     * get product name
     */
    public function getName()
    {
        return $this->product['name'];
    }

    /*
     * get reference
     */
    public function getSku()
    {
        return $this->product['reference'];
    }

    /*
     * get category
     */
    public function getCategory()
    {
        return $this->product['category'];
    }

    /*
     * get price
     */
    public function getPrice()
    {
        return $this->product['price'];
    }

    /*
     * get quantity
     */
    public function getQuantity()
    {
        return $this->product['cart_quantity'];
    }

    /*
     * get manufacturer
     */
    public function getBrand()
    {
        return $this->product['id_manufacturer'];
    }

    /*
     * get attributes
     */
    public function getVariant()
    {
        return $this->product['attributes_small'];
    }

    /*
     * get coupon
     */
    public function getCoupon()
    {
        return '';
    }

    /*
     * get product position
     */
    public function getPosition()
    {
        return '';
    }
}