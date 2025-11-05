<x-layouts.app title="Vender">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div id="app-vender"></div>

    @if(session('ventaDuplicada'))
        <script>
            window.ventaDuplicada = {!! session('ventaDuplicada') !!};
        </script>
    @endif
</x-layouts.app>