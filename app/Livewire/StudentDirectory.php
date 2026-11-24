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

    /**
     * Normalize incoming URL filter state on mount.
     */
    public function mount(): void
    {
        if (! in_array($this->class, ['', 'A', 'B', 'C', 'D'], true)) {
            $this->class = '';
        }
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
     * Clear both search and class filters.
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->class = '';
        $this->resetPage();
    }

    public function render(StudentDirectoryService $directory): View
    {
        $payload = $directory->getDirectoryPayload(
            search: trim($this->search) !== '' ? trim($this->search) : null,
            classFilter: $this->class !== '' ? $this->class : null,
            perPage: 15,
            page: max(1, (int) $this->getPage()),
            query: array_filter([
                'search' => trim($this->search) !== '' ? trim($this->search) : null,
                'class' => $this->class !== '' ? $this->class : null,
            ]),
        );

        return view('livewire.student-directory', $payload);
    }
}
