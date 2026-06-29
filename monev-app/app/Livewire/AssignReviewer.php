<?php
namespace App\Livewire;

use App\Models\Kegiatan;
use App\Models\PenugasanReviewer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class AssignReviewer extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';
    public int    $filterTahun  = 0;

    // State modal assign
    public bool   $showModal      = false;
    public ?int   $activeKegiatanId = null;
    public int    $selectedReviewer = 0;

    public function openModal(int $kegiatanId): void
    {
        $this->activeKegiatanId = $kegiatanId;
        $this->selectedReviewer = 0;
        $this->showModal        = true;
    }

    public function closeModal(): void
    {
        $this->showModal        = false;
        $this->activeKegiatanId = null;
    }

    public function assign(): void
    {
        $this->validate([
            'selectedReviewer' => 'required|exists:users,id',
        ], ['selectedReviewer.required' => 'Pilih reviewer terlebih dahulu.']);

        $kegiatan = Kegiatan::findOrFail($this->activeKegiatanId);

        // Cegah ketua sekaligus jadi reviewer
        $dosenUserId = $kegiatan->ketua?->user_id;
        if ($dosenUserId && $dosenUserId == $this->selectedReviewer) {
            $this->addError('selectedReviewer', 'Ketua kegiatan tidak boleh menjadi reviewer kegiatan sendiri.');
            return;
        }

        // Cegah duplikat
        $sudahAda = PenugasanReviewer::where('kegiatan_id', $this->activeKegiatanId)
            ->where('reviewer_user_id', $this->selectedReviewer)
            ->exists();

        if ($sudahAda) {
            $this->addError('selectedReviewer', 'Reviewer ini sudah ditugaskan ke kegiatan ini.');
            return;
        }

        PenugasanReviewer::create([
            'kegiatan_id'       => $this->activeKegiatanId,
            'reviewer_user_id'  => $this->selectedReviewer,
            'assigned_by'       => auth()->id(),
            'status'            => 'MENUNGGU',
        ]);

        $this->closeModal();
        session()->flash('success', 'Reviewer berhasil ditugaskan.');
    }

    public function remove(int $penugasanId): void
    {
        $penugasan = PenugasanReviewer::findOrFail($penugasanId);

        if ($penugasan->status !== 'MENUNGGU') {
            session()->flash('error', 'Tidak bisa hapus — reviewer sudah mulai menilai.');
            return;
        }

        $penugasan->delete();
        session()->flash('success', 'Penugasan reviewer dihapus.');
    }

    public function render()
    {
        $kegiatan = Kegiatan::with([
                'skema',
                'kategori',
                'ketua',
                'penugasanReviewer.reviewer:id,name',
            ])
            ->when($this->search, fn($q) =>
                $q->where('judul', 'like', "%{$this->search}%")
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterTahun,  fn($q) => $q->where('tahun', $this->filterTahun))
            ->whereNotIn('status', ['SELESAI'])
            ->orderByDesc('created_at')
            ->paginate(12);

        $tahunList = Kegiatan::selectRaw('DISTINCT tahun')->orderByDesc('tahun')->pluck('tahun');

        // Daftar user dengan role reviewer
        $reviewerList = \App\Models\User::role('reviewer')->orderBy('name')->get(['id', 'name']);

        // Reviewer sudah di-assign ke kegiatan aktif (untuk disable di dropdown)
        $assignedIds = $this->activeKegiatanId
            ? PenugasanReviewer::where('kegiatan_id', $this->activeKegiatanId)
                ->pluck('reviewer_user_id')
            : collect();

        return view('livewire.assign-reviewer', compact(
            'kegiatan', 'tahunList', 'reviewerList', 'assignedIds'
        ));
    }
}
