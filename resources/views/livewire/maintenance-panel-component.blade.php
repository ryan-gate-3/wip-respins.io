    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Maintenance Panel') }}
        </h2>
    </x-slot>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

                 <div class="mt-10 sm:mt-0">
                    @livewire('maintenance-clear-cache')
                </div>
                <x-jet-section-border />
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>


        </div>
    </div>
