<div class="card mb-3">
    <div class="row g-0">
        <div class="col-md-4">
            <a href="{{ $mention->getUrl() }}" target="_blank"><img src="{{ $mention->getAuthor()->getLargerAvatarUrl() }}" class="img-fluid w-100 rounded-start"></a>
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <h5 class="card-title"><a href="{{ $mention->getAuthor()->getUrl() }}" target="_blank">{{ $mention->getAuthor()->getNicename() }}</a> @if($mention->getAuthor()->isVerified()) <span class="badge bg-primary rounded-pill">Verified</span> @endif</h5>

                <p class="card-text">{{ $mention->getText() }}</p>
                <p class="card-text"><small class="text-muted text-end">Posted {{ $mention->getCreatedAt()->diffForHumans() }}</small></p>
                <a href="{{ $mention->getUrl() }}" class="btn btn-primary" target="_blank">See mention on Twitter</a>
            </div>
        </div>
    </div>
</div>
