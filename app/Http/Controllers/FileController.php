<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = File::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('original_name', 'like', "%{$search}%");
        }

        $files = $query->paginate(50);
        $files->getCollection()->transform(function($file) {
            $file->url = Storage::url($file->path);
            return $file;
        });

        return Inertia::render('File/Index', [
            'files' => $files,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return Inertia::render('File/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,gif,pdf,txt|max:8192',
            'name' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');

        if (!$file) {
            return redirect()->back()->with('error', 'Фото не загруженно');
        }

        $path = $file->store('public/file');

        if (!$path) {
            return redirect()->back()->with('error', 'Фото не сохраненно');
        }

        File::create([
            'name' => $request->input('name'),
            'original_name' => $request->file('file')->getClientOriginalName(),
            'size' => $request->file('file')->getSize(),
            'extension' => $request->file('file')->getClientOriginalExtension(),
            'path' => $path,
        ]);

        return redirect()->route('files.index');
    }

    public function edit($id)
    {
        $file = File::findOrFail($id);

        return Inertia::render('File/Edit', [
            'file' => $file
        ]);
    }

    public function update(Request $request, $id)
    {
        $file = File::findOrFail($id);

        $request->validate([
            'file' => 'nullable|mimes:jpg,jpeg,png,gif,pdf,txt|max:8192',
            'name' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('file')) {

            $old_path = $file->path;

            $path = $request->file('file')->store('files');
            $file->update([
                'path' => $path,
                'size' => $request->file('file')->getSize(),
                'extension' => $request->file('file')->getClientOriginalExtension(),
                'original_name' => $request->file('file')->getClientOriginalName(),
            ]);

            if ($old_path) {
                Storage::delete($old_path);
            }
        }

        $file->update([
            'name' => $request->input('name', $file->name),
        ]);

        return redirect()->route('files.index');
    }

    public function destroy($id)
    {
        $file = File::findOrFail($id);
        Storage::delete($file->path);
        $file->delete();

        return redirect()->route('files.index');
    }
}
