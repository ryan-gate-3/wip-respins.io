<x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Games List') }}
        </h2>
    </x-slot>

<div class="container-xl py-6 px-4 md:px-2 flex items-center justify-center grid grid-cols-2 gap-6 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-6">
    @foreach ($games_list['games'] as $game)
        <a href="{{ $game->link() }}">
            <div class="group relative bg-gray-300 hover:bg-gray-100 rounded-2xl px-2 py-1 shadow-md cursor-pointer">
                <div class="mt-1 mb-1">
                    <div class="image block w-full h-full items-center object-cover justify-center">
                        <div class="transition duration-300 ease absolute opacity-0 group-hover:opacity-100 z-10 items-center p-2"><button class="hover:bg-gray-100 text-gray-100 hover:text-gray-800 font-medium text-xs py-2 px-4 border border-gray-400 rounded-xl shadow">Demo</button></div>    
                        <img class="object-cover w-full opacity-100 group-hover:opacity-25" src="{{ $thumbnail_prefix }}/{{ $game->gid }}.png" style="border-radius: 22px;" loading="lazy">
                    </div>
                    <div class="py-2 px-3"> 
                    <p class="text-xs font-semibold text-gray-800 my-1 game-name">{{$game->name}}</p>
                    <div class="flex space-x-2 text-gray-400 text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    <p>{{$game->provider}}</p>
                    </div>
                </div>
                </div>
            </div>
        </a>
    @endforeach
    <div class="container-fluid mx-auto mt-10 mb-20" style="max-width:1250px;">
        {!! $games_list['games']->links('respins::partials.pagination') !!}		
    </div>
</div>
