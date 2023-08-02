
<div class="my-2 shadow text-white bg-dark p-1" id="row_{{$currentRequest->id}}">
  <div class="d-flex justify-content-between">
    <table class="ms-1">
      @if ($type == 'sent')
      <td class="align-middle">{{ $currentRequest->receiver->name }}</td>
      <td class="align-middle"> - </td>
      <td class="align-middle">{{ $currentRequest->receiver->email }}</td>
      <td class="align-middle">
      @else
        <td class="align-middle">{{ $currentRequest->sender->name }}</td>
        <td class="align-middle"> - </td>
        <td class="align-middle">{{ $currentRequest->sender->email }}</td>
        <td class="align-middle">
      @endif
    </table>
    <div>
      @if ($type == 'sent')
        <button id="cancel_request_btn" data-id="{{$currentRequest->id}}" class="btn btn-danger me-1 cancel_request_btn"
          onclick="">Withdraw Request</button>
      @else
        <button id="accept_request_btn" data-id="{{$currentRequest->id}}" class="btn btn-primary me-1 accept_request_btn"
          onclick="">Accept</button>
      @endif
    </div>
  </div>
</div>
