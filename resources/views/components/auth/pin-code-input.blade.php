@props([
    'id' => 'pin-code',
    'name' => 'code',
    'length' => 6,
])

@php($pinCode = (string) old($name, ''))

<div data-pin-code-input data-pin-code-length="{{ $length }}" role="group" aria-label="{{ $length }}-digit verification code" class="flex justify-center gap-2 sm:gap-3">
    @for ($digitIndex = 0; $digitIndex < $length; $digitIndex++)
        <input
            id="{{ $id }}-digit-{{ $digitIndex + 1 }}"
            type="text"
            inputmode="numeric"
            pattern="[0-9]"
            maxlength="{{ $digitIndex === 0 ? $length : 1 }}"
            autocomplete="{{ $digitIndex === 0 ? 'one-time-code' : 'off' }}"
            data-pin-code-digit
            aria-label="Digit {{ $digitIndex + 1 }} of {{ $length }}"
            value="{{ mb_substr($pinCode, $digitIndex, 1) }}"
            required
            class="size-11 rounded-xl border border-slate-200 bg-white text-center font-mono text-xl font-bold text-slate-800 outline-none transition focus:border-[#168cff] focus:ring-2 focus:ring-sky-100 sm:size-12 sm:text-2xl"
        >
    @endfor

    <input type="hidden" name="{{ $name }}" value="{{ $pinCode }}" data-pin-code-value>
</div>