<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
                ->public()
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
            $students = $query->paginate(15)->through(function ($student) {
                $hobbies = $student->hobbies
                    ? (is_string($student->hobbies) ? array_values(array_filter(array_map('trim', explode(',', $student->hobbies)))) : (array) $student->hobbies)
                    : ['Hardware', 'Sistem Digital'];

                return [
                    'nim' => $student->nim,
                    'name' => $student->name,
                    'nickname' => $student->nickname ?? $student->name,
                    'class_name' => $student->class_name,
                    'origin_city' => $student->origin_city ?? 'Semarang',
                    'hobbies' => array_slice($hobbies, 0, 3),
                    'formal_photo_url' => $student->formal_photo_url ?: asset('/assets/Placeholder.png'),
                    'avatar_url' => $student->formal_photo_url ?: asset('/assets/Placeholder.png'),
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

        } catch (ValidationException $e) {
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
