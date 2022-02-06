<?php

declare(strict_types=1);

namespace App\Commands;

use App\Exceptions\PilulkaMentionsFetchException;
use App\Exceptions\PilulkaMentionsGeneralException;
use App\Exceptions\TwitterApiException;
use App\Models\Twitter\Post;
use App\Models\Twitter\User;
use App\Services\Twitter;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/**
 * Gets the latest mentions of Pilulka's hashtags and  website URL from Twitter.
 * Result is sorted by posting date in the descending order (newest first).
 *
 * Uses Twitter Service with Twitter API v2 underneath.
 *
 */
class PilulkaMentionsCommand extends Command implements Arrayable {
    protected Twitter $twitter;

    protected Collection $mentions;
    protected Collection $rawMentionData;

    public function __construct() {
        $this->twitter          = new Twitter;

        $this->mentions         = collect();
        $this->rawMentionData   = collect();

    }


    /**
     * Fetches mentions using Twitter Service, transforms the data into
     * Twitter Post and User objects in a collection, sorts it from
     * newest to oldest Post.
     *
     * @throws PilulkaMentionsFetchException|PilulkaMentionsGeneralException
     */
    public function execute() {
        try {
            $this->fetchMentions();
            $this->processMentionData();
            $this->sortMentionsFromNewest();
        } catch (TwitterApiException|GuzzleException $exception) {
            throw new PilulkaMentionsFetchException('Error fetching data from Twitter.');
        } catch (\Exception $exception) {
            throw new PilulkaMentionsGeneralException('General error processing mentions.');
        }

    }

    public function getMentions() : Collection {
        return $this->mentions;
    }

    protected function getRawMentionData() : Collection {
        return $this->rawMentionData;
    }

    public function toArray() {
        return $this->getMentions()->toArray();
    }

    /**
     * Fetches mentions from Twitter. Query:
     * '#pilulka OR #pilulkacz OR (has:links pilulka.cz)'
     *
     * @throws TwitterApiException|GuzzleException
     */
    protected function fetchMentions() {
        $query                  = '#pilulka OR #pilulkacz OR (has:links pilulka.cz)';

        $this->rawMentionData  = collect($this->twitter->getRecentTweets($query));
    }

    /**
     * Takes an array of "raw" mentions data and transform it into
     * a collection of Twitter Post and User objects.
     *
     * @return void
     */
    protected function processMentionData() {
        $mentions       = $this->getRawMentionData()->get('data');
        $users          = $this->getRawMentionData()->get('includes')['users'];

        $users          = collect($users);

        foreach ($mentions as $mention) {
            $userId     = $mention['author_id'];
            $userInfo   = $users->where('id', $userId)->first();

            if (! $userInfo) continue;

            $author     = User::createFromArray($userInfo);

            $post       = new Post(
                $mention['id'],
                $mention['text'],
                $author,
                new Carbon($mention['created_at']),
            );

            $this->getMentions()->push($post);
        }
    }

    protected function sortMentionsFromNewest() {
        $this->mentions         = $this->mentions->sortByDesc(function($mention) {
            return $mention->getCreatedAt()->getTimestamp();
        });
    }
}
