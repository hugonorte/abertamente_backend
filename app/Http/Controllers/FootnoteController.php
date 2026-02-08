<?php

namespace App\Http\Controllers;

use App\Http\Requests\FootnoteRequest;
use App\Models\BibliographicReference;
use App\Models\Footnote;
use App\Models\Post;
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
        $footnote = Footnote::create($request->validated());

        if($footnote){
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

        $footnote->update($request->validated());

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

    public function showByPostId(Post $post): JsonResponse
    {
        $footnotes = Footnote::where('post_id', $post->id)->get();

        return response()->json($footnotes);
    }
}
