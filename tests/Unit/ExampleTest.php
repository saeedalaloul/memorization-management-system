<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_basic_test()
    {
       $response = $this->get("/login");
       $response->assertStatus(200);
    }

    public function testUserIsRedirectedWithNoLogin()
    {
        $response = $this->get("/dashboard");
        $response->assertRedirect(route('login'));
    }
}
