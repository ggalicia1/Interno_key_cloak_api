<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Keycloak Middleware",
 *     description="Documentación Swagger de la API intermedia entre Laravel y Keycloak"
 * )
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
}
