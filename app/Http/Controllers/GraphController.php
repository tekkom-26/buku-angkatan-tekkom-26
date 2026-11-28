<?php

namespace App\Http\Controllers;

use App\Services\StudentDirectoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GraphController extends Controller
{
    public function __construct(protected StudentDirectoryService $directory) {}

    /**
     * Display the interactive Obsidian-style MBTI force-directed graph explorer.
     */
    public function index(Request $request): View
    {
        $aspects = StudentDirectoryService::normalizeAspects((string) $request->query('aspects', ''));

        return view('graph', [
            'graphPayload' => $this->directory->getGraphPayload($aspects),
        ]);
    }
}
