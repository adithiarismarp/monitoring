<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Member;
use App\Models\history;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class HistoryController extends Controller
{

    //Function untuk menampikan data semua history
    public function index()
    {

        $history = history::all();

        // dd($history);
        return view('history.index', ['history' => $history], compact('history'));
    }

    -
    //Function untuk menampilkan halaman  tambah  history
    public function tambah()
    {
        $member = Member::all();
        $produk = Produk::all();
        $history = history::all();
        return view('history.tambah', compact('history', 'member', 'produk'));
    }

    //Function untuk menambah data history
    public function store(Request $request, history $history)
    {

        //history
        $dt = Carbon::now();
        $todayDate = $dt->toDayDateTimeString();

        History::create([
            'id_history' => $history,
            'progress' => "Nota Dibuat, Barang di terima.",
            'tgl' =>     $todayDate,
        ]);

        return redirect('/history')->with('status', 'Data history Berhasil Ditambahkan!');
    }
}
