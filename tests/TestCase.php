<?php

namespace Tests;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected function sanctumLogin(Authenticatable $customer, array $abilities = ['*']): Authenticatable
    {
        Sanctum::actingAs($customer, $abilities);

        return $customer;
    }
}
