<?php
namespace App\Livewire;

use App\Models\Dosen;
use App\Models\Prodi;
use Livewire\Component;
use Livewire\WithPagination;

class DosenList extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $filterProdi = '';

    public function updatingSearch(): void      { $this->resetPage(); }
    public function updatingFilterProdi(): void { $this->resetPage(); }

    public function delete(int $id): void
    {
        $dosen = Dosen::withCount('kegiatan')->findOrFail($id);

        if ($dosen->kegiatan_count > 0) {
            session()->flash('error', "Tidak bisa menghapus: dosen \"{$dosen->nama}\" masih memiliki kegiatan.");
            return;
        }

        $nama = $dosen->nama;
        $dosen->delete();
        session()->flash('success', "Dosen \"{$nama}\" berhasil dihapus.");
    }

    public function render()
    {
        $dosenList = Dosen::with(['prodi.fakultas', 'user'])
            ->withCount(['kegiatan', 'kegiatanAnggota'])
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('nama', 'like', "%{$this->search}%")
                       ->orWhere('nidn', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterProdi, fn($q) => $q->where('prodi_id', $this->filterProdi))
            ->orderBy('nama')
            ->paginate(15);

        $prodiAll = Prodi::with('fakultas')->orderBy('nama')->get();

        return view('livewire.dosen-list', compact('dosenList', 'prodiAll'));
    }
}
