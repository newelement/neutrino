<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach( $object_types as $type => $objects )
    @foreach( $objects as $object )
    @php
        $data = _getObjectSitemapData($type, $object, $sitemap_settings);
    @endphp
    <url>
        <loc>{{ config('app.url') }}{{ $data['url'] }}</loc>
        <lastmod>{{ $object->updated_at->timezone( config('neutrino.timezone') )->format('Y-m-d') }}</lastmod>
        <changefreq>{{ $object->sitemap_change? $object->sitemap_change : $data['change'] }}</changefreq>
        <priority>{{ $object->sitemap_priority? $object->sitemap_priority : $data['priority'] }}</priority>
    </url>
    @endforeach
@endforeach
</urlset>
