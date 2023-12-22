<div class="page-break"></div>

<h1>{{ $person_type }} {{ $person_index + 1 }} Details ({{ $type }})</h1>

<x-pdf-overview.person.overview :person="$person" :property="$property" />

<div class="spacer"></div>

<x-pdf-overview.person.representation :person="$person" :property="$property" />

<div class="spacer"></div>
