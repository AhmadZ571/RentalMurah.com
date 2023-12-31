<?php

namespace App\Http\Controllers\Admin;

use App\Models\Car;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\CarStoreRequest;
use App\Http\Requests\Admin\CarUpdateRequest;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cars = Car::latest()->get();

        return view('admin.cars.index', compact('cars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cars.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CarStoreRequest $request)
    {
        if($request->validated()) {
            $gambar = $request->file('gambar')->store('assets/car', 'public');
            $slug = Str::slug($request->nama_mobil, '-');

            Car::create($request->except('gambar') + ['gambar' => $gambar, 'slug' => $slug]);
        }

        return redirect()->route('admin.cars.index')->with([
            'message' => 'data sukses dibuat',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car)
    {
        return view('admin.cars.edit', compact('car'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CarUpdateRequest $request, car $car)
    {
        if($request->validated()) {
            $slug = Str::slug($request->nama_mobil, '-');
            $car->update($request->validated() + ['slug' => $slug]);
        }

        return redirect()->route('admin.cars.index')->with([
            'message' => 'data berhasil diedit',
            'alert-type' => 'info'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        if($car->gambar) {
            Storage::disk('public')->delete($car->gambar);
        }
        $car->delete();

        return redirect()->back()->with([
            'message' => 'data berhasil dihapus',
            'alert-type' => 'danger'
        ]);
    }

    public function updateImage(Request $request, $carId)
    {
        $request->validate([
            'gambar' => 'required|image',
            'nama_mobil' => 'required|string'
        ]);
        $car = Car::findOrFail($carId);
        if($request->hasFile('gambar')) {
            unlink('storage/' . $car->gambar);
            $gambar = $request->file('gambar')->store('assets/car', 'public');
            $car->update(['gambar' => $gambar]);
            $car->save();
        }
        return redirect()->back()->with([
            'message' => 'gambar berhasil diedit',
            'alert-type' => 'info'
        ]);
    }
}
