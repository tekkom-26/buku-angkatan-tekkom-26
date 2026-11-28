<?php

namespace App\Livewire;

use App\Services\StudentDirectoryService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StudentDirectory extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $class = '';

    #[Url(except: '')]
    public string $aspects = '';

    /**
     * Normalize incoming URL filter state on mount.
     */
    public function mount(): void
    {
        if (! in_array($this->class, ['', 'A', 'B', 'C', 'D'], true)) {
            $this->class = '';
        }

        $this->aspects = implode(',', StudentDirectoryService::normalizeAspects($this->aspects));
    }

    /**
     * Reset pagination whenever search input updates live.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Select class tab filter and reset pagination.
     */
    public function selectClass(string $class): void
    {
        $this->class = in_array($class, ['A', 'B', 'C', 'D'], true) ? $class : '';
        $this->resetPage();
    }

    /**
     * Toggle a single MBTI aspect (activates, switches opposing pole, or deactivates).
     */
    public function toggleAspect(string $aspect): void
    {
        $next = StudentDirectoryService::toggleAspectInList(
            StudentDirectoryService::normalizeAspects($this->aspects),
            $aspect,
        );

        $this->aspects = implode(',', $next);
        $this->resetPage();
    }

    /**
     * Clear active MBTI aspect filters without clearing search/class.
     */
    public function resetAspects(): void
    {
        $this->aspects = '';
        $this->resetPage();
    }

    /**
     * Clear search, class, and MBTI aspect filters.
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->class = '';
        $this->aspects = '';
        $this->resetPage();
    }

    public function render(StudentDirectoryService $directory): View
    {
        $activeAspects = StudentDirectoryService::normalizeAspects($this->aspects);
        $aspectsQuery = $activeAspects !== [] ? implode(',', $activeAspects) : null;

        $payload = $directory->getDirectoryPayload(
            search: trim($this->search) !== '' ? trim($this->search) : null,
            classFilter: $this->class !== '' ? $this->class : null,
            aspectsFilter: $activeAspects,
            perPage: 15,
            page: max(1, (int) $this->getPage()),
            query: array_filter([
                'search' => trim($this->search) !== '' ? trim($this->search) : null,
                'class' => $this->class !== '' ? $this->class : null,
                'aspects' => $aspectsQuery,
            ]),
        );

        return view('livewire.student-directory', $payload);
    }
}
