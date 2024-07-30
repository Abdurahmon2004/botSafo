<?php
namespace App\Http\Controllers;

use App\Models\Tarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::all();
        return view('back.tarifs.index', compact('tarifs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|string',
            'date' => 'required|string',
            'text' => 'required|string',
            'items' => 'required|array',
            'items.*' => 'required|string',
        ]);

        Tarif::create([
            'name' => $request->name,
            'price' => $request->price,
            'date' => $request->date,
            'text' => $request->text,
            'items' => json_encode($request->items),
        ]);

        return response()->json(['success' => 'Tarif added successfully']);
    }

    public function update(Request $request, Tarif $tarif)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|string',
            'date' => 'required|string',
            'text' => 'required|string',
            'items' => 'required|array',
            'items.*' => 'required|string',
        ]);

        $tarif->update([
            'name' => $request->name,
            'price' => $request->price,
            'date' => $request->date,
            'text' => $request->text,
            'items' => json_encode($request->items),
        ]);

        return response()->json(['success' => 'Tarif updated successfully']);
    }

    public function destroy(Tarif $tarif)
    {
        $tarif->delete();
        return response()->json(['success' => 'Tarif deleted successfully']);
    }
}
