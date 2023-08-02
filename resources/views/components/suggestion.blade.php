<div class="my-2 shadow shadow_tr text-white bg-dark p-1" id="row_{{$currentRequest->id}}">
  <div class="d-flex justify-content-between" id="">
    <table class="ms-1">
        <tr>
      <td class="align-middle">{{ $currentRequest->name }} </td>
      <td class="align-middle"> - </td>
      <td class="align-middle">{{ $currentRequest->email }}</td>
      <td class="align-middle">
    </tr>
    </table>
    <div>
      <button id="create_request_btn" data-id="{{$currentRequest->id}}" class="btn btn-primary me-1 create_request_btn row_{{$currentRequest->id}}">Connect</button>
    </div>
  </div>
</div>



