<?php

namespace App\Http\Controllers;

use App\Commands\PilulkaMentionsCommand;
use App\Exceptions\PilulkaMentionsFetchException;
use App\Exceptions\PilulkaMentionsGeneralException;
use Illuminate\Http\Request;

class PilulkaController extends Controller {
    public function twitterMentions(Request $request) {
        $command        = new PilulkaMentionsCommand;
        $error          = false;
        $message        = "";

        try {
            $command->execute();
        } catch (PilulkaMentionsFetchException $exception) {
            $error      = true;
            $message    = "Error fetching mentions from Twitter. Try checking authentication Bearer token.";
        } catch (PilulkaMentionsGeneralException $exception) {
            $error      = true;
            $message    = "General error processing mentions from Twitter.";
        }

        return view('pilulka.twitter-mentions', [
            'error'     => $error,
            'message'   => $message,
            'mentions'  => $command->getMentions(),
        ]);
    }
}
