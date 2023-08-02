var skeletonId = 'skeleton';
var contentId = 'content';
var skipCounter = 0;
var takeAmount = 10;


//common route for all get data
function getRequests(mode) {
  $("#load_more_btn").text("Load More"); 
  ajax('requests/'+mode, 'GET', undefined);
}

function getMoreRequests(mode) {
  // Optional: Depends on how you handle the "Load more"-Functionality
  // your code here...
}

function getConnections() {
  // your code here...
}

function getMoreConnections() {
  // Optional: Depends on how you handle the "Load more"-Functionality
  // your code here...
}

function getConnectionsInCommon(userId, connectionId) {
  // your code here...
}

function getMoreConnectionsInCommon(userId, connectionId) {
  // Optional: Depends on how you handle the "Load more"-Functionality
  // your code here...
}

function getSuggestions() {
  var val = 'suggestion';
  ajax('requests/'+val, 'GET', undefined);
}

function getMoreSuggestions() {
  // Optional: Depends on how you handle the "Load more"-Functionality
  // your code here...
}

function sendRequest(url,method,userId) {
  ajaxRequest(url, method, userId);
}

function deleteRequest(userId, requestId) {
  // your code here...
}

function acceptRequest(userId, requestId) {
  // your code here...
}

function removeConnection(userId, connectionId) {
  // your code here...
}

$(function () {

  //to get send requests
  getRequests('sent');
  $('#get_sent_requests_btn').on('click',function(){
    getRequests('sent');
  });

  //to get received requests
  getRequests('receive');
  $('#get_received_requests_btn').on('click',function(){
    getRequests('receive');
  });

  //to get connections
  getRequests('connection');
  $('#get_connections_btn').on('click',function(){
    getRequests('connection');
  });

  // get suggestions
  getRequests('suggestion');
  $('#get_suggestions_btn').on('click',function(){
    getSuggestions('suggestion');
  });

  //load more
  $(document).on('click', '#load_more_btn_parent', function(e){
    e.preventDefault();
    $(".shadow_tr:hidden").slice(0,10).fadeIn("slow");

    if($(".shadow_tr:hidden").length == 0){
        $("#load_more_btn_parent").text("No more data !!!");
    }
  });

  //sent request
  $(document).on('click', '.create_request_btn', function(){
    var method = "POST";
    var suggestionId = $(this).data('id');
    var url = 'requests';
    sendRequest(url,method,suggestionId);
  });

  //cancel request
  $(document).on('click', '.cancel_request_btn', function(){
    var method = "DELETE";
    var userId = $(this).data('id');
    var url = 'requests/'+userId;
    var type = 'reject';
    sendRequest(url,method,type);
  });

  //accept request
  $(document).on('click', '.accept_request_btn', function(){
    var method = "PUT";
    var userId = $(this).data('id');
    var url = 'requests/'+userId;
    sendRequest(url,method,userId);
  });

  //remove connection
  $(document).on('click', '.remove_connection_btn', function(){
    var method = "DELETE";
    var userId = $(this).data('id');
    var url = 'requests/'+userId;
    var type = 'remove';
    sendRequest(url,method,type);
  });

  //to get common connections
  $(document).on('click', '#get_connections_in_common', function(){
    var userId = $(this).data('id');
    getRequests(userId);
  });

});