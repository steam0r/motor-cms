@foreach ($rows as $row)
    <div class="row">
        @foreach ($row as $element)
            <div class="motor-template-section col-md-{{$element['width']}}@if (isset($element['class'])) {{$element['class']}}@endif">
                @if (isset($element['items']))
                    @include('motor-cms::layouts.partials.template-sections', ['rows' => $element['items'], 'record' => $record])
                @else
                    <div class="alert alert-info">
                        <b>{{$element['alias']}}</b>
                        <button data-container="{{$element['container']}}"
                                class="btn btn-xs btn-default pull-right float-right motor-component-new"><i class="fa fa-plus"></i>
                        </button>
                        <div>
                        @foreach($record->getCurrentVersion()->components()->where('container', $element['container'])->get() as $component)
                            <div>
                                <button @click="deleteComponent('{{route(config('motor-cms-page-components.components.'.$component->component_name.'.route').'.destroy', ['component' => $component->component_id])}}', $event)" class="motor-component-delete btn btn-xs btn-default pull-right float-right"><i class="fa fa-trash"></i></button>
                                <button @click="editComponent('{{route(config('motor-cms-page-components.components.'.$component->component_name.'.route').'.edit', ['component' => $component->component_id])}}', {{$component->component_id}}, $event)" class="motor-component-edit btn btn-xs btn-default pull-right float-right"><i class="fa fa-edit"></i></button>
                                {{config('motor-cms-page-components.components.'.$component->component_name.'.name')}}
                            </div>
                        @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endforeach