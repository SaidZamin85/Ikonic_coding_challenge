function ajaxForm(formItems) {
  var form = new FormData();
  formItems.forEach(formItem => {
    form.append(formItem[0], formItem[1]);
  });
  return form;
}



/**
 * 
 * @param {*} url route
 * @param {*} method POST or GET 
 * @param {*} functionsOnSuccess Array of functions that should be called after ajax
 * @param {*} form for POST request
 */
function ajax(url, method, functionsOnSuccess, form) {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  if (typeof form === 'undefined') {
    form = new FormData;
  }

  if (typeof functionsOnSuccess === 'undefined') {
    functionsOnSuccess = [];
  }

  $.ajax({
    url: url,
    type: method,
    async: true,
    data: form,
    processData: false,
    contentType: false,
    dataType: 'json',
    beforeSend: function(){
      $('#skeleton').removeClass('d-none');
      $('#content').addClass('d-none');
    },

    success: function(response) {
      $('.more_btn').val(response.type);
        $('#'+response.btn_name).html(response.text+' ('+response.count+')');
        $('#skeleton').addClass('d-none');
        $('#content').html(response.users);
        $('#content').removeClass('d-none');
        $(".shadow_tr").slice(0,10).show();
    },
    error: function(xhr, textStatus, error) {
      console.log(xhr.responseText);
      console.log(xhr.statusText);
      console.log(textStatus);
      console.log(error);
    }
  });
}


function exampleUseOfAjaxFunction(exampleVariable) {
  // show skeletons
  // hide content

  var form = ajaxForm([
    ['exampleVariable', exampleVariable],
  ]);

  var functionsOnSuccess = [
    [exampleOnSuccessFunction, [exampleVariable, 'response']]
  ];

  // POST 
  ajax('/example_route', 'POST', functionsOnSuccess, form);

  // GET
  ajax('/example_route/' + exampleVariable, 'POST', functionsOnSuccess);
}


function ajaxRequest(url, method, type){
  
  $.ajax({
    dataType: 'json',
    url: url,
    method: method,
    async: true,
    data: {user_id: type},
    beforeSend: function(){
      // $('#skeleton').removeClass('d-none');
      // $('#content').addClass('d-none');
    },
    success: function(response) {
      // $('#skeleton').addClass('d-none');
      if(response.success){
          $('#row_'+response.row_id).remove();
      }
    },
  });
}

function exampleOnSuccessFunction(exampleVariable, response) {
  // hide skeletons
  // show content

  console.log(exampleVariable);
  console.table(response);
  $('#content').html(response['content']);
}