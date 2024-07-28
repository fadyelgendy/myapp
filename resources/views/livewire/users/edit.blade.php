<?php

use App\Models\User;
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
    public int $country_id;

    #[Validate('nullable|image|max:1024')]
    public $photo;

    public function mount(): void
    {
        $this->fill($this->user);
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->photo) {
            $url = $this->photo->store('users', 'public');
            $this->user->update(['avatar' => "/storage/$url"]);
        }

        $this->user->update($data);

        $this->success('User Updated with Success', redirectTo: '/users');
    }

    public function with(): array
    {
        return [
            'countries' => Country::get(),
        ];
    }
}; ?>

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <x-header title="Update {{ $user->name }}" separator />

        <x-form wire:submit='save'>
            <x-file label="Avatar" wire:model="photo" accept="image/png, image/jpeg" crop-after-change>
                <img src="{{ $user->avatar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
            </x-file>

            <x-input label="Name" wire:modle='name' />
            <x-input label="Email" wire:modle='email' />
            <x-select label="Country" wire:modle='country_id' :options="$countries" placeholder="Select Country" />

            <x-slot:actions>
                <x-button label="Cancel" link="/users" />
                <x-button label="save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </div>

    <div>
        {{-- Get a nice picture from `StorySet` web site --}}
        <img src="/edit-form.png" width="300" class="mx-auto" />
    </div>
</div>
