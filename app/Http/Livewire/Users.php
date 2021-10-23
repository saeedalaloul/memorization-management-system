<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination, WithFileUploads;

    public $show_table = true, $catchError;
    public $sortBy = 'id', $sortDirection = 'asc', $perPage = 10, $search = '';
    protected $paginationTheme = 'bootstrap';

    public function render()
    {

        return view('livewire.users', ['users' => $this->all_Users()]);
    }

    public function activeEmail($id)
    {
        $user = User::find($id);
        if ($user != null) {
            if ($user->email_verified_at == null) {
                $user->markEmailAsVerified();
                event(new Verified($user));
                session()->flash('success_message', 'تمت عملية تفعيل البريد الإلكتروني بنجاح.');
            } else {
                $user->update(['email_verified_at' => null]);
                session()->flash('success_message', 'تمت عملية إلغاء تفعيل البريد الإلكتروني بنجاح.');
            }
        }
    }

    public function showformadd($isShow)
    {
        $this->show_table = $isShow;
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

    public function all_Users()
    {
        return User::query()
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }
}
