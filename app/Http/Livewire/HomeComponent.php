<?php

namespace App\Http\Livewire;

use App\Models\Tester;
use App\Traits\UploadImageTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class HomeComponent extends Component
{
    use WithPagination, WithFileUploads,UploadImageTrait;

    public string $catchError;
    public string $modalId = '';
    public string $sortBy = 'id';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public string $search = '';
    public string $process_type;
    public string $current_role;
    public string $successMessage = '';
    public $testers = [];
    protected string $paginationTheme = 'bootstrap';

    protected $queryString = ['search' => ['except' => '']];

    public function hydrate()
    {
        $this->dispatchBrowserEvent('render-select2');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSearch_()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        return $this->sortBy = $field;
    }


    public function resetMessage()
    {
        $this->catchError = '';
        $this->successMessage = '';
    }

    protected function cleanupOldUploads()
    {
        $storage = Storage::disk('local');

        foreach ($storage->allFiles('livewire-tmp') as $filepathname){
            $yesterdayStamp = now()->subSeconds(5)->timestamp;
            if ($yesterdayStamp > $storage->lastModified($filepathname)) {
                $storage->delete($filepathname);
            }
        }
    }

    public function all_Testers()
    {
        if (!Cache::has(Tester::CACHE_KEY)) {
            Cache::rememberForever(Tester::CACHE_KEY, function () {
                return $this->testers = Tester::with('user')->get();
            });
        }
        $this->testers = Cache::get(Tester::CACHE_KEY);
    }

}
