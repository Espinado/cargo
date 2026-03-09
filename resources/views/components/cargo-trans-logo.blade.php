@props(['size' => 'normal']) {{-- normal | compact --}}

@php
$isCompact = $size === 'compact';
$imgClass = $isCompact
    ? 'h-8 w-auto max-w-[120px] object-contain rounded-2xl'
    : 'h-12 w-auto max-w-[180px] object-contain rounded-3xl';
$wrapperClass = $isCompact
    ? 'inline-flex items-center justify-center'
    : 'inline-flex items-center justify-center';
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass]) }}>
    <img src="{{ asset('images/icons/cargo-logo.png') }}"
         alt="Cargo Trans"
         class="{{ $imgClass }}"
         loading="lazy"
    >
</div>
