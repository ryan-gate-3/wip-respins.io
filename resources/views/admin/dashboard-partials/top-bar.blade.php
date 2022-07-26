<!-- Top bar Dashboard !-->
<div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-2 lg:grid-cols-4">
    <!-- Operators Count -->
    <a href="#" class="p-4 transition-shadow border rounded-lg shadow-sm hover:shadow-lg">
        <div class="flex items-start">
        <div class="flex flex-col flex-shrink-0 space-y-2">
            <span class="text-gray-400">Operators</span>
            <span class="text-lg font-semibold">{{ $page_meta['operators'] }}</span>
        </div>
        </div>
    </a>

    <!-- Total Gamesessions Created -->
    <a href="#" class="p-4 transition-shadow border rounded-lg shadow-sm hover:shadow-lg">
        <div class="flex items-start">
        <div class="flex flex-col flex-shrink-0 space-y-2">
            <span class="text-gray-400">Game Sessions</span>
            <span class="text-lg font-semibold">{{ $page_meta['gamesessions'] }}</span>
        </div>
        </div>
    </a>

    <!-- Total Players Created -->
    <a href="#" class="p-4 transition-shadow border rounded-lg shadow-sm hover:shadow-lg">
        <div class="flex items-start">
        <div class="flex flex-col flex-shrink-0 space-y-2">
            <span class="text-gray-400">Players</span>
            <span class="text-lg font-semibold">{{ $page_meta['players'] }}</span>
        </div>
        </div>
    </a>

    <!-- Articles chart card -->
    <a href="#" class="p-4 transition-shadow border rounded-lg shadow-sm hover:shadow-lg">
        <div class="flex items-start">
        <div class="flex flex-col flex-shrink-0 space-y-2">
            <span class="text-gray-400">Articles</span>
            <span class="text-lg font-semibold">600,429</span>
        </div>
        </div>
    </a>
</div>
