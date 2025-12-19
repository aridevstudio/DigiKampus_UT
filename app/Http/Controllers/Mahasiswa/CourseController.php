<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    /**
     * Show get courses page (course catalog)
     */
    public function index()
    {
        return view('pages.mahasiswa.get-courses');
    }

    /**
     * Show course detail page
     */
    public function show($id)
    {
        return view('pages.mahasiswa.course-detail', ['id' => $id]);
    }
}
