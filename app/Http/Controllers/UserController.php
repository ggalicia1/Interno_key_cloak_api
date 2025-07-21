<?php

namespace App\Http\Controllers;

use App\Contracts\User\IUser;
use App\Http\Requests\User\RetrieveGroupRequest;
use App\Http\Requests\User\RetrieveRealmRoles;
use App\Http\Requests\User\SearchUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserByIdRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserCredentialRequest;
use App\Http\Requests\User\UserRequest;
use App\Response\Response;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $user_repository;
    public function __construct(IUser $user_repository) {
        $this->user_repository = $user_repository;
    }
    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="Listar usuarios de un reino.",
     *     tags={"Users"},
     *     security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Nombre del Reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         description="Tamaño del paginado",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numero de Pagina.",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function users(UserRequest $request)
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->user_repository->users($data);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/users/user-by-id",
     *     summary="Obtiene un usuario por id del reino.",
     *     tags={"Users"},
     *     security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Id del reina",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Id del usuario",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function userById(UserByIdRequest $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->user_repository->userById($data);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/users",
     *      tags={"Users"},
     *      summary="Crear un usuario en Keycloak",
     *      description="Endpoint para crear un usuario en Keycloak con los datos proporcionados.",
     *      security={{"bearer_token":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              required={"realm","username", "email", "first_Name", "last_name", "enabled"},
     *              @OA\Property(property="realm", type="string", example="Interno"),
     *              @OA\Property(property="username", type="string", example="juanperez"),
     *              @OA\Property(property="email", type="string", example="juan.perez@example.com"),
     *              @OA\Property(property="email_verified", type="boolean", example=true),
     *              @OA\Property(property="first_name", type="string", example="Juan"),
     *              @OA\Property(property="last_name", type="string", example="Perez"),
     *              @OA\Property(property="enabled", type="boolean", example=true),
     *              @OA\Property(property="credentials", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="type", type="string", example="password"),
     *                      @OA\Property(property="value", type="string", example="MiContraSegura123"),
     *                      @OA\Property(property="temporary", type="boolean", example=false)
     *                  )
     *              ),
     *              @OA\Property(property="attributes", type="object"),
     *              @OA\Property(property="groups", type="object"),
     *              @OA\Property(
     *                  property="requiredActions",
     *                  type="array",
     *                  @OA\Items(type="string"),
     *                  example={"VERIFY_EMAIL", "VERIFY_PROFILE", "UPDATE_PROFILE", "UPDATE_PASSWORD", "TERMS_AND_CONDITIONS", "CONFIGURE_TOTP"}
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Usuario creado correctamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *              @OA\Property(property="username", type="string", example="juanperez"),
     *              @OA\Property(property="email", type="string", example="juan.perez@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error en los datos enviados",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Datos inválidos")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error interno del servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Error interno")
     *          )
     *      )
     *  )
     */
    public function createUser(UserCreateRequest $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->user_repository->create($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);

    }
    /**
     * @OA\Put(
     *      path="/api/v1/users/{realm}/{user_id}",
     *      tags={"Users"},
     *      summary="Actualizar un usuario en Keycloak",
     *      description="Endpoint para Actualiar informacion del usuario en Keycloak.",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="path",
     *         description="Id del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="Id del usuario",
     *         required=true,
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string", example="juan.perez@example.com"),
     *              @OA\Property(property="first_name", type="string", example="Juan"),
     *              @OA\Property(property="last_name", type="string", example="Perez"),
     *              @OA\Property(property="email_verified", type="boolean", example=true),
     *              @OA\Property(property="enabled", type="boolean", example=true),

     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Usuario creado correctamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *              @OA\Property(property="username", type="string", example="juanperez"),
     *              @OA\Property(property="email", type="string", example="juan.perez@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error en los datos enviados",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Datos inválidos")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error interno del servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Error interno")
     *          )
     *      )
     *  )
     */
    public function update(string $realm, string $user_id, UpdateUserRequest $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->user_repository->update($realm, $user_id, $data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);

    }
    /**
     * @OA\Put(
     *      path="/api/v1/users/enable-disable",
     *      tags={"Users"},
     *      summary="Activar y desactivar un usuario.",
     *      description="Endpoint para habilitar y deshabilitar usuario del keycloak.",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Id del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Id del usuario",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="True de activar y false para desactivar un usuario",
     *         required=true,
     *         example=false
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Usuario creado correctamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *              @OA\Property(property="username", type="string", example="juanperez"),
     *              @OA\Property(property="email", type="string", example="juan.perez@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error en los datos enviados",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Datos inválidos")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error interno del servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Error interno")
     *          )
     *      )
     *  )
     */
    public function enableDisable(UserByIdRequest $data) : JsonResponse
    {

        $realm = $data['realm'];
        $user_id = $data['user_id'];
        if($data['enabled'] == 'true'){
            $data = ['enabled' => true];
        }else if($data['enabled'] == 'false'){
            $data = ['enabled' => false];
        }else{
            return Response::error(false, 'Debe de enviar un valor de tipo bool', 422);
        }
        list($status, $message, $response, $code) = $this->user_repository->update($realm, $user_id, $data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);

    }
    /**
     * @OA\Post(
     *      path="/api/v1/users/credentials/reset-password",
     *      tags={"Users"},
     *      summary="Crear la contraseña un usuario en Keycloak",
     *      description="Endpoint para crear un usuario en Keycloak con los datos proporcionados.",
     *      security={{"bearer_token":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              required={"realm","user_id", "credentials"},
     *              @OA\Property(property="realm", type="string", example="Interno"),
     *              @OA\Property(property="user_id", type="string", example="5faaccc7-5671-4041-88e3-9fc8d89db5e1"),
     *              @OA\Property(property="credentials", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="type", type="string", example="password"),
     *                      @OA\Property(property="value", type="string", example="MiContraSegura123"),
     *                      @OA\Property(property="temporary", type="boolean", example=false)
     *                  )
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Usuario creado correctamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *              @OA\Property(property="username", type="string", example="juanperez"),
     *              @OA\Property(property="email", type="string", example="juan.perez@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error en los datos enviados",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Datos inválidos")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error interno del servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Error interno")
     *          )
     *      )
     *  )
     */
    public function resetPassword(UserCredentialRequest $request) : JsonResponse
    {
        $data = $request->validated();
        $user_id = $data['user_id'];
        unset($data['user_id']);
        list($status, $message, $response, $code) = $this->user_repository->userCredential($user_id, $data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);

    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/search",
     *     summary="Buscar usuario por nombre usuarios, nombres, apellidos o correo electronico en un reino.",
     *     tags={"Users"},
     *     security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Nombre del Reino",
     *         required=true,
     *         example="Interno",
     *      ),
     *      @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         description="Tamaño del paginado",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numero de Pagina.",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Parametro de busqueda, puede ser username, first o last name o email",
     *         required=true,
     *         example="ggalicia",
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function search(SearchUserRequest $request) : JsonResponse
    {

        $data = $request->validated();
        list($status, $message, $response, $code) = $this->user_repository->search($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);

    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/retrieve-realm-roles",
     *     summary="Recupera roles del usuario",
     *     tags={"Users"},
     *     security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Nombre del Reino",
     *         required=true,
     *         example="Interno",
     *      ),
     *      @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Id del usuario",
     *         required=true,
     *         example="7677e406-9404-49f1-bddf-ab15182538ec",
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function retrieveRealmRoles(RetrieveRealmRoles $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->user_repository->retrieveRealmRoles($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

    /* public function roleAssign() : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->user_repository->search($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);

    } */

    /**
     * @OA\Put(
     *      path="/api/v1/users/join-group/{realm}/{user_id}/{group_id}",
     *      tags={"Users"},
     *      summary="Agregar usuario a un grupo.",
     *      description="Endpoint para agregar un asuario a un grupo.",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="path",
     *         description="Id del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="Id del usuario",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="group_id",
     *         in="path",
     *         description="Id del usuario",
     *         required=true,
     *      ),

     *      @OA\Response(
     *          response=201,
     *          description="Usuario creado correctamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *              @OA\Property(property="username", type="string", example="juanperez"),
     *              @OA\Property(property="email", type="string", example="juan.perez@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error en los datos enviados",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Datos inválidos")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error interno del servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Error interno")
     *          )
     *      )
     *  )
     */
    public function joinGroup(string $realm, string $user_id, string $group_id) : JsonResponse
    {

        list($status, $message, $response, $code) = $this->user_repository->joinGroup($realm, $user_id, $group_id);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }
    /**
     * @OA\Delete(
     *      path="/api/v1/users/leave-group/{realm}/{user_id}/{group_id}",
     *      tags={"Users"},
     *      summary="Eliminar usuario a un grupo.",
     *      description="Endpoint para Eliminar un asuario a un grupo.",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="path",
     *         description="Id del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="Id del usuario",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="group_id",
     *         in="path",
     *         description="Id del usuario",
     *         required=true,
     *      ),

     *      @OA\Response(
     *          response=201,
     *          description="Usuario creado correctamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *              @OA\Property(property="username", type="string", example="juanperez"),
     *              @OA\Property(property="email", type="string", example="juan.perez@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error en los datos enviados",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Datos inválidos")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error interno del servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Error interno")
     *          )
     *      )
     *  )
     */
    public function leaveGroup(string $realm, string $user_id, string $group_id) : JsonResponse
    {

        list($status, $message, $response, $code) = $this->user_repository->leaveGroup($realm, $user_id, $group_id);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/retrieve-groups",
     *     summary="Recupera reupos donde esta el usuario",
     *     tags={"Users"},
     *     security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Nombre del Reino",
     *         required=true,
     *         example="Interno",
     *      ),
     *      @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Id del usuario",
     *         required=true,
     *         example="7677e406-9404-49f1-bddf-ab15182538ec",
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function retrieveGroups(RetrieveGroupRequest $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->user_repository->retrieveGroups($data['realm'], $data['user_id'], []);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

}
