<?php

use App\Models\User;
use App\Models\Language;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\Attributes\Validate;
use App\Models\Country;
use Livewire\WithFileUploads;

new class extends Component {
    use Toast, WithFileUploads;

    public User $user;

    #[Validate('required')]
    public string $name;

    #[Validate('required|email')]
    public string $email;

    #[Validate('sometimes')]
    public ?int $country_id;

    #[Validate('nullable|image|max:1024')]
    public $photo;

    #[Validate('required')]
    public array $my_languages = [];

    #[Validate('sometimes')]
    public ?string $bio = null;

    public function mount(): void
    {
        $this->fill($this->user);
        $this->my_languages = $this->user->languages->pluck('id')->all();
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->photo) {
            $url = $this->photo->store('users', 'public');
            $this->user->update(['avatar' => "/storage/$url"]);
        }

        $this->user->update($data);

        // Sync selection
        $this->user->languages()->sync($this->my_languages);

        $this->success('User Updated with Success', redirectTo: '/users');
    }

    public function with(): array
    {
        return [
            'countries' => Country::get(),
            'languages' => Language::all(),
        ];
    }
}; ?>

<div>
    <x-header title="Update {{ $user->name }}" separator />

    <x-form wire:submit='save'>
        <div class="grid-cols-5 lg:grid">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from user" size="text-2xl" />
            </div>
            <div class="grid col-span-3 gap-3">
                <x-file label="Avatar" wire:model="photo" accept="image/png, image/jpeg" crop-after-change>
                    <img src="{{ $user->avatar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
                </x-file>

                <x-input label="Name" wire:model='name' />
                <x-input label="Email" wire:model='email' />
                <x-select label="Country" wire:model='country_id' :options="$countries" placeholder="Select Country" />
            </div>
        </div>

        {{--  Details section --}}
        <hr class="my-5" />

        <div class="grid-cols-5 lg:grid">
            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the user" size="text-2xl" />
            </div>
            <div class="grid col-span-3 gap-3">
                <x-choices-offline label="My languages" wire:model="my_languages" :options="$languages" searchable />
                <x-editor wire:model="bio" label="Bio" hint="The great biography" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/users" />
            <x-button label="save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
