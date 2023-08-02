<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use App\Models\FriendRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $receiverId = $request->user_id;
        
       // Create a new friend request.
        FriendRequest::create([
        'sender_id' => Auth::id(),
        'receiver_id' => $receiverId,
        ]);

        return response()->json(['success' => true, 'msg' => 'Request sent successfully!!','row_id' => $receiverId]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($type)
    {
        $userId = Auth::id();
        $loggedInUserId = $userId;
        $requestComponent = "components.request";

        if($type == 'suggestion'){
            $suggestionComponent = 'components.suggestion';
            $btnName = 'get_suggestions_btn';
            $header = 'Suggestions';

            // Get the users who are not yet connected, and have not sent or received friend requests
            
            $subquery = FriendRequest::select(DB::raw('DISTINCT
                    CASE
                        WHEN sender_id = '.$loggedInUserId.' THEN receiver_id
                        WHEN receiver_id = '.$loggedInUserId.' THEN sender_id
                    END AS friend_id'))
                ->where('sender_id', '=', $loggedInUserId)
                ->orWhere('receiver_id', '=', $loggedInUserId);

            $suggestedUsers = User::select('users.*')
                ->leftJoinSub($subquery, 'req_friends', function ($join) {
                    $join->on('users.id', '=', 'req_friends.friend_id');
                })
                ->where('users.id', '<>', $loggedInUserId)
                ->whereNotIn('users.id', function ($query) use ($loggedInUserId) {
                    $query->select(DB::raw('CASE
                                    WHEN user1_id = '.$loggedInUserId.' THEN user2_id
                                    WHEN user2_id = '.$loggedInUserId.' THEN user1_id
                                END AS friend_id'))
                        ->from('friendships')
                        ->where('user1_id', '=', $loggedInUserId)
                        ->orWhere('user2_id', '=', $loggedInUserId);
                })
                ->whereNull('req_friends.friend_id')
                ->where('users.id', '<>', $loggedInUserId)
                ->get();

            return $this->generateUsersResponse($suggestedUsers,$type,$suggestionComponent,$btnName,$header);

        }else if($type == "sent"){
            $btnName = 'get_sent_requests_btn';
            $header = 'Sent Requests';

            // Get the friend requests sent by the authenticated user
            $sentRequests = FriendRequest::with('sender')->where('sender_id', $userId)->get();
            return $this->generateUsersResponse($sentRequests,$type,$requestComponent,$btnName,$header);

        }else if($type == 'receive'){
            $btnName = 'get_received_requests_btn';
            $header = 'Received Requests';

            // Get the friend requests received by the authenticated user
            $receivedRequests = FriendRequest::where('receiver_id', $userId)->get();
            return $this->generateUsersResponse($receivedRequests,$type,$requestComponent,$btnName,$header);

        }else if($type == "connection"){
            $connectionComponent = "components.connection";
            $btnName = 'get_connections_btn';
            $header = 'Connections';

            // Get the connections for the authenticated user
            $connections = Friendship::with('user1','user2')->where(function ($query) use ($userId) {
                $query->where('user1_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('user2_id', $userId);
            })->get();
            // dd($connections);
            return $this->generateUsersResponse($connections,$type,$connectionComponent,$btnName,$header);
        }else {

            $otherUserId = $type;
            $userId = Auth::id();
            $connectionComponent = "components.connection_in_common";
            $btnName = 'get_connections_in_common';
            $header = 'Connections in common';

            // Get the connections of the authenticated user
            $authenticatedUserConnection1 = Friendship::where('user1_id', $userId)->pluck('user2_id')->toArray();
            $authenticatedUserConnection2 = Friendship::where('user2_id', $userId)->pluck('user1_id')->toArray();

            $authenticatedUserConnections = array_merge($authenticatedUserConnection1, $authenticatedUserConnection2);

            // Get the connections of the other user
            $otherUserConnections1 = Friendship::where('user1_id', $otherUserId)->pluck('user2_id')->toArray();
            $otherUserConnections2 = Friendship::where('user2_id', $otherUserId)->pluck('user1_id')->toArray();

            $otherUserConnections = array_merge($otherUserConnections1, $otherUserConnections2);

            // Find the connections in common between the authenticated user and the other user
            $connectionsInCommon = array_intersect($authenticatedUserConnections, $otherUserConnections);
           

            // Fetch the user data for connections in common
            $usersInCommon = User::whereIn('id', array_values($connectionsInCommon))->get();
            return $this->generateUsersResponse($usersInCommon,$type,$connectionComponent,$btnName,$header);
                        
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
        $userId = Auth::id();
        $friendRequest = FriendRequest::where('receiver_id', $userId)->where('id', $id)->firstOrFail();

            if ($friendRequest) {
                $userIds = [$friendRequest->sender_id, $friendRequest->receiver_id];
                // Create a friendship entry to signify both users are friends
                Friendship::create([
                'user1_id' => min($userIds),
                'user2_id' => max($userIds)
            ]);
            $friendRequest->delete();
            return response()->json(['success' => true, 'msg' => 'Request Rejected successfully!','row_id' => $id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $userId = Auth::id();
        $type = $request->user_id;
        if($type =="reject"){
            $receiver = FriendRequest::findOrFail($id);
            $friendRequest = FriendRequest::where('receiver_id',$receiver->receiver_id)->where('sender_id',$userId)->first();
            if ($friendRequest){
                $friendRequest->delete();
                return response()->json(['success' => true, 'msg' => 'Request Rejected successfully!','row_id' => $id]);
            }
        }else if($type == "remove"){

            $receiver = Friendship::findOrFail($id);
            // Remove friendship if it exists
            $friendship = Friendship::where(function ($query) use ($receiver,$userId) {
                $query->where('user1_id', $receiver->user1_id)->where('user2_id', $userId);
            })->orWhere(function ($query) use ($receiver, $userId) {
                $query->where('user1_id', $userId)->where('user2_id', $receiver->user2_id);
            })->first();
            
            if ($friendship) {
                $friendship->delete();
                return response()->json(['success' => true, 'msg' => 'Request Rejected successfully!','row_id' => $id]);
            }

        }

        return response()->json(['success' => true, 'msg' => 'Something went wrong. Please try again later!','row_id' => $id]);

    }


    public function generateUsersResponse($currentRequest,$type,$component,$btnName,$header){
        $currentRequestHtml = '';
        $count = $currentRequest->count();

        // Iterate through the sent,received,connection and render the 'components' view for each user
        foreach ($currentRequest as $currentRequest) {
            // dd($currentRequest->user2->email);
            $currentRequestHtml .= view($component, compact('currentRequest','type'))->render();
        }
        // Return a JSON response with the following data:
        return response()->json([
            'users' => $currentRequestHtml,         
            'count' => $count, 
            'btn_name' => $btnName,
            'text' => $header,            
            'type' => $type             
        ]);
    }
}
