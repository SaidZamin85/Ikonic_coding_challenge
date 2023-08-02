<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Friendship extends Model
{
    use HasFactory;
    protected $fillable = ['user1_id','user2_id'];

    public function user1()
    {
        // for receiver user
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        // for sender user
        return $this->belongsTo(User::class, 'user2_id');
    }

    //get common connection count 
    public function commonCount($userId){
        $authenticatedUser = User::find($userId);
        $suggestedUsers = User::where('id', '!=', $userId)
            ->whereDoesntHave('sentFriendRequests', function ($query) use ($userId) {
                $query->where('receiver_id', $userId);
            })
            ->whereDoesntHave('receivedFriendRequests', function ($query) use ($userId) {
                $query->where('sender_id', $userId);
            })
            ->get();

        $mutualConnections = collect();

        foreach ($suggestedUsers as $user) {
            $mutualFriends = $user->friends->intersect($authenticatedUser->friends);
            if ($mutualFriends->isNotEmpty()) {
                $user->mutualFriends = $mutualFriends;
                $mutualConnections->push($user);
            }
        }

        return $mutualConnections->count();

        }
}
