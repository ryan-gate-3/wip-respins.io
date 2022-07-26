@extends($scaffold['layout'])
@section('content')
<div class="relative px-8 pt-8 pb-20 mx-auto xl:px-5 max-w-7xl px-0 sm:px-6 lg:pt-10 lg:pb-28">
    <div class="absolute inset-0">
        <div class="bg-white h-1/3 sm:h-2/3"></div>
    </div>
    <div class="relative mx-auto">
		<div class="flex flex-col justify-start">
			<h1 class="text-3xl font-extrabold leading-9 tracking-tight text-gray-900 sm:text-4xl sm:leading-10">
				Games
			</h1>
			<p class="mt-1 text-base sm:text-md sm:text-xl leading-7 text-gray-500 sm:mt-4">
				Loaded {{ $games_count }} games.
			</p>
			@if($scaffold['show_provider_nav'] === true)
			<ul class="flex flex-wrap self-start inline w-auto px-3 py-1 mt-3 text-xs font-medium text-gray-600 bg-blue-100 rounded-md">
				<li class="flex mr-4 font-bold text-blue-600 uppercase">Providers:</li>
				@foreach($providers as $provider)
					<div class="@if(isset($providers) && isset($providers->slug) && ($providers->slug == $provider->slug)){{ 'text-blue-700' }}@endif">
						<a href="/games?provider={{ $provider->slug }}">{{ $provider->name }}</a>
					</div>
					@if(!$loop->last)
					<li class="mx-2">&middot;</li>
					@endif
				@endforeach
			</ul>
			@endif
		</div>
        <div class="grid gap-5 mx-auto mt-12 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
			<!-- Loop through games received from FrontendController -->
			@foreach($games as $game)
			<article id="post-{{ $game->gid }}" class="flex flex-col overflow-hidden rounded-lg shadow-lg" typeof="game">
				<meta property="name" content="{{ $game->name }}">
				<meta property="dateModified" content="{{ Carbon\Carbon::parse($game->updated_at)->toIso8601String() }}">
				<meta class="uk-margin-remove-adjacent" property="datePublished" content="{{ Carbon\Carbon::parse($game->created_at)->toIso8601String() }}">
                <div class="flex-shrink-0">
					<a target="_blank" href="{{ $game->link() }}">
                    	<img id="{{ $game->gid }}" class="object-cover w-full h-42 md:h-36 lg:h-30" src="{{ $thumbnail_prefix }}{{ $game->gid }}.png" alt="{{ $game->name }}">
					</a>
                </div>
                <div class="relative flex flex-col justify-between flex-1 p-2 md:p-4 bg-white">
                    <div class="flex-1">
                        <a href="{{ $game->slug }}" class="block">
                            <h3 class="text-md sm:text-xl font-semibold text-gray-900">
                                {{ $game->name }}
                            </h3>   
                        </a>
                        <a href="{{ $game->slug }}" class="block">
                            <p class="mt-1 text-base leading-6 text-gray-500">
							{{ $game->provider }} || {{ $game->internal_origin_casino }}
						</p>
                        </a>
                    </div>
                </div>
            </article> 
			@endforeach
			<!-- End loop games -->
        </div>
    </div>
	<div class="flex justify-center my-10">
	{!! $games->links('respins::partials.pagination') !!}		
	</ul>
</div>
@endsection
