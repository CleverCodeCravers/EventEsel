<?php

use App\Models\Admin;

beforeEach(function () {
    Admin::create(['username' => 'admin', 'password' => 'secret']);
});

test('login page is accessible', function () {
    $this->get('/login')->assertStatus(200)->assertSee('Admin Login');
});

test('admin can login with valid credentials', function () {
    $this->post('/login', ['username' => 'admin', 'password' => 'secret'])
        ->assertRedirect(route('terminumfrage.create'));

    $this->assertAuthenticated();
});

test('admin cannot login with invalid credentials', function () {
    $this->post('/login', ['username' => 'admin', 'password' => 'wrong'])
        ->assertSessionHasErrors('username');

    $this->assertGuest();
});

test('admin can logout', function () {
    $admin = Admin::first();

    $this->actingAs($admin)
        ->post('/logout')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('unauthenticated user is redirected to login', function () {
    $this->get(route('terminumfrage.create'))
        ->assertRedirect(route('login'));
});
