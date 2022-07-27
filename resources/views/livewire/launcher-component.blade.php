@if($error === 1)
  <div class="container mx-auto px-4 py-24">
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-2 rounded relative" role="alert">
              <strong class="font-bold">Something weird happened..</strong>
              <span class="block sm:inline">{{ session('error') }}</span>
            </div>
             <a href="/game-api/" class="hover:bg-gray-100 text-gray-100 hover:text-gray-800 font-medium text-xs py-2 px-4 border border-gray-400 rounded-full shadow cursor-pointer">Gameslist</a>
             <a class="hover:bg-gray-100 text-gray-100 hover:text-gray-800 font-medium text-xs py-2 px-4 border border-gray-400 rounded-full shadow cursor-pointer" href="/full-whitelabel-crypto-casino-solution">Full Fledged Solution</a>
        @endif
    </div>
@else

    <div class="container-fluid mx-auto center-items justify-center min-h-screen mt-8" style="max-width: 1250px;">
    <x-jet-button wire:click="$emit('refreshComponent')" class="ml-4">
        Refresh
    </x-jet-button>
        <div class="inline-flex items-center text-xs bg-gray-500 text-gray-400 border-0 transition ease duration-300 mb-4 py-2 px-4 focus:outline-none hover:bg-gray-800 hover:text-gray-100 rounded-full mt-4 md:mt-0">{{ $url }}</div>
        <div class="max-w-screen aspect-w-16 aspect-h-9 rounded-xl">
          <iframe src="{{$url}}" class="max-w-screen rounded-xl" frameborder="0" allowfullscreen></iframe>
        </div>
        </div>
    </div>
@endif