<?php

namespace Tests\Entity;

use App\Entity\Person;
use App\Entity\Wallet;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testConstructor(): void
    {
        $person = new Person('John', 'EUR');
        
        $this->assertEquals('John', $person->getName());
        $this->assertInstanceOf(Wallet::class, $person->getWallet());
        $this->assertEquals('EUR', $person->getWallet()->getCurrency());
    }

    public function testSetName(): void
    {
        $person = new Person('Alice', 'USD');
        $person->setName('Bob');
        
        $this->assertEquals('Bob', $person->getName());
    }

    public function testSetWallet(): void
    {
        $person = new Person('John', 'EUR');
        $newWallet = new Wallet('USD');
        
        $person->setWallet($newWallet);
        
        $this->assertEquals('USD', $person->getWallet()->getCurrency());
    }

    public function testHasFundReturnsFalseWhenBalanceIsZero(): void
    {
        $person = new Person('John', 'EUR');
        
        $this->assertFalse($person->hasFund());
    }

    public function testHasFundReturnsTrueWhenBalanceIsPositive(): void
    {
        $person = new Person('John', 'EUR');
        $person->getWallet()->addFund(100);
        
        $this->assertTrue($person->hasFund());
    }

    public function testTransfertFundValid(): void
    {
        $person1 = new Person('Alice', 'EUR');
        $person1->getWallet()->addFund(100);
        
        $person2 = new Person('Bob', 'EUR');
        
        $person1->transfertFund(30, $person2);
        
        $this->assertEquals(70, $person1->getWallet()->getBalance());
        $this->assertEquals(30, $person2->getWallet()->getBalance());
    }

    public function testTransfertFundWithDifferentCurrencies(): void
    {
        $person1 = new Person('Alice', 'EUR');
        $person1->getWallet()->addFund(100);
        
        $person2 = new Person('Bob', 'USD');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t give money with different currencies');
        
        $person1->transfertFund(30, $person2);
    }

    public function testDivideWallet(): void
    {
        $person1 = new Person('Alice', 'EUR');
        $person1->getWallet()->addFund(100);
        
        $person2 = new Person('Bob', 'EUR');
        $person3 = new Person('Charlie', 'EUR');
        
        $person1->divideWallet([$person2, $person3]);
        
        $this->assertEquals(0, $person1->getWallet()->getBalance());
        $this->assertEquals(50, $person2->getWallet()->getBalance());
        $this->assertEquals(50, $person3->getWallet()->getBalance());
    }

    public function testDivideWalletFiltersDifferentCurrencies(): void
    {
        $person1 = new Person('Alice', 'EUR');
        $person1->getWallet()->addFund(100);
        
        $person2 = new Person('Bob', 'EUR');
        $person3 = new Person('Charlie', 'USD');
        
        $person1->divideWallet([$person2, $person3]);
        
        $this->assertEquals(0, $person1->getWallet()->getBalance());
        $this->assertEquals(100, $person2->getWallet()->getBalance());
        $this->assertEquals(0, $person3->getWallet()->getBalance());
    }

    public function testBuyProductValid(): void
    {
        $person = new Person('John', 'EUR');
        $person->getWallet()->addFund(100);
        
        $product = new Product('Laptop', ['EUR' => 50], 'tech');
        
        $person->buyProduct($product);
        
        $this->assertEquals(50, $person->getWallet()->getBalance());
    }

    public function testBuyProductWithIncompatibleCurrency(): void
    {
        $person = new Person('John', 'EUR');
        $person->getWallet()->addFund(100);
        
        $product = new Product('Laptop', ['USD' => 50], 'tech');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t buy product with this wallet currency');
        
        $person->buyProduct($product);
    }
}
