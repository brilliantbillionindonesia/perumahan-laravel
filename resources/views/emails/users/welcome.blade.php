@component('mail::message')
# Hi {{ $user->name ?? 'there' }} 👋

Akun kamu sudah dibuat dengan email **{{ $user->email }}**.

Klik tombol di bawah untuk konfirmasi email dan aktivasi akun:

@component('mail::button', ['url' => 'url'])
Konfirmasi Email
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
