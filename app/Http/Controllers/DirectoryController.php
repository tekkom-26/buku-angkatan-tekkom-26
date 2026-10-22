<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    /**
     * Display the student directory with filtering & pagination.
     */
    public function index(Request $request): View
    {
        try {
            // Validate query parameters
            $validated = $request->validate([
                'search' => 'nullable|string|max:100',
                'class' => 'nullable|string|in:A,B,C,D',
                'page' => 'nullable|integer|min:1',
            ]);

            // Query builder
            $query = Student::query()
                ->select([
                    'nim',
                    'name',
                    'nickname',
                    'class_name',
                    'origin_city',
                    'formal_photo_url',
                ])
                ->orderBy('class_name', 'asc')
                ->orderBy('name', 'asc');

            // Apply search filter (nim or name)
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nim', 'LIKE', "%{$search}%")
                      ->orWhere('name', 'LIKE', "%{$search}%")
                      ->orWhere('nickname', 'LIKE', "%{$search}%");
                });
            }

            // Apply class filter
            if ($request->filled('class')) {
                $query->where('class_name', $request->input('class'));
            }

            // Paginate
            $students = $query->paginate(20)->through(function ($student) {
                return [
                    'nim' => $student->nim,
                    'nickname' => $student->nickname ?? $student->name,
                    'class_name' => $student->class_name,
                    'origin_city' => $student->origin_city ?? 'Unknown',
                    'avatar_url' => $student->formal_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->nickname ?? $student->name) . '&background=176B70&color=fff&size=400',
                ];
            });

            // Available filters (all classes)
            $filters = ['A', 'B', 'C', 'D'];

            // Total students count (without filters)
            $totalStudents = Student::count();

            return view('directory', [
                'students' => $students,
                'filters' => $filters,
                'totalStudents' => $totalStudents,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation error - redirect back with errors
            return redirect()
                ->route('directory.index')
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Throwable $e) {
            // Log unexpected errors
            Log::error('Directory index error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all(),
            ]);

            // Show user-friendly error page
            abort(500, 'Terjadi kesalahan saat memuat direktori mahasiswa. Silakan coba lagi nanti.');
        }
    }
}
