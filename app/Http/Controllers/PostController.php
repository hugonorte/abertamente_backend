<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $post = Post::all();

        return response()->json($post);
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
    public function store(PostRequest $request): JsonResponse
    {

        $post = new Post();
        $post->title = $request->get('title');
        $post->tldr = $request->get('tldr');
        $post->content = $request->get('content');
        $post->image_path = $request->get('image_path');
        $post->author_id = $request->get('author_id');
        $post->category_id = $request->get('category_id');
        $post->published_at = $request->get('published_at');
        $post->status = $request->get('status');

        if($post->save()){
            return response()->json($post, 201);
        }

        return response()->json(['message' => 'Erro ao criar autor'], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        return response()->json($post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, string $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        $post->update([
            'title' => $request->get('title'),
            'tldr' => $request->get('tldr'),
            'content' => $request->get('content'),
            'image_path' => $request->get('image_path'),
            'author_id' => $request->get('author_id'),
            'category_id' => $request->get('category_id'),
            'published_at' => $request->get('published_at'),
            'status' => $request->get('status'),
        ]);

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(['message' => 'Post excluído com sucesso']);
    }
}
