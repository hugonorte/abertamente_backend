<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\BibliographicReference;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $users = User::paginate(15);

        return UserResource::collection($users);
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
    public function store(UserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);

        try {
            $user = User::create($validatedData);

            return (new UserResource($user))
                ->response()
                ->setStatusCode(201);

        } catch (Exception) {
            return response()->json(['message' => 'Erro ao criar o usuário'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
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
    public function update(UserRequest $request, User $user): UserResource
    {
        $validatedData = $request->validated();

        // Lógica para só atualizar a senha se ela foi enviada
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']); // Remove do array para não salvar ""
        }

        $user->update($validatedData);

        return new UserResource($user->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
