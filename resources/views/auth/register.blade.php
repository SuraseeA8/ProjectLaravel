<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- ชื่อจริง --}}
        <div>
            <x-input-label for="users_fname" :value="__('First Name')" />
            <x-text-input id="users_fname" class="block mt-1 w-full" type="text" name="users_fname" :value="old('users_fname')" required autofocus />
            <x-input-error :messages="$errors->get('users_fname')" class="mt-2" />
        </div>

        {{-- นามสกุล --}}
        <div class="mt-4">
            <x-input-label for="users_lname" :value="__('Last Name')" />
            <x-text-input id="users_lname" class="block mt-1 w-full" type="text" name="users_lname" :value="old('users_lname')" required />
            <x-input-error :messages="$errors->get('users_lname')" class="mt-2" />
        </div>

        {{-- อีเมล --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- โทรศัพท์ --}}
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        {{-- ชื่อร้าน (บันทึก table shop_detail) --}}
        <div class="mt-6">
            <x-input-label for="shop_name" :value="__('Shop Name')" />
            <x-text-input id="shop_name" class="block mt-1 w-full" type="text" name="shop_name" :value="old('shop_name')" required />
            <x-input-error :messages="$errors->get('shop_name')" class="mt-2" />
        </div>

        {{-- รายละเอียดร้าน (บันทึก table shop_detail) --}}
        <div class="mt-4">
            <x-input-label for="description" :value="__('Shop Description')" />
            <textarea id="description" name="description" class="block mt-1 w-full" rows="3" maxlength="255">{{ old('description') }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        {{-- รหัสผ่าน --}}
        <div class="mt-6">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- ยืนยันรหัสผ่าน --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- ปุ่ม --}}
        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
