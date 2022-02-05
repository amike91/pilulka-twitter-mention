<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PilulkaApiController extends Controller {
    public function twitterMentions(Request $request) {
        return [
            'foo' => 'bar'
        ];
    }
}
