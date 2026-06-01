<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class ContactController extends ApiController
{
    public function index()
    {
        return $this->successResponse('Contact submissions loaded', [
            'messages' => [],
        ]);
    }

    public function show(int $id)
    {
        return $this->successResponse('Contact submission loaded', [
            'message' => ['id' => $id],
        ]);
    }

    public function destroy(int $id)
    {
        return $this->successResponse('Contact submission deleted', [
            'message' => ['id' => $id],
        ]);
    }
}
