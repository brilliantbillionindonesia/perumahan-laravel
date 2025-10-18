<?php

namespace App\Http\Controllers\Web\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    public function import(Request $request){
        // Validasi input dari form
        $validated = $request->validate([
            'file' => 'required|string|max:255',
        ]);
    }
}
