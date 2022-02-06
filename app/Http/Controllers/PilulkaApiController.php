<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\PilulkaMentionsCommand;
use App\Exceptions\PilulkaMentionsFetchException;
use App\Exceptions\PilulkaMentionsGeneralException;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class PilulkaApiController extends Controller {
    use ApiResponses;

    public function twitterMentions(Request $request) {
        $pilulkaMentions = new PilulkaMentionsCommand;

        try {
            $pilulkaMentions->execute();
        } catch (PilulkaMentionsFetchException $exception) {
            return $this->respondWithError(
                'Error fetching mentions from Twitter. Try checking authentication Bearer token.'
            );
        } catch (PilulkaMentionsGeneralException $exception) {
            return $this->respondWithError('General error processing mentions from Twitter.');
        }

        $message        = 'Twitter mentions of Pilulka for the last 7 days';
        if ($pilulkaMentions->getMentions()->count() === 0) {
            $message    = 'There are no mentions of Pilulka on Twitter for the last 7 days.';
        }

        return $this->respondWithPayload(
            $message, $pilulkaMentions->getMentions()->toArray()
        );
    }
}
