<?php
namespace App\Livewire;

use App\Models\Kegiatan;
use App\Models\Skema;
use Livewire\Component;
use Livewire\WithPagination;

class KegiatanList extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $filterStatus = '';
    public string $filterSkema  = '';
    public int    $filterTahun  = 0;

    protected $queryString = [
        'search'        => ['except' => ''],
        'filterStatus'  => ['except' => ''],
        'filterSkema'   => ['except' => ''],
        'filterTahun'   => ['except' => 0],
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterSkema(): void { $this->resetPage(); }
    public function updatingFilterTahun(): void { $this->resetPage(); }

    public function delete(int $id): void
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();
        session()->flash('success', 'Kegiatan berhasil dihapus.');
    }

    public function render()
    {
        $kegiatan = Kegiatan::with(['skema', 'kategori', 'ketua'])
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('judul', 'like', "%{$this->search}%")
                       ->orWhereHas('ketua', fn($q3) =>
                           $q3->where('nama', 'like', "%{$this->search}%")
                       )
                )
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterSkema,  fn($q) => $q->where('skema_id', $this->filterSkema))
            ->when($this->filterTahun,  fn($q) => $q->where('tahun', $this->filterTahun))
            ->orderByDesc('created_at')
            ->paginate(15);

        $skemaList = Skema::where('aktif', 1)->orderBy('nama')->get();
        $tahunList = Kegiatan::selectRaw('DISTINCT tahun')->orderByDesc('tahun')->pluck('tahun');

        return view('livewire.kegiatan-list', compact('kegiatan', 'skemaList', 'tahunList'));
    }
}
