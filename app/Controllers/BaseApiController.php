<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class BaseApiController extends ResourceController
{
    use ResponseTrait;

    protected $format = 'json';

    /**
     * Return success response
     */
    protected function respondWithSuccess($data = null, $message = 'Success', $code = 200)
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];

        return $this->respond($response, $code);
    }

    /**
     * Return error response
     */
    protected function respondWithError($message = 'Error', $code = 400, $data = null)
    {
        $response = [
            'status' => false,
            'message' => $message,
            'data' => $data
        ];

        return $this->respond($response, $code);
    }

    /**
     * Return validation error response
     */
    protected function respondWithValidationError($errors, $message = 'Validation failed')
    {
        $response = [
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ];

        return $this->respond($response, 422);
    }

    /**
     * Return paginated response
     */
    protected function respondWithPagination($data, $pager, $message = 'Success')
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'current_page' => $pager->getCurrentPage(),
                'per_page' => $pager->getPerPage(),
                'total' => $pager->getTotal(),
                'total_pages' => $pager->getPageCount(),
                'has_previous' => $pager->getCurrentPage() > 1,
                'has_next' => $pager->getCurrentPage() < $pager->getPageCount(),
                'previous_page' => $pager->getCurrentPage() > 1 ? $pager->getCurrentPage() - 1 : null,
                'next_page' => $pager->getCurrentPage() < $pager->getPageCount() ? $pager->getCurrentPage() + 1 : null
            ]
        ];

        return $this->respond($response, 200);
    }

    /**
     * Get current user from token
     */
    protected function getCurrentUser()
    {
        // Try to get user from token (via TokenAuthFilter)
        if (isset($this->request->user)) {
            return $this->request->user;
        }

        // Fallback to session (for backward compatibility)
        $session = session();
        if ($session->get('is_logged_in')) {
            return [
                'id' => $session->get('user_id'),
                'username' => $session->get('username'),
                'email' => $session->get('email'),
                'first_name' => $session->get('first_name'),
                'last_name' => $session->get('last_name'),
            ];
        }
        return null;
    }

    /**
     * Get current token data
     */
    protected function getCurrentToken()
    {
        return isset($this->request->token) ? $this->request->token : null;
    }

    /**
     * Get token service instance
     */
    protected function getTokenService()
    {
        return isset($this->request->tokenService) ? $this->request->tokenService : new \App\Libraries\TokenService();
    }

    /**
     * Validate required authentication
     */
    protected function requireAuth()
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return $this->respondWithError('Authentication required', 401);
        }
        return $user;
    }
}
