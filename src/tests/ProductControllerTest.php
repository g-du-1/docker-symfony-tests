<?php

namespace App\Tests;

use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{

    public function test_product_page_loads(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Products');
    }

    public function test_new_product_can_be_added(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

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

    public function test_product_can_be_deleted(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/product/');
        $addNewLink = $crawler->filter('a[href="/product/new"]')->link();

        $client->click($addNewLink);
        $crawler = $client->submitForm('Save', [
            'product[name]' => 'Test product to be deleted',
            'product[price]' => '11.11',
            'product[description]' => 'Test product created by PHPUnit, to be deleted',
        ]);

        $newProductLink = $crawler->filter('.product')->last()->selectLink('show')->link();
        $crawler = $client->click($newProductLink);

        $client->followRedirects(false);

        $client->submit($crawler->filter('.btn-danger')->form());

        $this->assertResponseRedirects('/product/', Response::HTTP_FOUND);

    }
}
