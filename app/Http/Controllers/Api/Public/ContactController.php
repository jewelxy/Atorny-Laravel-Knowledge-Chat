<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class ContactController extends ApiController
{
    public function info()
    {
        return $this->successResponse('Contact data retrieved', [
            'contact' => [
                'email' => config('mail.from.address'),
                'phone' => config('app.contact_phone', null),
            ],
        ]);
    }

    public function submit(Request $request)
    {
        $payload = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        return $this->successResponse('Contact request received', [
            'payload' => $payload,
        ], 201);
    }
}
