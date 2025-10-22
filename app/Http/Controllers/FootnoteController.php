<?php

namespace App\Http\Controllers;

use App\Http\Requests\FootnoteRequest;
use App\Models\BibliographicReference;
use App\Models\Footnote;
use Illuminate\Http\JsonResponse;

class FootnoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $footnotes = Footnote::all();

        return response()->json($footnotes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FootnoteRequest $request): JsonResponse
    {
        $footnote = new Footnote();
        $footnote->post_id = $request->get('post_id');
        $footnote->description = $request->get('description');

        if($footnote->save()){
            return response()->json($footnote, 201);
        }

        return response()->json(['message' => 'Erro ao criar a referência bibliográfica'], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Footnote $footnote): JsonResponse
    {
        return response()->json($footnote);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BibliographicReference $bibliographicReference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FootnoteRequest $request, string $id): JsonResponse
    {
        $footnote = Footnote::findOrFail($id);

        $footnote->update([
            'post_id' => $request->get('post_id'),
            'description' => $request->get('description'),
        ]);

        return response()->json($footnote);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Footnote $footnote): JsonResponse
    {
        $footnote->delete();

        return response()->json(['message' => 'Footnote excluído com sucesso']);
    }
}
