<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\TokenService;

class TokenAuthFilter implements FilterInterface
{
    protected $tokenService;

    public function __construct()
    {
        $this->tokenService = new TokenService();
    }

    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');
        
        // Get Authorization header
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (!$authHeader) {
            // Check for token in query parameter (fallback)
            $token = $request->getGet('token');
            if ($token) {
                $authHeader = $token;
            }
        }

        if (!$authHeader) {
            return $response->setJSON([
                'status' => false,
                'message' => 'Authorization token required',
                'error' => 'missing_token'
            ])->setStatusCode(401);
        }

        // Extract token from header
        $token = $this->tokenService->extractTokenFromHeader($authHeader);
        
        if (!$token) {
            return $response->setJSON([
                'status' => false,
                'message' => 'Invalid token format',
                'error' => 'invalid_token_format'
            ])->setStatusCode(401);
        }

        // Validate token
        try {
            $result = $this->tokenService->validateToken($token);
            
            if (!$result) {
                return $response->setJSON([
                    'status' => false,
                    'message' => 'Invalid or expired token',
                    'error' => 'invalid_token'
                ])->setStatusCode(401);
            }

            // Check if specific ability is required
            if (!empty($arguments)) {
                $requiredAbility = $arguments[0];
                if (!$this->tokenService->hasAbility($result['token'], $requiredAbility)) {
                    return $response->setJSON([
                        'status' => false,
                        'message' => 'Insufficient permissions',
                        'error' => 'insufficient_permissions',
                        'required_ability' => $requiredAbility
                    ])->setStatusCode(403);
                }
            }

            // Store user and token data in request for later use
            $request->user = $result['user'];
            $request->token = $result['token'];
            $request->tokenService = $this->tokenService;

        } catch (\Exception $e) {
            log_message('error', 'Token validation error: ' . $e->getMessage());
            
            return $response->setJSON([
                'status' => false,
                'message' => 'Token validation failed',
                'error' => 'validation_failed'
            ])->setStatusCode(401);
        }

        return $request;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an exception or error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add token info to response headers (optional)
        if (isset($request->token)) {
            $response->setHeader('X-Token-Name', $request->token['name']);
            $response->setHeader('X-Token-Last-Used', $request->token['last_used_at'] ?? 'Never');
        }

        return $response;
    }
}
