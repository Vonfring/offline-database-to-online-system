<?php

namespace App\Http\Controllers;

use App\Models\JenisPlastik;
use App\Models\Pembeli;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'jumlahSupplier' => Supplier::count(),
            'jumlahPembeli' => Pembeli::count(),
            'jenisPlastik' => JenisPlastik::with('hargaBerlaku')->orderBy('kode')->get(),
        ]);
    }
}
