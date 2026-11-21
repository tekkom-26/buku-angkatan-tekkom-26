<?php

namespace App\Http\Controllers;

use App\Services\StudentDirectoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StudentController extends Controller
{
    public function __construct(protected StudentDirectoryService $directory) {}

    /**
     * Display a single student's profile page.
     */
    public function show(string $nim): View
    {
        try {
            // Validate NIM format (must be numeric, typically 14 digits)
            if (! preg_match('/^\d{14}$/', $nim)) {
                abort(404, 'Format NIM tidak valid. NIM harus 8 digit angka.');
            }

            $payload = $this->directory->getStudentProfilePayload($nim);

            if ($payload === null) {
                abort(404, "Mahasiswa dengan NIM {$nim} tidak ditemukan.");
            }

            return view('students.show', $payload);

        } catch (ModelNotFoundException $e) {
            // Model not found (shouldn't happen with first() but just in case)
            abort(404, "Mahasiswa dengan NIM {$nim} tidak ditemukan.");

        } catch (HttpException $e) {
            // Re-throw HTTP exceptions (404, etc)
            throw $e;
        } catch (\Throwable $e) {
            // Log unexpected errors
            Log::error('Student show error', [
                'nim' => $nim,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Show user-friendly error
            abort(500, 'Terjadi kesalahan saat memuat profil mahasiswa. Silakan coba lagi nanti.');
        }
    }
}
