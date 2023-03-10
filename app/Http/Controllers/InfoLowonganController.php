<?php

namespace App\Http\Controllers;

use File;
use Carbon;
use App\Preset;
use DataTables;
use App\Instansi;
use App\datalowongan;
use App\InfoLowongan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InfoLowonganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth','admin']);
    }

    public function index()
    {
        $preset = preset::where('status','active')->first();
        return view('admin.infoLowongan',compact('preset'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function json()
    {
        return Datatables::of(datalowongan::all())->make(true);
    }

    public function active($id)
    {
        InfoLowongan::where('id',$id)->update(['status' => 'Aktif']);
        return response()->json();
    }

    public function deactive($id)
    {
        InfoLowongan::where('id',$id)->update(['status' => 'Tidak Aktif']);
        return response()->json();
    }

    public function detail($id)
    {
        $data = datalowongan::where('id',$id)->get();

        return response()->json($data);
    }
    
    public function create()
    {
        $preset = preset::where('status','active')->first();
        $instansi = Instansi::all();
        return view('admin.inputInfoLowongan',compact('instansi','preset'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path = public_path().'/image/InfoLowongan/';
        File::makeDirectory($path, $mode = 0777, true, true);
        $file = $request->file('foto');
        $nama_file = $request->judul."_".$file->getClientOriginalName();
        $file->move($path,$nama_file);

        InfoLowongan::create([
    		'judul' => $request->judul,
            'instansi_id' => $request->instansi_id,
            'isi' => $request->isi,
            'foto' => $nama_file,
            'expired' => $request->expired,
            'status' => 'Aktif'
        ]);

        return redirect('/infolowongan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InfoLowongan  $infoLowongan
     * @return \Illuminate\Http\Response
     */
    public function show(InfoLowongan $infoLowongan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InfoLowongan  $infoLowongan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $preset = preset::where('status','active')->first();
        $data = datalowongan::where('id',$id)->get();
        $instansi = Instansi::all();
        return view('admin.editInfoLowongan',compact('preset','data','instansi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InfoLowongan  $infoLowongan
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $lowongan = InfoLowongan::find($id);
        $lowongan->judul = $request->judul;
        $lowongan->instansi_id = $request->instansi_id;
        $lowongan->isi = $request->isi;
        $lowongan->expired = $request->expired;
        if ($request->hasFile('foto')) {
            $gambar = InfoLowongan::where('id',$id)->first();
	        File::delete('image/InfoLowongan/'.$gambar->foto);
            $path = public_path().'/image/InfoLowongan/';
            File::makeDirectory($path, $mode = 0777, true, true);
            $file = $request->file('foto');
            $nama_file = $request->judul."_".$file->getClientOriginalName();
            $file->move($path,$nama_file);
            $lowongan->foto = $nama_file;
        }
        $lowongan->save();

        return redirect('/infolowongan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InfoLowongan  $infoLowongan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gambar = InfoLowongan::where('id',$id)->first();
	    File::delete('image/InfoLowongan/'.$gambar->foto);

        InfoLowongan::find($id)->delete();
        return response()->json();
    }
}
