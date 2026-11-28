<?php

namespace App\Http\Controllers;

use App\Services\StudentDirectoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    public function __construct(protected StudentDirectoryService $directory) {}

    /**
     * Display the student directory with filtering & pagination.
     */
    public function index(Request $request): View|RedirectResponse
    {
        try {
            $validated = $request->validate([
                'search' => 'nullable|string|max:100',
                'class' => 'nullable|string|in:A,B,C,D',
                'aspects' => 'nullable|string|max:32',
                'per_page' => 'nullable|numeric',
                'page' => 'nullable|integer|min:1',
            ]);

            $payload = $this->directory->getDirectoryPayload(
                search: $validated['search'] ?? null,
                classFilter: $validated['class'] ?? null,
                aspectsFilter: StudentDirectoryService::normalizeAspects($validated['aspects'] ?? ''),
                perPage: StudentDirectoryService::clampPerPage($validated['per_page'] ?? null),
                page: (int) ($validated['page'] ?? 1),
                query: $request->query(),
            );

            return view('directory', $payload);

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
