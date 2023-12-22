@php($pinIcon = 'data:image/png;base64,' . base64_encode(file_get_contents(resource_path('img/pdf-icons/pin.png'))))
@php($tenureIcon = file_get_contents(resource_path('img/pdf-icons/document.svg')))
@php($propertyTypeIcon = file_get_contents(resource_path('img/pdf-icons/property.svg')))
@php($propertySubTypeIcon = file_get_contents(resource_path('img/pdf-icons/property_alt.svg')))
@php($percentageIcon = file_get_contents(resource_path('img/pdf-icons/percentage.svg')))
@php($currentUseIcon = file_get_contents(resource_path('img/pdf-icons/box.svg')))
@php($intendedUseIcon = file_get_contents(resource_path('img/pdf-icons/box_plus.svg')))
@php($refreshIcon = file_get_contents(resource_path('img/pdf-icons/refresh.svg')))
@php($relationshipToSellerIcon = file_get_contents(resource_path('img/pdf-icons/profile.svg')))

<div class="panel panel-filled">
    <h4>
        <img style="margin-bottom: -4px; margin-right: 4px" src="{{ $pinIcon }}" width="24" />
        {{ $address }}
    </h4>

    @if (isset($price))
        <h4>
            <strong>{{ $price_title }}:</strong> {{ $price }}
        </h4>
    @endif

    <div style="margin-top: 4px">
        <div style="display: inline-block; margin-right: 10px">
            {!! $tenureIcon !!}
            <strong>Tenure:</strong> {{ $tenure }}
        </div>

        @if (isset($property_type))
            <div style="display: inline-block; margin-right: 10px">
                {!! $propertyTypeIcon !!}
                <strong>Property type:</strong> {{ $property_type }}
            </div>
        @endif

        @if (isset($property_sub_type))
            <div style="display: inline-block; margin-right: 10px">
                {!! $propertySubTypeIcon !!}
                <strong>Property sub-type:</strong> {{ $property_sub_type }}
            </div>
        @endif
    </div>

    <div style="margin-top: 4px">
        @if (isset($shared_ownership_percentage))
            <div style="display: inline-block; margin-right: 10px">
                {!! $percentageIcon !!}
                <strong>Shared ownership percentage:</strong> {{ $shared_ownership_percentage }}
            </div>
        @endif

        @if (isset($current_use))
            <div style="display: inline-block; margin-right: 10px">
                {!! $currentUseIcon !!}
                <strong>Current use:</strong> {{ $current_use }}
            </div>
        @endif

        @if (isset($intended_use))
            <div style="display: inline-block; margin-right: 10px">
                {!! $intendedUseIcon !!}
                <strong>Intended use:</strong> {{ $intended_use }}
            </div>
        @endif
    </div>

    <div style="margin-top: 4px">
        @if (isset($dependent_on_sale))
            <div style="display: inline-block; margin-right: 10px">
                {!! $refreshIcon !!}
                <strong>Dependent on sale:</strong> {{ $dependent_on_sale }}
            </div>
        @endif

        @if (isset($relationship_to_seller))
            <div style="display: inline-block; margin-right: 10px">
                {!! $relationshipToSellerIcon !!}
                <strong>Relationship to seller:</strong> {{ $relationship_to_seller }}
            </div>
        @endif
    </div>
</div>
