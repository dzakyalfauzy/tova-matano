<x-mail::message>
# Pesan dari Formulir Kontak

Anda menerima pesan baru dari formulir kontak TMCC.

**Nama:** {{ $name }}<br>
**Email:** {{ $email }}

## Isi Pesan

{{ $message }}

---
<br>
Dikirim dari formulir kontak website TMCC.
</x-mail::message>
