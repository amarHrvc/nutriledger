<?php

use App\Livewire\Admin\DeleteUser;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(DeleteUser::class)
        ->assertStatus(200);
});
