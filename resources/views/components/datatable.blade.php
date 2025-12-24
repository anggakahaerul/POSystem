{{--
    Dynamic DataTable Component
    
    Usage:
    @include('components.datatable', [
        'id' => 'categories-table',
        'ajax' => route('categories.data'),
        'columns' => [
            ['data' => 'id', 'title' => 'ID', 'width' => '50px'],
            ['data' => 'name', 'title' => 'Nama'],
            ['data' => 'created_at', 'title' => 'Dibuat'],
            ['data' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false, 'width' => '150px'],
        ],
        'createUrl' => route('categories.create'),
        'createText' => 'Tambah Kategori',
        'order' => [[0, 'desc']],
    ])
--}}

<div class="card">
    @if(isset($title) || isset($createUrl))
    <div class="card-header">
        @if(isset($title))
            <h3 class="card-title">{{ $title }}</h3>
        @endif
        @if(isset($createUrl))
            <div class="card-tools">
                <a href="{{ $createUrl }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> {{ $createText ?? 'Tambah Baru' }}
                </a>
            </div>
        @endif
    </div>
    @endif
    
    <div class="card-body">
        @if(isset($filters) && count($filters) > 0)
        <div class="row mb-3">
            @foreach($filters as $filter)
                <div class="{{ $filter['col'] ?? 'col-md-3' }}">
                    <div class="form-group">
                        <label>{{ $filter['label'] }}</label>
                        @if($filter['type'] === 'select')
                            <select class="form-control form-control-sm dt-filter" id="filter_{{ $filter['name'] }}" data-column="{{ $filter['column'] ?? $filter['name'] }}">
                                <option value="">-- Semua --</option>
                                @foreach($filter['options'] as $option)
                                    @php
                                        $optValue = is_object($option) ? $option->{$filter['option_value'] ?? 'id'} : (is_array($option) ? $option['value'] : $option);
                                        $optLabel = is_object($option) ? $option->{$filter['option_label'] ?? 'name'} : (is_array($option) ? $option['label'] : $option);
                                    @endphp
                                    <option value="{{ $optValue }}">{{ $optLabel }}</option>
                                @endforeach
                            </select>
                        @elseif($filter['type'] === 'date')
                            <input type="date" class="form-control form-control-sm dt-filter" id="filter_{{ $filter['name'] }}" data-column="{{ $filter['column'] ?? $filter['name'] }}">
                        @else
                            <input type="text" class="form-control form-control-sm dt-filter" id="filter_{{ $filter['name'] }}" data-column="{{ $filter['column'] ?? $filter['name'] }}" placeholder="{{ $filter['placeholder'] ?? '' }}">
                        @endif
                    </div>
                </div>
            @endforeach
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-secondary btn-sm mb-3" id="reset-filters">
                    <i class="fas fa-redo mr-1"></i> Reset
                </button>
            </div>
        </div>
        @endif

        <table id="{{ $id }}" class="table table-bordered table-striped table-hover" style="width: 100%">
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th style="{{ isset($column['width']) ? 'width: '.$column['width'] : '' }}">
                            {{ $column['title'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#{{ $id }}').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ $ajax }}",
            data: function(d) {
                // Add filter parameters
                $('.dt-filter').each(function() {
                    d[$(this).attr('id')] = $(this).val();
                });
                @if(isset($extraData))
                    @foreach($extraData as $key => $value)
                        d['{{ $key }}'] = '{{ $value }}';
                    @endforeach
                @endif
            }
        },
        columns: [
            @foreach($columns as $column)
            {
                data: '{{ $column['data'] }}',
                name: '{{ $column['name'] ?? $column['data'] }}',
                orderable: {{ isset($column['orderable']) && $column['orderable'] === false ? 'false' : 'true' }},
                searchable: {{ isset($column['searchable']) && $column['searchable'] === false ? 'false' : 'true' }},
                @if(isset($column['className']))
                className: '{{ $column['className'] }}',
                @endif
                @if(isset($column['render']))
                render: {!! $column['render'] !!},
                @endif
            },
            @endforeach
        ],
        order: {!! json_encode($order ?? [[0, 'desc']]) !!},
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Memuat...',
            lengthMenu: 'Tampilkan _MENU_ data',
            zeroRecords: 'Data tidak ditemukan',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(filter dari _MAX_ total data)',
            search: 'Cari:',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: '<i class="fas fa-chevron-right"></i>',
                previous: '<i class="fas fa-chevron-left"></i>'
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Re-initialize tooltips after table draw
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // Filter change event
    $('.dt-filter').on('change keyup', function() {
        table.draw();
    });

    // Reset filters
    $('#reset-filters').on('click', function() {
        $('.dt-filter').val('');
        table.draw();
    });

    // Make table accessible globally
    window.{{ $id.replace('-', '_') }} = table;
});
</script>
@endpush
