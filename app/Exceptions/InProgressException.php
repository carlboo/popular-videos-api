<?php

namespace App\Exceptions;

use Exception;

class InProgressException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     * Returns in progress response for asynchronous processing
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response([
            [
                "data" => [
                    "type" => "queue-jobs",
                    "attributes" => [
                        "status" => "Pending request, waiting other process"
                    ],
                    "links" => [
                        "self" => $request->fullUrl()
                    ]
                ]
            ]
        ])
        ->withHeaders([
            'Content-Type' => 'application/vnd.api+json',
            'Retry-After' => 1,
        ]);
    }
}