<?php

namespace App\Http\Controllers;

use App\Services\WebContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function __construct(
        protected WebContentService $contentService,
    ) {}

    /**
     * Display the landing page (Buku Angkatan).
     */
    public function index(Request $request): View|JsonResponse
    {
        $landingContent = $this->contentService->getLandingContent();

        if ($request->expectsJson()) {
            return response()->json([
                'framework' => 'Laravel',
                'data' => $landingContent,
            ]);
        }

        return view('landing', [
            'landingContent' => $landingContent,
        ]);
    }

    /**
     * Display the philosophy & identity page.
     */
    public function identity(Request $request): View
    {
        $identityContent = $this->contentService->getIdentityContent();

        return view('identity', [
            'identityContent' => $identityContent,
        ]);
    }
}
