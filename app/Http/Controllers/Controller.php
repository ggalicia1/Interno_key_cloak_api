<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Keycloak Middleware",
 *     description="Documentación Swagger de la API intermedia entre Laravel y Keycloak"
 *      ),
 *     @OA\Tag(
 *          name="Realm",
 *          description="Operaciones relacionados con el reino de Keycloak."
 *      ),
 *     @OA\Tag(
 *          name="Realm Roles",
 *          description="Operaciones relacionados con los roles a nivel de reino de Keycloak."
 *      ),
 *     @OA\Tag(
 *          name="Clients",
 *          description="Operaciones relacionados con los clientes del reino de Keycloak."
 *      ),
 *     @OA\Tag(
 *          name="Client Roles",
 *          description="Operaciones relacionados con los roles a nivel de cliente de Keycloak."
 *      ),
 *     @OA\Tag(
 *          name="User Role",
 *          description="Operaciones relacionados con el reino de Keycloak."
 *      ),
 *     @OA\Tag(
 *          name="Users",
 *          description="Operaciones relacionados con los usuarios del reino de Keycloak."
 *      ),
 */
abstract class Controller
{
    public static function snake_to_camel(string $input): string
    {
        $words = explode('_', $input);
        $camel_case = '';
        foreach ($words as $key => $word) {
            if ($key === 0) {
                $camel_case .= strtolower($word);
            } else {
                $camel_case .= ucfirst(strtolower($word));
            }
        }
        return $camel_case;
    }

    public static function camel_case_to_snake_case(string $input): string
    {
        $pattern = '/(?<=\w)([A-Z])/';
        $replacement = '_$1';
        $snake_case = strtolower(preg_replace($pattern, $replacement, $input));
        return $snake_case;
    }
}
