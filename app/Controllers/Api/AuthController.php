<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use App\Models\User;
use App\Libraries\TokenService;

class AuthController extends BaseApiController
{
    protected $userModel;
    protected $tokenService;

    public function __construct()
    {
        $this->userModel = new User();
        $this->tokenService = new TokenService();
        helper(['url', 'form']);
    }

    /**
     * Register new user
     * POST /api/auth/register
     */
    public function register()
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'username'   => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'password'   => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return $this->respondWithValidationError($this->validator->getErrors());
        }

        // Get data from JSON or POST
        $first_name = $this->request->getJSON(true)['first_name'] ?? $this->request->getPost('first_name');
        $last_name = $this->request->getJSON(true)['last_name'] ?? $this->request->getPost('last_name');
        $username = $this->request->getJSON(true)['username'] ?? $this->request->getPost('username');
        $email = $this->request->getJSON(true)['email'] ?? $this->request->getPost('email');
        $password = $this->request->getJSON(true)['password'] ?? $this->request->getPost('password');

        $userData = [
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'username'   => $username,
            'email'      => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'is_active'  => 1,
        ];

        if ($this->userModel->save($userData)) {
            $user = $this->userModel->find($this->userModel->getInsertID());
            unset($user['password_hash']); // إخفاء كلمة المرور

            return $this->respondWithSuccess($user, 'User registered successfully', 201);
        } else {
            return $this->respondWithError('Failed to register user', 500);
        }
    }

    /**
     * User login
     * POST /api/auth/login
     */
    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
            'device_name' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->respondWithValidationError($this->validator->getErrors());
        }

        // Get data from JSON or POST
        $jsonData = $this->request->getJSON(true);
        $email = $jsonData['email'] ?? $this->request->getVar('email');
        $password = $jsonData['password'] ?? $this->request->getVar('password');
        $deviceName = $jsonData['device_name'] ?? $this->request->getVar('device_name') ?? 'Unknown Device';

        $user = $this->userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return $this->respondWithError('Invalid email or password', 401);
        }

        if (!$user['is_active']) {
            return $this->respondWithError('Account is deactivated', 401);
        }

        try {
            // إنشاء token جديد
            $tokenResult = $this->tokenService->createDefaultToken($user['id'], $deviceName);
            
            unset($user['password_hash']); // إخفاء كلمة المرور

            return $this->respondWithSuccess([
                'user' => $user,
                'token' => $tokenResult['token'],
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult['expires_at']
            ], 'Login successful');

        } catch (\Exception $e) {
            log_message('error', 'Token creation failed: ' . $e->getMessage());
            return $this->respondWithError('Login failed, please try again', 500);
        }
    }

    /**
     * User logout
     * POST /api/auth/logout
     */
    public function logout()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        
        if (!$authHeader) {
            return $this->respondWithError('No token provided', 400);
        }

        $token = $this->tokenService->extractTokenFromHeader($authHeader);
        
        if (!$token) {
            return $this->respondWithError('Invalid token format', 400);
        }

        try {
            if ($this->tokenService->revokeToken($token)) {
                return $this->respondWithSuccess(null, 'Logout successful');
            } else {
                return $this->respondWithError('Token not found', 404);
            }
        } catch (\Exception $e) {
            log_message('error', 'Logout failed: ' . $e->getMessage());
            return $this->respondWithError('Logout failed', 500);
        }
    }

    /**
     * Get current user profile
     * GET /api/auth/profile
     */
    public function profile()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        $userData = $this->userModel->find($user['id']);
        unset($userData['password_hash']); // إخفاء كلمة المرور

        return $this->respondWithSuccess($userData, 'Profile retrieved successfully');
    }

    /**
     * Update user profile
     * PUT /api/auth/profile
     */
    public function updateProfile()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'username'   => "required|min_length[3]|max_length[100]|is_unique[users.username,id,{$user['id']}]",
            'email'      => "required|valid_email|is_unique[users.email,id,{$user['id']}]"
        ];

        if (!$this->validate($rules)) {
            return $this->respondWithValidationError($this->validator->getErrors());
        }

        // Get data from JSON or POST
        $jsonData = $this->request->getJSON(true);
        $updateData = [
            'id'         => $user['id'],
            'first_name' => $jsonData['first_name'] ?? $this->request->getVar('first_name'),
            'last_name'  => $jsonData['last_name'] ?? $this->request->getVar('last_name'),
            'username'   => $jsonData['username'] ?? $this->request->getVar('username'),
            'email'      => $jsonData['email'] ?? $this->request->getVar('email')
        ];

        if ($this->userModel->save($updateData)) {
            $userData = $this->userModel->find($user['id']);
            unset($userData['password_hash']); // إخفاء كلمة المرور

            return $this->respondWithSuccess($userData, 'Profile updated successfully');
        } else {
            return $this->respondWithError('Failed to update profile', 500);
        }
    }

    /**
     * Change password
     * PUT /api/auth/change-password
     */
    public function changePassword()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
            'revoke_all_tokens' => 'permit_empty|in_list[0,1,true,false]'
        ];

        if (!$this->validate($rules)) {
            return $this->respondWithValidationError($this->validator->getErrors());
        }

        // Get data from JSON or POST
        $jsonData = $this->request->getJSON(true);
        $currentPassword = $jsonData['current_password'] ?? $this->request->getVar('current_password');
        $newPassword = $jsonData['new_password'] ?? $this->request->getVar('new_password');
        $revokeTokens = $jsonData['revoke_all_tokens'] ?? $this->request->getVar('revoke_all_tokens');

        $userData = $this->userModel->find($user['id']);
        
        if (!password_verify($currentPassword, $userData['password_hash'])) {
            return $this->respondWithError('Current password is incorrect', 400);
        }

        $updateData = [
            'id' => $user['id'],
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)
        ];

        if ($this->userModel->save($updateData)) {
            // Optionally revoke all tokens after password change
            if ($revokeTokens === '1' || $revokeTokens === 'true') {
                $this->tokenService->revokeAllUserTokens($user['id']);
                return $this->respondWithSuccess(null, 'Password changed successfully. All tokens revoked.');
            }

            return $this->respondWithSuccess(null, 'Password changed successfully');
        } else {
            return $this->respondWithError('Failed to change password', 500);
        }
    }

    /**
     * Get user's tokens
     * GET /api/auth/tokens
     */
    public function tokens()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        try {
            $tokens = $this->tokenService->getUserTokens($user['id']);
            $stats = $this->tokenService->getTokenStats($user['id']);

            return $this->respondWithSuccess([
                'tokens' => $tokens,
                'stats' => $stats
            ], 'Tokens retrieved successfully');

        } catch (\Exception $e) {
            log_message('error', 'Get tokens failed: ' . $e->getMessage());
            return $this->respondWithError('Failed to retrieve tokens', 500);
        }
    }

    /**
     * Create new token
     * POST /api/auth/tokens
     */
    public function createToken()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        $rules = [
            'name' => 'required|max_length[255]',
            'abilities' => 'permit_empty',
            'expires_in_minutes' => 'permit_empty|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->respondWithValidationError($this->validator->getErrors());
        }

        try {
            // Get data from JSON or POST
            $jsonData = $this->request->getJSON(true);
            $name = $jsonData['name'] ?? $this->request->getVar('name');
            $abilities = $jsonData['abilities'] ?? $this->request->getVar('abilities') ?? ['*'];
            $expiresInMinutes = $jsonData['expires_in_minutes'] ?? $this->request->getVar('expires_in_minutes');

            // Parse abilities if it's a string
            if (is_string($abilities)) {
                $abilities = array_map('trim', explode(',', $abilities));
            }

            $tokenResult = $this->tokenService->createToken($user['id'], $name, $abilities, $expiresInMinutes);

            return $this->respondWithSuccess([
                'token' => $tokenResult['token'],
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult['expires_at'],
                'name' => $name,
                'abilities' => $abilities
            ], 'Token created successfully', 201);

        } catch (\Exception $e) {
            log_message('error', 'Create token failed: ' . $e->getMessage());
            return $this->respondWithError('Failed to create token: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Revoke specific token
     * DELETE /api/auth/tokens/{token}
     */
    public function revokeToken($token = null)
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        if (!$token) {
            return $this->respondWithError('Token is required', 400);
        }

        try {
            if ($this->tokenService->revokeToken($token)) {
                return $this->respondWithSuccess(null, 'Token revoked successfully');
            } else {
                return $this->respondWithError('Token not found or already revoked', 404);
            }
        } catch (\Exception $e) {
            log_message('error', 'Revoke token failed: ' . $e->getMessage());
            return $this->respondWithError('Failed to revoke token', 500);
        }
    }

    /**
     * Revoke all user tokens
     * DELETE /api/auth/tokens
     */
    public function revokeAllTokens()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        try {
            $this->tokenService->revokeAllUserTokens($user['id']);
            return $this->respondWithSuccess(null, 'All tokens revoked successfully');
        } catch (\Exception $e) {
            log_message('error', 'Revoke all tokens failed: ' . $e->getMessage());
            return $this->respondWithError('Failed to revoke tokens', 500);
        }
    }
}
