<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Models\Author;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : JsonResponse
    {
        $author = Author::all();

        return response()->json($author);
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
    public function store(AuthorRequest $request) : JsonResponse
    {
        $author = new Author();
        $author->name = $request->get('name');
        $author->email = $request->get('email');
        $author->bio = $request->get('bio');
        $author->main_title = $request->get('main_title');
        $author->preferred_social_network = $request->get('preferred_social_network');
        $author->preferred_social_network_username = $request->get('preferred_social_network_username');

        if($author->save()){
            return response()->json($author, 201);
        }

        return response()->json(['message' => 'Erro ao criar autor'], 500);
    }


    /**
     * Display the specified resource.
     */
    public function show(Author $author): JsonResponse
    {
        return response()->json($author);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $author)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AuthorRequest $request, string $id): JsonResponse
    {
        $author = Author::findOrFail($id);

        $author->update([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'bio' => $request->get('bio'),
            'main_title' => $request->get('main_title'),
            'preferred_social_network' => $request->get('preferred_social_network'),
            'preferred_social_network_username' => $request->get('preferred_social_network_username'),
        ]);

        return response()->json($author);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author): JsonResponse
    {
        $author->delete();

        return response()->json(['message' => 'Usuário excluído com sucesso']);
    }


}
