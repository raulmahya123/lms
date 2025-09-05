<?php

// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $r)
    {
        // Ambil kelas yang dipublish; tampilkan 12 terbaru
        $courses = Course::query()
            ->where('is_published', 1)
            ->withCount(['modules','lessons']) // pastikan relasi lessons ada; kalau belum, hapus
            ->latest('id')
            ->take(12)
            ->get();

        // Kategori opsional (kalau ada tabel categories). Kalau belum ada, hapus saja bagian kategori di Blade.
        $categories = [
            ['key' => 'backend', 'name' => 'Backend'],
            ['key' => 'frontend','name' => 'Frontend'],
            ['key' => 'mobile',  'name' => 'Mobile'],
            ['key' => 'data',    'name' => 'Data & AI'],
            ['key' => 'devops',  'name' => 'DevOps'],
            ['key' => 'uiux',    'name' => 'UI/UX'],
        ];

        return view('welcome', compact('courses','categories'));
    }
}
