<x-guest-layout>
    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-primary tracking-tight italic">System Installation</h1>
        <p class="text-sm text-gray-500 mt-2">Create the first administrator account to begin.</p>
    </div>

    <form method="POST" action="/install" class="space-y-6">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label value="First Name" />
                <x-text-input name="first_name" required autofocus />
            </div>
            <div>
                <x-input-label value="Last Name" />
                <x-text-input name="last_name" required />
            </div>
        </div>

        <div>
            <x-input-label value="Username" />
            <x-text-input name="username" required placeholder="admin" />
        </div>

        <div>
            <x-input-label value="Password" />
            <x-text-input name="password" type="password" required />
        </div>

        <div>
            <x-input-label value="Confirm Password" />
            <x-text-input name="password_confirmation" type="password" required />
        </div>

        <div class="mt-10">
            <x-primary-button class="w-full justify-center py-4 text-lg">
                Complete Setup
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
