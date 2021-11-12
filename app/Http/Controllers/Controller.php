<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function getValidatorResponse(Validator $validator)
    {
        return new JsonResponse($validator->errors()->all(), 417);
    }
}
