<?php
namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $filterRole  = '';
    public string $filterStatus = 'active'; // default tampilkan yang aktif

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterRole(): void   { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function aktivasi(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);
        session()->flash('success', "Akun \"{$user->name}\" berhasil diaktifkan.");
    }

    public function tolak(int $id): void
    {
        $user = User::findOrFail($id);
        $nama = $user->name;
        // Hapus dosen & user
        $user->dosen?->delete();
        $user->delete();
        session()->flash('success', "Pendaftaran \"{$nama}\" ditolak dan dihapus.");
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Tidak bisa menghapus akun sendiri.');
            return;
        }
        User::findOrFail($id)->delete();
        session()->flash('success', 'User berhasil dihapus.');
    }

    public function render()
    {
        $users = User::with('roles', 'dosen')
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterRole, fn($q) => $q->role($this->filterRole))
            ->where('status', $this->filterStatus ?: 'active')
            ->orderBy($this->filterStatus === 'pending' ? 'created_at' : 'name',
                      $this->filterStatus === 'pending' ? 'desc' : 'asc')
            ->paginate(15);

        $pendingCount = User::where('status', 'pending')->count();

        return view('livewire.user-list', compact('users', 'pendingCount'));
    }
}
