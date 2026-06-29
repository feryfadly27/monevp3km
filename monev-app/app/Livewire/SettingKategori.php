<?php
namespace App\Livewire;

use App\Models\Kategori;
use Livewire\Component;

class SettingKategori extends Component
{
    public bool   $showModal = false;
    public ?int   $editId   = null;
    public string $nama     = '';

    public function openModal(int $id): void
    {
        $this->resetErrorBag();
        $this->editId = $id;
        $this->nama   = Kategori::findOrFail($id)->nama;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate(['nama' => 'required|string|max:100']);
        Kategori::findOrFail($this->editId)->update(['nama' => $this->nama]);
        session()->flash('success_kategori', 'Kategori diperbarui.');
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.setting-kategori', [
            'kategoriAll' => Kategori::orderBy('kode')->get(),
        ]);
    }
}
