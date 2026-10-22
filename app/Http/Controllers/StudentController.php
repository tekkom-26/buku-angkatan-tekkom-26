<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a single student's profile page.
     */
    public function show(string $nim): View
    {
        try {
            // Validate NIM format (must be numeric, typically 8 digits)
            if (!preg_match('/^\d{8}$/', $nim)) {
                abort(404, 'Format NIM tidak valid. NIM harus 8 digit angka.');
            }

            // Find student by NIM with only PUBLIC fields
            $student = Student::query()
                ->select([
                    'nim',
                    'name',
                    'nickname',
                    'class_name',
                    'religion',
                    'birth_place',
                    'birth_date',
                    'origin_city',
                    'instagram_username',
                    'formal_photo_url',
                    'casual_photo_url',
                    'bio_quote',
                    'hobbies',
                    'favorite_food_place',
                    'spotify_song_url',
                ])
                ->where('nim', $nim)
                ->first();

            // Student not found
            if (!$student) {
                abort(404, "Mahasiswa dengan NIM {$nim} tidak ditemukan.");
            }

            // Transform data for view
            $studentData = [
                'nim' => $student->nim,
                'name' => $student->name,
                'nickname' => $student->nickname ?? $student->name,
                'class_name' => $student->class_name,
                'religion' => $student->religion ?? 'Tidak tersedia',
                'birth_place' => $student->birth_place ?? 'Tidak tersedia',
                'birth_date' => $student->birth_date ? $student->birth_date->format('d F Y') : 'Tidak tersedia',
                'age' => $student->birth_date ? $student->birth_date->age : null,
                'origin_city' => $student->origin_city ?? 'Tidak tersedia',
                'instagram_username' => $student->instagram_username ?? 'unknown',
                'instagram_url' => $student->instagram_username 
                    ? 'https://instagram.com/' . $student->instagram_username 
                    : 'https://instagram.com',
                'formal_photo_url' => $student->formal_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->nickname ?? $student->name) . '&background=176B70&color=fff&size=800',
                'casual_photo_url' => $student->casual_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->nickname ?? $student->name) . '&background=4A8B8C&color=fff&size=800',
                'bio_quote' => $student->bio_quote ?? 'Hidup ini indah, nikmati setiap momennya.',
                'hobbies' => $student->hobbies ? (is_string($student->hobbies) ? array_map('trim', explode(',', $student->hobbies)) : $student->hobbies) : ['Membaca', 'Olahraga'],
                'favorite_food_place' => $student->favorite_food_place ?? 'Warung favorit',
                'spotify_song_url' => $student->spotify_song_url ?? null,
                'spotify_track_id' => $this->extractSpotifyTrackId($student->spotify_song_url),
            ];

            // Find previous and next student for navigation
            $prevStudent = Student::query()
                ->select('nim', 'nickname')
                ->where('class_name', $student->class_name)
                ->where('nim', '<', $student->nim)
                ->orderBy('nim', 'desc')
                ->first();

            $nextStudent = Student::query()
                ->select('nim', 'nickname')
                ->where('class_name', $student->class_name)
                ->where('nim', '>', $student->nim)
                ->orderBy('nim', 'asc')
                ->first();

            // Get current position in class
            $currentPosition = Student::query()
                ->where('class_name', $student->class_name)
                ->where('nim', '<=', $student->nim)
                ->count();

            $totalInClass = Student::query()
                ->where('class_name', $student->class_name)
                ->count();

            return view('students.show', [
                'student' => $studentData,
                'prevStudent' => $prevStudent ? [
                    'nim' => $prevStudent->nim,
                    'nickname' => $prevStudent->nickname ?? 'Student',
                ] : null,
                'nextStudent' => $nextStudent ? [
                    'nim' => $nextStudent->nim,
                    'nickname' => $nextStudent->nickname ?? 'Student',
                ] : null,
                'currentPosition' => $currentPosition,
                'totalInClass' => $totalInClass,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Model not found (shouldn't happen with first() but just in case)
            abort(404, "Mahasiswa dengan NIM {$nim} tidak ditemukan.");

        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
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

    private function extractSpotifyTrackId(?string $url): ?string {
        if (!$url) return null;
        if (preg_match('/track\/([a-zA-Z0-9]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}

