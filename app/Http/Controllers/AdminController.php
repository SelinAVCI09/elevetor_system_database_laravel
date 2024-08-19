<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Admin ana sayfası
    public function index()
    {
        $information = DB::table('information')->get();
        $works = DB::table('works')->get();
        return view('admin_home', compact('information', 'works'));
    }

    // Bilgi ekleme formu
    public function createInformation()
    {
        return view('add_information');
    }

    // Bilgi ekleme işlemi
    public function storeInformation(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|integer',
            'tel_number1' => 'required|string',
            'tel_number2' => 'nullable|string',
            'address' => 'required|string',
            'mail' => 'required|email',
        ]);

        DB::table('information')->insert([
            'admin_id' => $request->input('admin_id'),
            'tel_number1' => $request->input('tel_number1'),
            'tel_number2' => $request->input('tel_number2'),
            'address' => $request->input('address'),
            'mail' => $request->input('mail'),
        ]);

        return redirect()->route('admin_home')->with('success', 'Bilgi başarıyla eklendi.');
    }

    // Bilgi düzenleme formu
    public function editInformation($id)
    {
        $information = DB::table('information')->where('id', $id)->first();
        return view('edit_information', compact('information'));
    }

    // Bilgi güncelleme işlemi
    public function updateInformation(Request $request, $id)
    {
        $request->validate([
            'admin_id' => 'required|integer',
            'tel_number1' => 'required|string',
            'tel_number2' => 'nullable|string',
            'address' => 'required|string',
            'mail' => 'required|email',
        ]);

        DB::table('information')->where('id', $id)->update([
            'admin_id' => $request->input('admin_id'),
            'tel_number1' => $request->input('tel_number1'),
            'tel_number2' => $request->input('tel_number2'),
            'address' => $request->input('address'),
            'mail' => $request->input('mail'),
        ]);

        return redirect()->route('admin_home')->with('success', 'Bilgi başarıyla güncellendi.');
    }

    // Bilgi silme işlemi
    public function deleteInformation(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        DB::table('information')->where('id', $request->input('id'))->delete();
        return redirect()->route('admin_home')->with('success', 'Bilgi başarıyla silindi.');
    }

    // İş ekleme formu
    public function createWorks()
    {
        return view('add_works');
    }

    // İş ekleme işlemi
    public function storeWorks(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|integer',
            'text' => 'required|string',
            'label' => 'required|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $photoUrls = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPath = $photo->store('uploads', 'public');
                $photoUrls[] = $photoPath;
            }
        }

        DB::table('works')->insert([
            'admin_id' => $request->input('admin_id'),
            'text' => $request->input('text'),
            'label' => $request->input('label'),
            'photo_urls' => json_encode($photoUrls),
        ]);

        return redirect()->route('admin_home')->with('success', 'İş başarıyla eklendi.');
    }

    // İş düzenleme formu
    public function editWorks($id)
    {
        $work = DB::table('works')->where('id', $id)->first();
        return view('edit_works', compact('work'));
    }

    // İş güncelleme işlemi
    public function updateWorks(Request $request, $id)
    {
        $request->validate([
            'admin_id' => 'required|integer',
            'text' => 'required|string',
            'label' => 'required|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $work = DB::table('works')->where('id', $id)->first();
        $photoUrls = json_decode($work->photo_urls, true) ?? [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPath = $photo->store('uploads', 'public');
                $photoUrls[] = $photoPath;
            }
        }

        DB::table('works')->where('id', $id)->update([
            'admin_id' => $request->input('admin_id'),
            'text' => $request->input('text'),
            'label' => $request->input('label'),
            'photo_urls' => json_encode($photoUrls),
        ]);

        return redirect()->route('admin_home')->with('success', 'İş başarıyla güncellendi.');
    }

    // İş silme işlemi
    public function deleteWorks(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $work = DB::table('works')->where('id', $request->input('id'))->first();
        $photoUrls = json_decode($work->photo_urls, true) ?? [];

        if (is_array($photoUrls)) {
            foreach ($photoUrls as $photoUrl) {
                Storage::disk('public')->delete($photoUrl);
            }
        }

        DB::table('works')->where('id', $request->input('id'))->delete();
        return redirect()->route('admin_home')->with('success', 'İş başarıyla silindi.');
    }
}
