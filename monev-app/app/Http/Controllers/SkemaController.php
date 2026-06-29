<?php
namespace App\Http\Controllers;

use App\Models\Skema;
use App\Models\Kategori;
use App\Models\SkemaLuaran;
use Illuminate\Http\Request;

class SkemaController extends Controller
{
    public function index()
    {
        $kategoriList = Kategori::with(['skema' => function ($q) {
            $q->withCount('kegiatan')->with('luaran')->orderBy('nama');
        }])->get();

        return view('skema.index', compact('kategoriList'));
    }

    public function create()
    {
        return view('skema.create', [
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_id'   => 'required|exists:kategori,id',
            'kode'          => 'required|string|max:20|unique:skema,kode',
            'nama'          => 'required|string|max:255',
            'dana_maksimal' => 'nullable|numeric|min:0',
            'durasi_bulan'  => 'nullable|integer|min:1|max:60',
            'deskripsi'     => 'nullable|string',
            'luaran.*.jenis'         => 'required|in:PUBLIKASI,HKI,PRODUK,LAPORAN,LAINNYA',
            'luaran.*.deskripsi'     => 'required|string|max:255',
            'luaran.*.wajib'         => 'nullable|boolean',
            'luaran.*.jumlah_minimal'=> 'nullable|integer|min:1',
        ]);

        $skema = Skema::create([
            'kategori_id'   => $data['kategori_id'],
            'kode'          => strtoupper($data['kode']),
            'nama'          => $data['nama'],
            'dana_maksimal' => $data['dana_maksimal'] ?? 0,
            'durasi_bulan'  => $data['durasi_bulan'] ?? 12,
            'deskripsi'     => $data['deskripsi'],
            'aktif'         => true,
        ]);

        foreach ($request->input('luaran', []) as $l) {
            if (!empty($l['deskripsi'])) {
                SkemaLuaran::create([
                    'skema_id'       => $skema->id,
                    'jenis'          => $l['jenis'],
                    'deskripsi'      => $l['deskripsi'],
                    'wajib'          => isset($l['wajib']),
                    'jumlah_minimal' => $l['jumlah_minimal'] ?? 1,
                ]);
            }
        }

        return redirect()->route('skema.index')
            ->with('success', "Skema \"{$skema->nama}\" berhasil ditambahkan.");
    }

    public function edit(Skema $skema)
    {
        return view('skema.edit', [
            'skema'    => $skema->load('luaran'),
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Skema $skema)
    {
        $data = $request->validate([
            'kategori_id'   => 'required|exists:kategori,id',
            'kode'          => "required|string|max:20|unique:skema,kode,{$skema->id}",
            'nama'          => 'required|string|max:255',
            'dana_maksimal' => 'nullable|numeric|min:0',
            'durasi_bulan'  => 'nullable|integer|min:1|max:60',
            'deskripsi'     => 'nullable|string',
            'aktif'         => 'nullable|boolean',
            'luaran.*.id'            => 'nullable|integer',
            'luaran.*.jenis'         => 'required|in:PUBLIKASI,HKI,PRODUK,LAPORAN,LAINNYA',
            'luaran.*.deskripsi'     => 'required|string|max:255',
            'luaran.*.wajib'         => 'nullable|boolean',
            'luaran.*.jumlah_minimal'=> 'nullable|integer|min:1',
        ]);

        $skema->update([
            'kategori_id'   => $data['kategori_id'],
            'kode'          => strtoupper($data['kode']),
            'nama'          => $data['nama'],
            'dana_maksimal' => $data['dana_maksimal'] ?? 0,
            'durasi_bulan'  => $data['durasi_bulan'] ?? 12,
            'deskripsi'     => $data['deskripsi'],
            'aktif'         => $request->boolean('aktif'),
        ]);

        // Sync luaran: hapus semua lama, insert ulang
        $skema->luaran()->delete();
        foreach ($request->input('luaran', []) as $l) {
            if (!empty($l['deskripsi'])) {
                SkemaLuaran::create([
                    'skema_id'       => $skema->id,
                    'jenis'          => $l['jenis'],
                    'deskripsi'      => $l['deskripsi'],
                    'wajib'          => isset($l['wajib']),
                    'jumlah_minimal' => $l['jumlah_minimal'] ?? 1,
                ]);
            }
        }

        return redirect()->route('skema.index')
            ->with('success', "Skema \"{$skema->nama}\" berhasil diperbarui.");
    }

    public function destroy(Skema $skema)
    {
        if ($skema->kegiatan()->count() > 0) {
            return back()->with('error', "Tidak bisa hapus — skema ini sudah memiliki kegiatan terdaftar.");
        }

        $skema->luaran()->delete();
        $skema->delete();

        return redirect()->route('skema.index')
            ->with('success', "Skema \"{$skema->nama}\" berhasil dihapus.");
    }
}
