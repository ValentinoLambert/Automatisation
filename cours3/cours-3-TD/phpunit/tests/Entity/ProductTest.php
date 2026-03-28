<?php

namespace Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testConstructor(): void
    {
        $prices = ['EUR' => 10.5, 'USD' => 12.0];
        $product = new Product('Laptop', $prices, 'tech');
        
        $this->assertEquals('Laptop', $product->getName());
        $this->assertEquals($prices, $product->getPrices());
        $this->assertEquals('tech', $product->getType());
    }

    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid type');
        
        new Product('Item', ['EUR' => 10], 'invalid');
    }

    public function testSetName(): void
    {
        $product = new Product('Phone', ['USD' => 500], 'tech');
        $product->setName('Smartphone');
        
        $this->assertEquals('Smartphone', $product->getName());
    }

    public function testSetTypeValid(): void
    {
        $product = new Product('Bread', ['EUR' => 2], 'food');
        $product->setType('other');
        
        $this->assertEquals('other', $product->getType());
    }

    public function testSetTypeInvalid(): void
    {
        $product = new Product('Item', ['EUR' => 10], 'food');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid type');
        
        $product->setType('invalid');
    }

    public function testSetPricesValid(): void
    {
        $product = new Product('Item', ['EUR' => 10], 'tech');
        $product->setPrices(['USD' => 20, 'EUR' => 15]);
        
        $expected = ['USD' => 20, 'EUR' => 15];
        $this->assertEquals($expected, $product->getPrices());
    }

    public function testSetPricesFiltersInvalidCurrency(): void
    {
        $product = new Product('Item', ['EUR' => 10], 'tech');
        $product->setPrices(['GBP' => 20, 'USD' => 15]);
        
        $this->assertEquals(['EUR' => 10, 'USD' => 15], $product->getPrices());
    }

    public function testSetPricesFiltersNegativePrices(): void
    {
        $product = new Product('Item', ['EUR' => 10], 'tech');
        $product->setPrices(['EUR' => -5, 'USD' => 20]);
        
        $this->assertEquals(['EUR' => 10, 'USD' => 20], $product->getPrices());
    }

    public function testGetTVAForFood(): void
    {
        $product = new Product('Bread', ['EUR' => 2], 'food');
        
        $this->assertEquals(0.1, $product->getTVA());
    }

    public function testGetTVAForNonFood(): void
    {
        $product = new Product('Laptop', ['EUR' => 1000], 'tech');
        
        $this->assertEquals(0.2, $product->getTVA());
    }

    public function testListCurrencies(): void
    {
        $product = new Product('Item', ['EUR' => 10, 'USD' => 12], 'tech');
        
        $currencies = $product->listCurrencies();
        $this->assertCount(2, $currencies);
        $this->assertContains('EUR', $currencies);
        $this->assertContains('USD', $currencies);
    }

    public function testGetPriceValid(): void
    {
        $product = new Product('Item', ['EUR' => 10, 'USD' => 12], 'tech');
        
        $this->assertEquals(10, $product->getPrice('EUR'));
        $this->assertEquals(12, $product->getPrice('USD'));
    }

    public function testGetPriceInvalidCurrency(): void
    {
        $product = new Product('Item', ['EUR' => 10], 'tech');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency');
        
        $product->getPrice('GBP');
    }

    public function testGetPriceCurrencyNotAvailableForProduct(): void
    {
        $product = new Product('Item', ['EUR' => 10], 'tech');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Currency not available for this product');
        
        $product->getPrice('USD');
    }
}
