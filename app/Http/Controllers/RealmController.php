<?php

namespace App\Http\Controllers;

use App\Contracts\Realm\IRealm;
use App\Http\Requests\Realm\RealmKeyRequest;
use App\Response\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RealmController extends Controller
{
    protected IRealm $realm_repository;
    public function __construct(IRealm $realm_repository)
    {
        $this->realm_repository= $realm_repository;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/realms",
     *     summary="Listar reinos de keycloak",
     *     tags={"Realm"},
     *      security={{"bearer_token":{}}},
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
    public function realms(Request $request) : JsonResponse
    {
        list($status, $message, $response, $code) = $this->realm_repository->all();
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/realms/public_key",
     *     summary="Obtiene reino por nombre",
     *     tags={"Realm"},
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Nombre del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="algorithm",
     *         in="query",
     *         description="Algoritmot",
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
    public function keys(RealmKeyRequest $request)
    {
        $data = $request->validated($request);
        $data['realm'] = $request->realm ? $request->realm : null;
        $data['algorithm'] = $request->algorithm ? $request->algorithm : null;
        if($data['algorithm'] == null || $data['realm'] == null){
            return Response::error(false, 'Parametros invalidos', 422);
        }
        list($status, $message, $response, $code) = $this->realm_repository->key($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }
}
