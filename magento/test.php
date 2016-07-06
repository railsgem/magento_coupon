<?php

require_once 'app/Mage.php';

Mage::app();

//$product = new Juno_Jcoupon_Model_Product;

//$product->sayHello();

$customer = Mage::getModel("customer/session");

//Juno_Jcoupon_Model_Product
$product = Mage::getModel("jcoupon/product");
$product->sayHello();

$helper = Mage::helper("jcoupon");
$helper->sayHello();