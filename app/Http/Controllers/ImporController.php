<?php

namespace App\Http\Controllers;

use App\Exports\TemplateImpor;
use App\Models\LogImpor;
use App\Services\Impor\DaftarImpor;
use App\Services\Impor\DefinisiImpor;
use App\Services\Impor\HasilValidasi;
use App\Services\Impor\PembacaFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * Migrasi data offline (khusus admin):
 * unggah file → cocokkan kolom → validasi & pratinjau → konfirmasi → simpan + catat log_impor.
 *
 * File yang sedang diproses disimpan sementara di storage/app/private/impor,
 * sedangkan status prosesnya disimpan di sesi dengan kunci "impor".
 */
class ImporController extends Controller
{
    private const SESI = 'impor';

    private const FOLDER = 'impor';

    public function __construct(private PembacaFile $pembaca) {}

    /** Langkah 1: pilih jenis data & unggah file. */
    public function index(Request $request): View
    {
        return view('impor.index', [
            'daftarJenis' => DaftarImpor::semua(),
            'jenisDipilih' => $request->old('jenis_data', $request->query('jenis', session(self::SESI.'.jenis', 'supplier'))),
            'sedangBerjalan' => session(self::SESI),
            'riwayatTerakhir' => LogImpor::with('pengguna')->latest('id')->limit(5)->get(),
        ]);
    }

    public function template(string $jenis): BinaryFileResponse
    {
        $definisi = DaftarImpor::ambil($jenis);

        return Excel::download(new TemplateImpor($definisi), "template-impor-{$jenis}.xlsx");
    }

    public function unggah(Request $request): RedirectResponse
    {
        $request->validate([
            'jenis_data' => ['required', Rule::in(DaftarImpor::kunci())],
            'file' => ['required', 'file', 'max:5120', 'mimes:xlsx,xls,csv,txt', 'extensions:xlsx,xls,csv'],
        ], [
            'file.required' => 'Pilih file Excel atau CSV yang akan diunggah.',
            'file.mimes' => 'File harus berformat .xlsx, .xls, atau .csv.',
            'file.extensions' => 'File harus berformat .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $definisi = DaftarImpor::ambil($request->jenis_data);
        $file = $request->file('file');
        $ekstensi = strtolower($file->getClientOriginalExtension());
        $path = $file->storeAs(self::FOLDER, Str::uuid().'.'.$ekstensi, 'local');

        try {
            $isi = $this->pembaca->baca(Storage::disk('local')->path($path));
        } catch (Throwable $e) {
            report($e);
            Storage::disk('local')->delete($path);

            return back()->withInput()->with('gagal', 'File tidak dapat dibaca. Pastikan file berformat Excel/CSV yang benar dan tidak dikunci kata sandi.');
        }

        if (! $isi['judul']) {
            Storage::disk('local')->delete($path);

            return back()->withInput()->with('gagal', 'File kosong. Baris pertama harus berisi judul kolom.');
        }

        if (! $isi['baris']) {
            Storage::disk('local')->delete($path);

            return back()->withInput()->with('gagal', 'File hanya berisi judul kolom tanpa data.');
        }

        if (count($isi['baris']) > PembacaFile::MAKS_BARIS) {
            Storage::disk('local')->delete($path);

            return back()->withInput()->with('gagal', 'File berisi lebih dari '.angka(PembacaFile::MAKS_BARIS).' baris. Bagi menjadi beberapa file.');
        }

        // Saat unggah ulang dengan susunan kolom yang sama, pemetaan sebelumnya dipakai lagi.
        $sebelumnya = session(self::SESI);
        $pemetaan = ($sebelumnya && $sebelumnya['jenis'] === $definisi->kunci() && $sebelumnya['judul'] === $isi['judul'])
            ? $sebelumnya['pemetaan']
            : $definisi->tebakPemetaan($isi['judul']);

        $this->hapusFileSesi();

        session([self::SESI => [
            'jenis' => $definisi->kunci(),
            'nama_file' => Str::limit($file->getClientOriginalName(), 250, ''),
            'path' => $path,
            'judul' => $isi['judul'],
            'jumlah_baris' => count($isi['baris']),
            'pemetaan' => $pemetaan,
            'pemetaan_dikonfirmasi' => $sebelumnya && ($sebelumnya['pemetaan_dikonfirmasi'] ?? false) && $pemetaan === $sebelumnya['pemetaan'],
        ]]);

        if (session(self::SESI.'.pemetaan_dikonfirmasi')) {
            return redirect()->route('impor.pratinjau')->with('info', 'File baru diunggah dan divalidasi ulang dengan pemetaan kolom sebelumnya.');
        }

        return redirect()->route('impor.pemetaan');
    }

    /** Langkah 2: cocokkan kolom file dengan kolom sistem. */
    public function pemetaan(): View|RedirectResponse
    {
        if (! $sesi = $this->sesi()) {
            return redirect()->route('impor.index')->with('info', 'Silakan unggah file terlebih dahulu.');
        }

        $definisi = DaftarImpor::ambil($sesi['jenis']);
        $isi = $this->pembaca->baca(Storage::disk('local')->path($sesi['path']));

        return view('impor.pemetaan', [
            'sesi' => $sesi,
            'definisi' => $definisi,
            'contoh' => array_slice($isi['baris'], 0, 3, true),
        ]);
    }

    public function simpanPemetaan(Request $request): RedirectResponse
    {
        if (! $sesi = $this->sesi()) {
            return redirect()->route('impor.index');
        }

        $definisi = DaftarImpor::ambil($sesi['jenis']);
        $indeksValid = array_keys($sesi['judul']);

        $aturan = [];
        foreach ($definisi->kolom() as $kunci => $k) {
            $aturan["pemetaan.{$kunci}"] = [$k['wajib'] ? 'required' : 'nullable', Rule::in($indeksValid)];
        }

        $request->validate($aturan, [
            'required' => 'Kolom ":attribute" wajib dipasangkan dengan salah satu kolom file.',
            'in' => 'Pilihan kolom untuk ":attribute" tidak valid.',
        ], collect($definisi->kolom())->mapWithKeys(fn ($k, $kunci) => ["pemetaan.{$kunci}" => $k['label']])->all());

        $pemetaan = collect($request->input('pemetaan', []))
            ->only(array_keys($definisi->kolom()))
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v) => (int) $v);

        $ganda = $pemetaan->duplicates();
        if ($ganda->isNotEmpty()) {
            return back()->withInput()->with('gagal', 'Satu kolom file tidak boleh dipasangkan ke lebih dari satu kolom sistem ("'.$sesi['judul'][$ganda->first()].'").');
        }

        session([
            self::SESI.'.pemetaan' => $pemetaan->all(),
            self::SESI.'.pemetaan_dikonfirmasi' => true,
        ]);

        return redirect()->route('impor.pratinjau');
    }

    /** Langkah 3: hasil validasi & pratinjau baris valid/tidak valid. */
    public function pratinjau(): View|RedirectResponse
    {
        $sesi = $this->sesi();

        if (! $sesi || ! ($sesi['pemetaan_dikonfirmasi'] ?? false)) {
            return redirect()->route($sesi ? 'impor.pemetaan' : 'impor.index');
        }

        [$definisi, $hasil] = $this->validasiSesi($sesi);

        return view('impor.pratinjau', compact('sesi', 'definisi', 'hasil'));
    }

    /** Langkah 4: simpan baris valid ke database dan catat riwayat impor. */
    public function konfirmasi(Request $request): RedirectResponse
    {
        $sesi = $this->sesi();

        if (! $sesi || ! ($sesi['pemetaan_dikonfirmasi'] ?? false)) {
            return redirect()->route('impor.index')->with('gagal', 'Sesi impor tidak ditemukan. Silakan unggah ulang file.');
        }

        // Validasi diulang di sini agar data yang disimpan selalu sesuai kondisi database terbaru.
        [$definisi, $hasil] = $this->validasiSesi($sesi);

        if ($hasil->jumlahValid() === 0) {
            return redirect()->route('impor.pratinjau')->with('gagal', 'Tidak ada baris valid yang bisa diimpor. Perbaiki file lalu unggah ulang.');
        }

        try {
            $tersimpan = $definisi->simpan($hasil, $request->user());
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('impor.pratinjau')->with('gagal', 'Impor gagal disimpan dan dibatalkan seluruhnya. Tidak ada data yang masuk. Silakan coba lagi.');
        }

        $log = LogImpor::create([
            'pengguna_id' => $request->user()->id,
            'nama_file' => $sesi['nama_file'],
            'jenis_data' => $definisi->kunci(),
            'jumlah_baris' => $hasil->jumlahBaris(),
            'jumlah_berhasil' => $tersimpan,
            'jumlah_gagal' => $hasil->jumlahTidakValid(),
        ]);

        $this->hapusFileSesi();
        session()->forget(self::SESI);

        return redirect()->route('impor.hasil', $log)->with('sukses', "Impor selesai: {$tersimpan} baris berhasil disimpan.");
    }

    public function batal(): RedirectResponse
    {
        $this->hapusFileSesi();
        session()->forget(self::SESI);

        return redirect()->route('impor.index')->with('info', 'Proses impor dibatalkan. Tidak ada data yang disimpan.');
    }

    /** Ringkasan satu proses impor. */
    public function hasil(LogImpor $logImpor): View
    {
        $logImpor->load('pengguna');

        return view('impor.hasil', ['log' => $logImpor]);
    }

    public function riwayat(Request $request): View
    {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
        ]);

        $log = LogImpor::query()
            ->with('pengguna')
            ->when($request->jenis, fn ($q, $jenis) => $q->where('jenis_data', $jenis))
            ->when($request->q, fn ($q, $kata) => $q->where('nama_file', 'ilike', "%{$kata}%"))
            ->when($request->dari, fn ($q, $dari) => $q->whereDate('created_at', '>=', $dari))
            ->when($request->sampai, fn ($q, $sampai) => $q->whereDate('created_at', '<=', $sampai))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('impor.riwayat', ['log' => $log, 'daftarJenis' => DaftarImpor::semua()]);
    }

    /** @return array{0: DefinisiImpor, 1: HasilValidasi} */
    private function validasiSesi(array $sesi): array
    {
        $definisi = DaftarImpor::ambil($sesi['jenis']);
        $isi = $this->pembaca->baca(Storage::disk('local')->path($sesi['path']));
        $baris = $this->pembaca->petakan($isi['baris'], $sesi['pemetaan']);

        return [$definisi, $definisi->validasi($baris)];
    }

    private function sesi(): ?array
    {
        $sesi = session(self::SESI);

        if ($sesi && ! Storage::disk('local')->exists($sesi['path'])) {
            session()->forget(self::SESI);

            return null;
        }

        return $sesi;
    }

    private function hapusFileSesi(): void
    {
        if ($path = session(self::SESI.'.path')) {
            Storage::disk('local')->delete($path);
        }
    }
}
