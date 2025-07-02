<?php

namespace App\Http\Controllers;

use App\Contracts\Client\IClient;
use App\Http\Requests\Client\ClientByIdRequest;
use App\Http\Requests\Client\ClientRequest;
use App\Response\Response;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    protected $client_repository;
    public function __construct(IClient $client_repository) {
        $this->client_repository = $client_repository;
    }

    /**
     * @OA\Get(
     *     path="/api/clients",
     *     summary="Listar clientes del reino.",
     *     tags={"Clients"},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Nombre del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limite de registros por lista de usuarios o paginado",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Inicio de la lista de usuarios.",
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
    public function clients(ClientRequest $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_repository->clients($data);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }


    /**
     * @OA\Get(
     *     path="/api/clients/client-by-id",
     *     summary="Obtiene cliente por Id de un reino",
     *     tags={"Clients"},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Id del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="client_id",
     *         in="query",
     *         description="Id del client",
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
    public function clientById(ClientByIdRequest $request)
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_repository->clientById($data);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);

    }

}
