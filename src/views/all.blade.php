{{-- 
This demonstrates how to setup session security client side stuff on your own.
It provides sensible defaults so you could start with just::

    @include ( 'laravel-session-security::all')

--}}

{{-- If the user is not authenticated then there is no session to secure ! --}}
@if(Auth::check())

    <script type="text/javascript" src="{{ asset('packages/med-abidi/laravel-session-security/jquery.min.js') }}"></script>

    {{-- The modal dialog stylesheet, it's pretty light so it should be easy to hack --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('packages/med-abidi/laravel-session-security/style.css') }}"></link>

    {{-- Include the template that actually contains the modal dialog --}}
    @include('laravel-session-security::dialog')

    {{-- Load SessionSecurity javascript 'class', jquery should be loaded - by you - at this point --}}
    <script type="text/javascript" src="{{ asset('packages/med-abidi/laravel-session-security/script.js') }}"></script>

    {{-- Bootstrap a SessionSecurity instance as the sessionSecurity global variable --}}
        <script type="text/javascript">
            var sessionSecurity = new yourlabs.SessionSecurity({
                pingUrl: "{{ route('session_security_ping') }}",
                warnAfter: {{ Config::get('laravel-session-security::warn_after') }},
                expireAfter: {{ Config::get('laravel-session-security::expire_after') }},
                confirmFormDiscard: "{{ trans('session_secure.have_unsave_changes') }}"
            });
        </script>
@endif

