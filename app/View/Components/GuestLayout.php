<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public $title;
    public $description;
    public function __construct($title = null, $description = null)
    {
        $this->title = $title ?? 'Login';
        $this->description = $description ?? 'Masukkan email dan kata sandi Anda untuk masuk';
    }
    public function render(): View
    {
        return view('layouts.admin.guest');
    }
}
