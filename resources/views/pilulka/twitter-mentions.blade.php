@extends('layout')

@section('title', 'Pilulka - Twitter mentions')

@section('content')
<div class="container">
    <div class="col-6 offset-md-3 pt-5">
        <h1 class="text-center pb-3">Pilulka - Twitter mentions</h1>

        @if($error)
            <div class="alert alert-danger" role="alert">
                {{ $message }}
            </div>
        @else
            @if($mentions->count() > 0)
            <p class="text-center pb-2">
                Tweets containing <a href="https://twitter.com/hashtag/pilulka" target="_blank">#pilulka</a>, <a
                    href="https://twitter.com/hashtag/pilulkacz" target="_blank">#pilulkacz</a> hashtags and links to <a href="https://pilulka.cz" target="_blank">pilulka.cz</a> in links from the last 7 days.
            </p>

            <p class="text-center small pb-5">
                {{ $mentions->count() }} mentions found in total.
            </p>

            @foreach($mentions as $mention)
                @include('components.mention', $mention)
            @endforeach

            @elseif($mentions->count() === 0)
                <div class="alert alert-primary" role="alert">
                    No mentions of Pilulka on Twitter for the last 7 days :(
                </div>
            @endif
        @endif

    </div>
</div>
@stop
