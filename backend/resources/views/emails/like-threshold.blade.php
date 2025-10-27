<x-mail::message>
# Like Threshold Notification

Great news! **{{ $person->name }}** has reached **50+ likes**! 🎉

## Profile Details
- **Name:** {{ $person->name }}
- **Age:** {{ $person->age }}
- **Location:** {{ $person->location }}
- **Total Likes:** {{ $person->likes_count }}

## Bio
{{ $person->bio }}

<x-mail::button url="{{ config('app.url') }}/admin/people/{{ $person->id }}">
View Profile
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
