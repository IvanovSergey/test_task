<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testCalculatePrice()
    {
        $client = static::createClient();

        $client->request('POST', '/calculate-price', [
            'product' => 0,
            'taxNumber' => 'GR123456789',
            'couponCode' => 'P6'
        ]);
        $this->assertResponseIsSuccessful();        
    }
    
    public function testPurchase()
    {
        $client = static::createClient();

        $client->request('POST', '/purchase', [
            'product' => 0,
            'taxNumber' => 'GR123456789',
            'couponCode' => 'P6', 
            'paymentProcessor' => 'stripe'
        ]);
        $this->assertResponseIsSuccessful();        
    }
}