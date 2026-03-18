<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->doktor = User::factory()->create(['role' => 'doktor']);
    $this->pacijent = User::factory()->create(['role' => 'pacijent']);
});

test('admin sees patients menu item', function () {
    $this->actingAs($this->admin)
        ->get('/dashboard')
        ->assertSee('Patients');
});

test('doktor sees patients menu item', function () {
    $this->actingAs($this->doktor)
        ->get('/dashboard')
        ->assertSee('Patients');
});

test('pacijent does not see patients menu item in nav', function () {
    $this->actingAs($this->pacijent)
        ->get('/dashboard')
        ->assertDontSee(route('patients.index'));
});
