<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Member;
use App\Models\perbaikan;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class PerbaikanController extends Controller
{

    //Function untuk menampikan data semua perbaikan
    public function index()
    {

        $perbaikan = Perbaikan::all();

        // dd($perbaikan);
        return view('perbaikan.index', ['perbaikan' => $perbaikan], compact('perbaikan'));
    }

    public function data()
    {
        $perbaikan = Perbaikan::orderBy('id', 'desc')->get();

        return datatables()
            ->of($perbaikan)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {
                return '
                <div class="btn-group">
                    <button onclick="editForm(`' . route('kategori.update', $kategori->id_kategori) . '`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="deleteData(`' . route('kategori.destroy', $kategori->id_kategori) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    //Function untuk menampilkan halaman  tambah  perbaikan
    public function tambah()
    {

        $member = Member::all();
        $produk = Produk::all();
        $perbaikan = perbaikan::all();
        return view('perbaikan.tambah', compact('perbaikan', 'member', 'produk'));
    }

    //Function untuk menambah data perbaikan
    public function store(Request $request, perbaikan $perbaikan)
    {

        $request->validate([
            'kode_unik' => 'unique:perbaikan',
        ]);

        //Data perbaikan harus diisi dengan huruf dan harus unique dengan karakter maksimal 255 huruf
        //Function untuk menambah data perbaikan baru
        $perbaikan = Perbaikan::create([
            'nama_customer' => $request->nama_customer,
            'daftar_perbaikan' => $request->daftar_perbaikan,
            'serial_number' => $request->serial_number,
            'model' => $request->model,
            'dp' => $request->dp,
            'note' => $request->note,
            'jenis' => $request->jenis,
            'status' => $request->status,
            'total' => $request->total,
            'kode_unik' => $request->kode_unik,


        ])->id;

        //history
        $dt = Carbon::now();
        $todayDate = $dt->toDayDateTimeString();

        History::create([
            'id_perbaikan' => $perbaikan,
            'progress' => "Nota Dibuat, Barang di terima.",
            'tgl' =>     $todayDate,
        ]);

        return redirect('/perbaikan')->with('status', 'Data perbaikan Berhasil Ditambahkan!');
    }

    //Function untuk menampilkan halaman edit perbaikan, dan mambawa data perbaikan yang sudah ada
    public function edit($id, perbaikan $perbaikan)

    {
        $perbaikan = perbaikan::find($id);
        $member = Member::all();
        $teknisi = Supplier::all();
        $produk = Produk::all();
        $history = History::join('perbaikan', 'history.id_perbaikan', '=', 'perbaikan.id')
            ->where('perbaikan.id', $id)
            ->get();

        return view('perbaikan.edit', compact('perbaikan', 'member', 'produk', 'teknisi', 'history'));
    }
    public function update(Request $request, perbaikan $perbaikan, $id)
    {

        //update data perbaikan
        $perbaikan = Perbaikan::find($id)->update($request->all());

        return redirect('/perbaikan')->with('status', 'Data perbaikan Berhasil Diubah!');
    }
    public function TambahHistory(Request $request, perbaikan $perbaikan)

    {
        History::create([
            'id_perbaikan' => $request->id_perbaikan,
            'id_teknisi' => $request->id_teknisi,
            'progress' => $request->progress,
            'tgl' =>   $request->tgl,
        ]);

        return back()->with('success', ' Data telah diperbaharui!');
    }

    //Function untuk menghapus perbaikan
    public function delete($id)
    {
        $perbaikan = perbaikan::find($id);
        $perbaikan->delete();
        return redirect('/perbaikan')->with('status', 'Data perbaikan Berhasil Dihapus!');
    }


    public function trash()
    {
        // mengambil data perbaikan yang sudah dihapus
        $perbaikan = perbaikan::onlyTrashed()->get();
        return view('admin.perbaikan-trash', ['perbaikan' => $perbaikan]);
    }

    // restore data perbaikan yang dihapus
    public function kembalikan($id)
    {
        $perbaikan = perbaikan::onlyTrashed()->where('id', $id);
        $perbaikan->restore();
        return redirect('/perbaikan')->with('status', 'Data perbaikan Berhasil Dikembalikan!');
    }

    // restore semua data perbaikan yang sudah dihapus
    public function kembalikan_semua()
    {

        $perbaikan = perbaikan::onlyTrashed();
        $perbaikan->restore();

        return redirect('/perbaikan')->with('status', 'Data perbaikan Berhasil Dikembalikan!');
    }

    // hapus permanen
    public function hapus_permanen($id)
    {
        // hapus permanen data perbaikan
        $perbaikan = perbaikan::onlyTrashed()->where('id', $id);
        $perbaikan->forceDelete();

        return redirect('/perbaikan/trash')->with('status', 'Data perbaikan Berhasil Dihapus!');
    }

    // hapus permanen semua perbaikan yang sudah dihapus
    public function hapus_permanen_semua()
    {
        // hapus permanen semua data perbaikan yang sudah dihapus
        $perbaikan = perbaikan::onlyTrashed();
        $perbaikan->forceDelete();

        return redirect('/perbaikan/trash')->with('status', 'Data perbaikan Berhasil Dihapus!');
    }


    public function cetakNota(Request $request, $id)
    {
        $perbaikan = DB::table('perbaikan')->join('member', 'member.id_member', 'perbaikan.nama_customer')
            ->where("perbaikan.id", $id)->get();
        $setting    = Setting::first();

        // dd($perbaikan);

        $pdf = PDF::loadView('perbaikan.cetak', compact('perbaikan', 'setting'));
        $pdf->setPaper(array(0, 0, 566.93, 850.39), 'potrait');
        return $pdf->stream('perbaikan.pdf');
    }
}
