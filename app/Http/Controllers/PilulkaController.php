<?php

namespace App\Http\Controllers;

use App\Commands\PilulkaMentionsCommand;
use Illuminate\Http\Request;

class PilulkaController extends Controller {
    public function twitterMentions(Request $request) {
        $command        = new PilulkaMentionsCommand;

        $command->execute();
        dd($command->getMentions()->toArray());
    }
}
