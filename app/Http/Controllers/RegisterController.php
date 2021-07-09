<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
  public function register(Request $request, UserService $userService): JsonResponse
  {
    /** @var ValidationValidator */
    $validator = Validator::make(
      $request->all(),
      [
        'name' => 'required|string',
        'email' => 'required|email',
        'password' => 'required|string|gte:5'
      ],
      [
        'required' => ':attribute_required',
        'string' => ':attribute_must_be_a_string',
        'email' => ':attribute_must_be_an_email',
        'gte' => ':attribute_is_to_short',
      ]
    );

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()], 417);
    }

    $name = $request->input('name');
    $email = $request->input('email');
    $password = $request->input('password');

    $user = $userService->create($name, $email, $password);
    return response()->json();
  }
}
