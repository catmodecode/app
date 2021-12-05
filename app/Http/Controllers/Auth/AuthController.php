<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\User\TokenNotFoundException;
use App\Exceptions\User\WrongLoginOrPasswordException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\JwtService;
use Exception;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        private JwtService $jwtService
    ) {
    }

    public function login(Request $request, UserRepository $userRepository)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ], [
            'required' => ':attribute_required',
            'email' => ':attribute_must_be_email',
            'exists' => 'wrong_user_or_password',
        ]);

        if ($validator->fails()) {
            $failResponse = $this->getValidatorResponse($validator);
            if ($validator->errors()->has('exists')) {
                $failResponse->setStatusCode(404);
            }
            return $failResponse;
        }

        $email = $request->input('email');
        $password = $request->input('password');

        try {
            $user = $userRepository->checkCredintials($email, $password);
        } catch (WrongLoginOrPasswordException) {
            return response()->json(['errors' => ['wrong_user_or_password']], 400);
        };

        try {
            $tokenResult = $this->generateTokens($user);
        } catch (Exception) {
            return new JsonResponse(['errors' => ['token_server_error']], 500);
        }

        return new JsonResponse($tokenResult, 201);
    }

    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'exists:tokens,token'],
        ], [
            'required' => ':attribute_required',
            'exists' => ':attribute_not_found',
        ]);

        if ($validator->fails()) {
            $failResponse = $this->getValidatorResponse($validator);
            if ($validator->errors()->has('exists')) {
                $failResponse->setStatusCode('404');
            }
            return $failResponse;
        }

        $token = $request->input('token');

        $jwtService = $this->jwtService;

        try {
            $token = $jwtService->useToken($token);
        } catch (ExpiredException) {
            return new JsonResponse(['errors' => ['token_expired']], 400);
        } catch (TokenNotFoundException) {
            return new JsonResponse(['errors' => ['token_not_found']], 400);
        } catch (Exception $e) {
            return new JsonResponse(['errors' => ['token_error']], 400);
        };

        $user = $token->user;

        try {
            $tokenResult = $this->generateTokens($user);
        } catch (Exception) {
            return new JsonResponse(['errors' => ['token_server_error']], 500);
        }

        return new JsonResponse($tokenResult, 201);
    }

    /**
     * @param User $user
     * @return Collection
     *
     * @throws InvalidArgumentException — — Provided JWT was empty
     * @throws UnexpectedValueException — — Provided JWT was invalid
     * @throws SignatureInvalidException
     * Provided JWT was invalid because the signature verification failed
     * @throws BeforeValidException
     * Provided JWT is trying to be used before it's eligible as defined by 'nbf'
     * @throws BeforeValidException
     * Provided JWT is trying to be used before it's been created as defined by 'iat'
     * @throws ExpiredException
     * Provided JWT has since expired, as defined by the 'exp' claim
     */
    protected function generateTokens(User $user): Collection
    {
        $jwtService = $this->jwtService;
        $refresh = $jwtService->generateAndStoreRefreshJwt($user);
        $access = $jwtService->generateAccessJwt($user);
        $accessExpired = $jwtService->getExpired($access);

        return collect([
            'refresh' => $refresh->token,
            'access' => $access,
            'access_expired' => $accessExpired,
        ]);
    }
}
