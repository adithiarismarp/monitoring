<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Perbaikan;
use Illuminate\Http\Request;


class LandingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('landingpage.index');
    }
    public function search(Request $request)
    {
        $keyword = $request->kode;
        $hasiltrack = Perbaikan::join('member', 'perbaikan.nama_customer', 'member.id_member')->join('history', 'history.id_perbaikan',  'perbaikan.id')->where('kode_unik',  $keyword)->get(['perbaikan.*', 'history.*', 'id_member', 'kode_member', 'nama', 'alamat', 'telepon'])->unique();

        $perbaikan = Perbaikan::join('history', 'history.id_perbaikan',  'perbaikan.id')->where('kode_unik',  $keyword)->first(['id_perbaikan']);

        $history = History::where('history.id_perbaikan', $perbaikan->id_perbaikan)->get();

        // dd($hasiltrack);
        // dd($history);

        // dd($perbaikan);
        return view('tracking.index', compact('hasiltrack', 'history'));
    }

    public function checkout(Request $request, $id)
    {

        $keyword = $request->kode;

        $perbaikan = perbaikan::find($id);

        return view('tracking.checkout', compact('perbaikan'));
    }
}
