<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Models\User; 

class UserTest extends TestCase
{
    public function test_fillable_attributes()
    {
        $user = new User();
        $expected = ['name', 'phone', 'role', 'email', 'password'];

        $this->assertSame($expected, $user->getFillable());
    }

    public function test_hidden_attributes()
    {
        $user = new User();
        $expected = ['password', 'remember_token'];

        $this->assertSame($expected, $user->getHidden());
    }

    public function test_casts_attributes() 
    {
        $user = new User();
        $expected = ['id' => 'int','email_verified_at' => 'datetime', 'password' => 'hashed'];

        $this->assertSame($expected, $user->getCasts());
    }
}
