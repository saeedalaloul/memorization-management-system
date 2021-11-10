<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo/>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            شكرا لتسجيلك! قبل البدء ، هل يمكنك التحقق من عنوان بريدك الإلكتروني من خلال النقر على الرابط الذي أرسلناه
            إليك عبر البريد الإلكتروني للتو؟ إذا لم تتلق البريد الإلكتروني ، فسنرسل لك رسالة أخرى بكل سرور.
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                تم إرسال رابط تحقق جديد إلى عنوان البريد الإلكتروني الذي قدمته أثناء التسجيل.
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-jet-button type="submit">
                        إعادة ارسال بريد التحقق
                    </x-jet-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </x-jet-authentication-card>
</x-guest-layout>
