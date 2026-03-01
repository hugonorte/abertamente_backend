<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

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

    public function postSummary(): JsonResponse
    {
        $posts = Post::leftJoin('categories', 'posts.category_id', '=', 'categories.id')
            ->leftJoin('authors', 'posts.author_id', '=', 'authors.id')
            ->select(
                'posts.id',
                'posts.category_id',
                'categories.name as category_name',
                'posts.author_id',
                'authors.name as author_name',
                'posts.created_at',
                'posts.updated_at',
                'posts.status',
                'posts.title'
            )
            ->get();

        return response()->json($posts);
    }

    public function publishedPostContent($id): JsonResponse
    {
        $posts = Post::leftJoin('categories', 'posts.category_id', '=', 'categories.id')
            ->leftJoin('authors', 'posts.author_id', '=', 'authors.id')
            ->where('posts.status', '=', 'published')
            ->where('posts.id', '=', $id)
            ->select(
                'posts.id',
                'posts.category_id',
                'categories.name as category_name',
                'posts.author_id',
                'authors.name as author_name',
                'authors.main_title as author_main_title',
                'authors.preferred_social_network as author_preferred_social_network',
                'authors.preferred_social_network_username as author_preferred_social_network_username',
                'authors.bio as author_bio',
                'posts.created_at',
                'posts.updated_at',
                'posts.status',
                'posts.title',
                'posts.content',
                'posts.tldr',
                'posts.image_path',
                'posts.published_at'
            )
            ->firstOrFail();

        return response()->json($posts);
    }

    public function publishedPostList(): JsonResponse
    {
        $posts = Post::leftJoin('categories', 'posts.category_id', '=', 'categories.id')
            ->leftJoin('authors', 'posts.author_id', '=', 'authors.id')
            ->where('posts.status', '=', 'published')
            ->select(
                'posts.id',
                'posts.category_id',
                'categories.name as category_name',
                'posts.author_id',
                'authors.name as author_name',
                'posts.created_at',
                'posts.updated_at',
                'posts.status',
                'posts.title',
                'posts.image_path',
                'posts.published_at',
                'posts.tldr',
                'posts.slug',
            )
            ->get();

        return response()->json($posts);
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
        $post->slug = Str::of($request->get('title'))->slug('-');
        $post->tldr = $request->get('tldr');
        $post->content = $request->get('content');
        $post->author_id = $request->get('author_id');
        $post->category_id = $request->get('category_id');
        $post->published_at = $request->get('published_at');
        $post->status = $request->get('status');

        if ($request->hasFile('image_path')) {
                $file = $request->file('image_path');

                $filename = time() . '_' . $file->getClientOriginalName();

                $path = $file->storeAs('posts', $filename, 'public');

                $post->image_path = $path;
        }

        if($post->save()){
            return response()->json($post, 201);
        }

        return response()->json(['message' => 'Erro ao criar autor'], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): Post
    {
        return $post;
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
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('posts', $filename, 'public');
            $data['image_path'] = $path;
        } else {
            // Keep existing image if no new one is uploaded
            unset($data['image_path']);
        }

        $post->update($data);

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
