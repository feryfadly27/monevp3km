<?php
namespace App\Livewire;

use App\Models\Fakultas;
use App\Models\Prodi;
use Livewire\Component;

class SettingFakultas extends Component
{
    // Fakultas modal
    public bool   $showFakModal = false;
    public ?int   $editFakId    = null;
    public string $fakNama      = '';

    // Prodi modal
    public bool   $showProdiModal = false;
    public ?int   $editProdiId   = null;
    public string $prodiNama     = '';
    public ?int   $prodiAFak     = null;

    public function openFakModal(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->editFakId = $id;
        $this->fakNama   = $id ? Fakultas::findOrFail($id)->nama : '';
        $this->showFakModal = true;
    }

    public function saveFakultas(): void
    {
        $this->validate(['fakNama' => 'required|string|max:255|unique:fakultas,nama,' . ($this->editFakId ?? 'NULL')]);

        if ($this->editFakId) {
            Fakultas::findOrFail($this->editFakId)->update(['nama' => $this->fakNama]);
            session()->flash('success', 'Fakultas diperbarui.');
        } else {
            Fakultas::create(['nama' => $this->fakNama]);
            session()->flash('success', 'Fakultas ditambahkan.');
        }

        $this->showFakModal = false;
    }

    public function deleteFakultas(int $id): void
    {
        $fak = Fakultas::withCount('prodi')->findOrFail($id);
        if ($fak->prodi_count > 0) {
            session()->flash('error', "Tidak bisa menghapus: \"{$fak->nama}\" masih memiliki {$fak->prodi_count} prodi.");
            return;
        }
        $fak->delete();
        session()->flash('success', 'Fakultas dihapus.');
    }

    public function openProdiModal(?int $id = null, ?int $fakId = null): void
    {
        $this->resetErrorBag();
        $this->editProdiId  = $id;
        $this->prodiAFak    = $fakId;
        $this->prodiNama    = $id ? Prodi::findOrFail($id)->nama : '';
        if ($id) $this->prodiAFak = Prodi::findOrFail($id)->fakultas_id;
        $this->showProdiModal = true;
    }

    public function saveProdi(): void
    {
        $this->validate([
            'prodiNama' => 'required|string|max:255',
            'prodiAFak' => 'required|exists:fakultas,id',
        ], [
            'prodiAFak.required' => 'Pilih fakultas.',
            'prodiAFak.exists'   => 'Fakultas tidak valid.',
        ]);

        if ($this->editProdiId) {
            Prodi::findOrFail($this->editProdiId)->update(['nama' => $this->prodiNama, 'fakultas_id' => $this->prodiAFak]);
            session()->flash('success', 'Program studi diperbarui.');
        } else {
            Prodi::create(['nama' => $this->prodiNama, 'fakultas_id' => $this->prodiAFak]);
            session()->flash('success', 'Program studi ditambahkan.');
        }

        $this->showProdiModal = false;
    }

    public function deleteProdi(int $id): void
    {
        $prodi = Prodi::withCount('dosen')->findOrFail($id);
        if ($prodi->dosen_count > 0) {
            session()->flash('error', "Tidak bisa menghapus: \"{$prodi->nama}\" masih digunakan oleh {$prodi->dosen_count} dosen.");
            return;
        }
        $prodi->delete();
        session()->flash('success', 'Program studi dihapus.');
    }

    public function render()
    {
        return view('livewire.setting-fakultas', [
            'fakultasAll' => Fakultas::with('prodi')->orderBy('nama')->get(),
        ]);
    }
}
