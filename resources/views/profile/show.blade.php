<x-app-layout>
    <x-slot name="header">
        <h2>
            {{ __('Profile Information') }}
        </h2>
    </x-slot>

    <div>
        <div>
            <div>
                <div>
                    @include('profile.partials.view-profile-information-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
