<div class="my-2 shadow text-white bg-dark p-1" id="row_{{$currentRequest->id}}">
  <div class="d-flex justify-content-between">
    <table class="ms-1">
      <td class="align-middle">{{ $currentRequest->user1_id != auth()->id() ? $currentRequest->user1->name : $currentRequest->user2->name }}</td>
      <td class="align-middle"> - </td>
      <td class="align-middle">{{ $currentRequest->user1_id != auth()->id() ? $currentRequest->user1->email : $currentRequest->user2->email }}</td>
      <td class="align-middle">
    </table>
    <div>
      @php
    $commonCount = $currentRequest->commonCount($currentRequest->user1->id);
    @endphp

    <button style="width: 220px" id="get_connections_in_common" data-id="{{ $currentRequest->user1->id }}" class="btn btn-primary" type="button"
            data-bs-toggle="collapse" data-bs-target="#collapse_{{ $currentRequest->id }}" aria-expanded="false" aria-controls="collapseExample"
            {{ $commonCount === 0 ? 'disabled' : '' }}>
            Connections in common ({{ $commonCount }})
    </button>
      <button id="remove_connection_btn" data-id="{{$currentRequest->id}}" class="btn btn-danger me-1 remove_connection_btn">Remove Connection</button>
    </div>

  </div>
  <div class="collapse" id="collapse_{{$currentRequest->id}}">

    <div id="content_{{$currentRequest->id}}" class="p-2">
      {{-- Display data here --}}
      {{-- <x-connection_in_common /> --}}
    </div>
    <div id="connections_in_common_skeletons_">
      {{-- Paste the loading skeletons here via Jquery before the ajax to get the connections in common --}}
    </div>
    <div class="d-flex justify-content-center w-100 py-2">
      {{-- <button class="btn btn-sm btn-primary" id="load_more_connections_in_common_">Load
        more</button> --}}
    </div>
  </div>
</div>
