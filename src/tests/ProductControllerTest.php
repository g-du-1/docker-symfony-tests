<?php

namespace App\Tests;

use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{

    public function test_product_page_loads(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Products');
    }

    public function test_adding_new_product(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/product/');
        $addNewLink = $crawler->filter('a[href="/product/new"]')->link();

        $client->click($addNewLink);
        $crawler = $client->submitForm('Save', [
            'product[name]' => 'Test product 1',
            'product[price]' => '12.34',
            'product[description]' => 'Test product 1 created by PHPUnit',
        ]);

        $newProduct = $crawler->filter('.product')->last()->filter('.product-name')->text();

        $this->assertSame('Test product 1', $newProduct);
    }

//    public function test_deleting_product(): void
//    {
//        $client = static::createClient();
//        $client->followRedirects();
//
//
//    }
}
