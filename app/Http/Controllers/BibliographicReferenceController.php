<?php

namespace App\Http\Controllers;

use App\Http\Requests\BibliographicReferenceRequest;
use App\Models\BibliographicReference;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class BibliographicReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $bibliographicRef = BibliographicReference::all();

        return response()->json($bibliographicRef);
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
    public function store(BibliographicReferenceRequest $request): JsonResponse
    {
        $bibliographicRef = BibliographicReference::create($request->validated());

        if($bibliographicRef){
            return response()->json($bibliographicRef, 201);
        }

        return response()->json(['message' => 'Erro ao criar a referência bibliográfica'], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(BibliographicReference $bibliographicReference)
    {
        return response()->json($bibliographicReference);
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
    public function update(BibliographicReferenceRequest $request, string $id): JsonResponse
    {
        $bibliographicRef = BibliographicReference::findOrFail($id);

        $bibliographicRef->update($request->validated());

        return response()->json($bibliographicRef);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BibliographicReference $bibliographicReference): JsonResponse
    {
        $bibliographicReference->delete();

        return response()->json(['message' => 'Referência bibliográfica excluída com sucesso']);
    }

    public function showByPostId(Post $post): JsonResponse
    {
        $bibliographicRef = BibliographicReference::where('post_id', $post->id)->get();

        return response()->json($bibliographicRef);
    }
}
