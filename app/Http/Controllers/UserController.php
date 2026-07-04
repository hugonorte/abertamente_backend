<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\BibliographicReference;
use App\Models\User;
use App\Enums\UserRole;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Aplicar middleware de autorização via Policy no construtor.
     * ESTA É A FORMA EXPLÍCITA E CORRETA NO LARAVEL 12.
     */
    public function __construct()
    {
        /**
         * Isso mapeia manualmente os métodos da Policy para os métodos do Controller.
         * 'can' refere-se ao middleware de autorização do Laravel.
         *
         * O parâmetro 'user' (minúsculo) refere-se ao parâmetro da rota: /api/user/{user}
         */

        // Mapeia a policy 'viewAny' (ver lista) para o método 'index'
        $this->middleware('can:viewAny,' . User::class)->only('index');

        // Mapeia a policy 'view' (ver um) para o método 'show'
        $this->middleware('can:view,user')->only('show');

        // Mapeia 'create' para 'store'
        $this->middleware('can:create,' . User::class)->only('store');

        // Mapeia 'update' para 'update'
        $this->middleware('can:update,user')->only('update');

        // Mapeia 'delete' para 'destroy'
        $this->middleware('can:delete,user')->only('destroy');
    }

    /**
     * Retorna todas as roles disponíveis.
     */
    public function roles(): JsonResponse
    {
        $roles = collect(UserRole::cases())->map(function ($role) {
            return [
                'value' => $role->value,
                'label' => $role->label(),
            ];
        });

        return response()->json($roles);
    }

    /**
     * Display a listing of the resource.
     *  (Protegido pelo método 'viewAny' da UserPolicy)
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
     *  (Protegido pelo método 'create' da UserPolicy)
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
     * (Protegido pelo método 'view' da UserPolicy)
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
     * (Protegido pelo método 'update' da UserPolicy)
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
     * (Protegido pelo método 'delete' da UserPolicy)
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
