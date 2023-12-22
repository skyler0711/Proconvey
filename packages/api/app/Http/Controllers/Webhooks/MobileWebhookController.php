<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MobileWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->view('mobile-webhook', [
            'url' => 'proconvey://redirect?'.http_build_query($request->query()),
        ]);
    }
}
