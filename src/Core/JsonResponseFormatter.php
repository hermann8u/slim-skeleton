<?php

namespace Core;

use Slim\Http\Response;
use Symfony\Component\Validator\ConstraintViolationList;

class JsonResponseFormatter
{
    /**
     * Shortcut to prepare to return a json success Response
     *
     * @param Response $response
     * @param $data
     *
     * @return Response
     */
    public function successResponse(Response $response, $data)
    {
        return $response->withJson([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Shortcut to prepare to return a json error Response
     *
     * @param Response $response
     * @param $code
     * @param $errors
     *
     * @return Response
     */
    public function errorResponse(Response $response, $code, $errors)
    {
        $message = '';
        $formattedErrors = [];

        switch ($code) {
            case 400:
                $message = 'Bad request';
                break;
            case 401:
                $message = 'Unauthorized';
                break;
            case 403:
                $message = 'Forbidden';
                break;
            case 404:
                $message = 'Not Found';
                break;
            case 405:
                $message = 'Method Not Allowed';
                break;
            case 422:
                $message = 'Unprocessable Entity';
                break;
            case 500:
                $message = 'Internal Server Error';
                break;
        }

        if (is_string($errors)) {
            $formattedErrors[] = [
                'message' =>$errors
            ];
        }
        elseif ($errors instanceof ConstraintViolationList) {
            foreach ($errors as $error) {
                $formattedError = [
                    'message' => $error->getMessage()
                ];

                if ($params = $error->getParameters()) {
                    foreach ($params as $key => $param) {
                        $params[trim($key, '{} ')] = $param;
                        unset($params[$key]);
                    }
                    $formattedError['settings'] = $params;
                }

                $formattedErrors[] = $formattedError;
            }
        }
        else {
            $formattedErrors = $errors;
        }

        return $response->withJson([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $formattedErrors
        ], $code);
    }
}