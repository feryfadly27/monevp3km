<?php
namespace App\Livewire;

use App\Models\KriteriaPenilaian;
use App\Models\Kategori;
use App\Models\Skema;
use Livewire\Component;

class SettingKriteria extends Component
{
    public bool    $showModal  = false;
    public ?int    $editId     = null;

    public string  $nama      = '';
    public string  $scope     = 'GLOBAL';
    public ?int    $kategoriId = null;
    public ?int    $skemaId    = null;
    public string  $bobot     = '';
    public int     $skorMin   = 1;
    public int     $skorMax   = 100;
    public int     $urutan    = 0;
    public bool    $aktif     = true;

    public function openModal(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->editId = $id;

        if ($id) {
            $k = KriteriaPenilaian::findOrFail($id);
            $this->nama       = $k->nama;
            $this->scope      = $k->scope;
            $this->kategoriId = $k->kategori_id;
            $this->skemaId    = $k->skema_id;
            $this->bobot      = (string) $k->bobot;
            $this->skorMin    = $k->skor_min;
            $this->skorMax    = $k->skor_max;
            $this->urutan     = $k->urutan;
            $this->aktif      = (bool) $k->aktif;
        } else {
            $this->nama = ''; $this->scope = 'GLOBAL';
            $this->kategoriId = null; $this->skemaId = null;
            $this->bobot = ''; $this->skorMin = 1; $this->skorMax = 100;
            $this->urutan = KriteriaPenilaian::max('urutan') + 1;
            $this->aktif = true;
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'nama'       => 'required|string|max:255',
            'scope'      => 'required|in:GLOBAL,KATEGORI,SKEMA',
            'kategoriId' => 'nullable|required_if:scope,KATEGORI|exists:kategori,id',
            'skemaId'    => 'nullable|required_if:scope,SKEMA|exists:skema,id',
            'bobot'      => 'required|numeric|min:0.01|max:100',
            'skorMin'    => 'required|integer|min:0|max:99',
            'skorMax'    => 'required|integer|min:1|max:100|gte:skorMin',
            'urutan'     => 'required|integer|min:0',
        ], [
            'kategoriId.required_if' => 'Pilih kategori untuk scope KATEGORI.',
            'skemaId.required_if'    => 'Pilih skema untuk scope SKEMA.',
            'skorMax.gte'            => 'Skor maksimal harus ≥ skor minimal.',
        ]);

        $payload = [
            'nama'        => $this->nama,
            'scope'       => $this->scope,
            'kategori_id' => $this->scope === 'KATEGORI' ? $this->kategoriId : null,
            'skema_id'    => $this->scope === 'SKEMA'    ? $this->skemaId    : null,
            'bobot'       => (float) $this->bobot,
            'skor_min'    => $this->skorMin,
            'skor_max'    => $this->skorMax,
            'urutan'      => $this->urutan,
            'aktif'       => $this->aktif,
        ];

        if ($this->editId) {
            KriteriaPenilaian::findOrFail($this->editId)->update($payload);
            session()->flash('success_k', 'Kriteria diperbarui.');
        } else {
            KriteriaPenilaian::create($payload);
            session()->flash('success_k', 'Kriteria ditambahkan.');
        }

        $this->showModal = false;
    }

    public function toggleAktif(int $id): void
    {
        $k = KriteriaPenilaian::findOrFail($id);
        $k->update(['aktif' => !$k->aktif]);
    }

    public function delete(int $id): void
    {
        KriteriaPenilaian::findOrFail($id)->delete();
        session()->flash('success_k', 'Kriteria dihapus.');
    }

    public function render()
    {
        $kriteria    = KriteriaPenilaian::with(['kategori', 'skema'])
                        ->orderBy('scope')->orderBy('urutan')->get();
        $totalBobot  = $kriteria->where('aktif', true)->sum('bobot');
        $kategoriAll = Kategori::orderBy('nama')->get();
        $skemaAll    = Skema::orderBy('nama')->get();

        return view('livewire.setting-kriteria',
            compact('kriteria', 'totalBobot', 'kategoriAll', 'skemaAll'));
    }
}
