<?php

namespace Tests\Entity;

use App\Entity\Wallet;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    public function testConstructor(): void
    {
        $wallet = new Wallet('EUR');
        
        $this->assertEquals('EUR', $wallet->getCurrency());
        $this->assertEquals(0, $wallet->getBalance());
    }

    public function testConstructorWithInvalidCurrency(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency');
        
        new Wallet('INVALID');
    }

    public function testSetBalanceValid(): void
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(100.50);
        
        $this->assertEquals(100.50, $wallet->getBalance());
    }

    public function testSetBalanceNegative(): void
    {
        $wallet = new Wallet('EUR');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid balance');
        
        $wallet->setBalance(-10);
    }

    public function testSetCurrencyValid(): void
    {
        $wallet = new Wallet('USD');
        $wallet->setCurrency('EUR');
        
        $this->assertEquals('EUR', $wallet->getCurrency());
    }

    public function testSetCurrencyInvalid(): void
    {
        $wallet = new Wallet('USD');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency');
        
        $wallet->setCurrency('GBP');
    }

    public function testAddFundValid(): void
    {
        $wallet = new Wallet('EUR');
        $wallet->addFund(50);
        
        $this->assertEquals(50, $wallet->getBalance());
        
        $wallet->addFund(25.5);
        $this->assertEquals(75.5, $wallet->getBalance());
    }

    public function testAddFundNegative(): void
    {
        $wallet = new Wallet('EUR');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid amount');
        
        $wallet->addFund(-10);
    }

    public function testRemoveFundValid(): void
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(100);
        $wallet->removeFund(30);
        
        $this->assertEquals(70, $wallet->getBalance());
    }

    public function testRemoveFundNegative(): void
    {
        $wallet = new Wallet('USD');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid amount');
        
        $wallet->removeFund(-10);
    }

    public function testRemoveFundInsufficientFunds(): void
    {
        $wallet = new Wallet('EUR');
        $wallet->setBalance(50);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient funds');
        
        $wallet->removeFund(100);
    }
}
