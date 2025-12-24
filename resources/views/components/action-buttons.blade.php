{{--
    Action Buttons Component for DataTables
    
    Usage in Controller:
    return DataTables::of($data)
        ->addColumn('action', function($row) {
            return view('components.action-buttons', [
                'id' => $row->id,
                'showUrl' => route('categories.show', $row->id),
                'editUrl' => route('categories.edit', $row->id),
                'deleteUrl' => route('categories.destroy', $row->id),
            ])->render();
        })
        ->rawColumns(['action'])
        ->make(true);
--}}

<div class="btn-group" role="group">
    @if(isset($showUrl))
        <a href="{{ $showUrl }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="Lihat Detail">
            <i class="fas fa-eye"></i>
        </a>
    @endif
    
    @if(isset($editUrl))
        <a href="{{ $editUrl }}" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
    @endif
    
    @if(isset($deleteUrl))
        <form action="{{ $deleteUrl }}" method="POST" id="delete-form-{{ $id }}" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Hapus" 
                onclick="confirmDelete('delete-form-{{ $id }}', '{{ $deleteMessage ?? 'Data akan dihapus permanen!' }}')">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endif
    
    @if(isset($customButtons))
        @foreach($customButtons as $button)
            @if(isset($button['url']))
                <a href="{{ $button['url'] }}" class="btn btn-{{ $button['color'] ?? 'secondary' }} btn-sm" 
                    data-toggle="tooltip" title="{{ $button['title'] ?? '' }}">
                    <i class="{{ $button['icon'] ?? 'fas fa-cog' }}"></i>
                    @if(isset($button['text'])) {{ $button['text'] }} @endif
                </a>
            @elseif(isset($button['action']))
                <form action="{{ $button['action'] }}" method="POST" class="d-inline" id="{{ $button['formId'] ?? 'action-form-'.$id }}">
                    @csrf
                    @if(isset($button['method']))
                        @method($button['method'])
                    @endif
                    <button type="{{ $button['submit'] ?? 'button' }}" class="btn btn-{{ $button['color'] ?? 'secondary' }} btn-sm"
                        data-toggle="tooltip" title="{{ $button['title'] ?? '' }}"
                        @if(isset($button['confirm']))
                            onclick="event.preventDefault(); Swal.fire({
                                title: '{{ $button['confirmTitle'] ?? 'Konfirmasi' }}',
                                text: '{{ $button['confirm'] }}',
                                icon: '{{ $button['confirmIcon'] ?? 'question' }}',
                                showCancelButton: true,
                                confirmButtonText: '{{ $button['confirmText'] ?? 'Ya' }}',
                                cancelButtonText: 'Batal'
                            }).then((result) => { if (result.isConfirmed) this.form.submit(); })"
                        @endif
                    >
                        <i class="{{ $button['icon'] ?? 'fas fa-cog' }}"></i>
                        @if(isset($button['text'])) {{ $button['text'] }} @endif
                    </button>
                </form>
            @endif
        @endforeach
    @endif
</div>
