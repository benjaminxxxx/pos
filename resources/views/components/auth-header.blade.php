@props(['title', 'description' => null])

<div class="flex w-full flex-col text-center">
    <flux:heading size="lg">{{ $title }}</flux:heading>
    @if ($description)
        <flux:subheading class="mt-5">{{ $description }}</flux:subheading>
    @endif
</div>
