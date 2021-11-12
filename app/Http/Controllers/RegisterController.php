<?php

namespace App\Http\Controllers;

use App\Contracts\UserRepositoryContract;
use App\Exceptions\User\NotPhoneException;
use App\Exceptions\User\PhoneExistsException;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request, UserRepositoryContract $userRepository): JsonResponse
    {
        return new JsonResponse(['data' => 'ok']);
        /** @var ValidationValidator */
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string|gte:5',
                'phone' => 'required|phone'
            ],
            [
                'required' => ':attribute_required',
                'string' => ':attribute_must_be_a_string',
                'email' => ':attribute_must_be_an_email',
                'gte' => ':attribute_is_to_short',
                'phone' => ':attribute_is_not_a_phone_or_already_exists'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 417);
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $phone = $request->input('phone');

        if (!$userRepository->validatePhone($phone) || $userRepository->phoneExists($phone)) {
            return response()->json(['errors' => ['phone_is_not_a_phone_or_already_exists']]);
        }
        $user = $userRepository->create($name, $email, $password, $phone);

        return response()->json($user);
    }
}
