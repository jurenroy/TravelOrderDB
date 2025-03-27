<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\UnreadMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Store a new message
    public function store(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|integer',
            'receiver_id' => 'required|integer',
            'content' => 'required|string',
        ]);

        // Create a new message
        $message = Message::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);

        // Mark the message as unread for the receiver
        UnreadMessage::create([
            'name_id' => $request->receiver_id,
            'message_id' => $message->id,
        ]);

        return response()->json($message, 201);
    }

    // Get messages between two users
    public function index($user1_id, $user2_id)
    {
        // Validate the parameters
        // if (!is_numeric($user1_id) || !is_numeric($user2_id)) {
        //     return response()->json(['error' => 'Invalid user IDs'], 400);
        // }

        $messages = Message::where(function ($query) use ($user1_id, $user2_id) {
            $query->where('sender_id', $user1_id)
                  ->where('receiver_id', $user2_id);
        })->orWhere(function ($query) use ($user1_id, $user2_id) {
            $query->where('sender_id', $user2_id)
                  ->where('receiver_id', $user1_id);
        })->get();

        return response()->json($messages);
    }

    public function indexer($user_id)
    {
        // Validate the user ID
        if (!is_numeric($user_id)) {
            return response()->json(['error' => 'Invalid user ID'], 400);
        }

        // Get all messages where the user is either the sender or receiver
        $messages = Message::where('sender_id', $user_id)
            ->orWhere('receiver_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get unique users involved in the conversation
        $userIds = $messages->pluck('sender_id')->merge($messages->pluck('receiver_id'))->unique()->filter(function ($id) use ($user_id) {
            return $id != $user_id; // Exclude the current user
        });

        // Prepare unread message counts
        $unreadCounts = [];
        foreach ($userIds as $id) {
            // Get the count of unread messages for the current user
            $unreadCount = UnreadMessage::where('name_id', $user_id)
                ->whereIn('message_id', function ($query) use ($id) {
                    $query->select('id')
                          ->from('messages')
                          ->where('sender_id', $id);
                })
                ->count();

            $unreadCounts[$id] = $unreadCount;
        }

        return response()->json(
            // 'messages' => $messages, get back the bracket if u want
            // 'unread_counts' => sakat lang og gusto ka also return comma,
            $unreadCounts
        );
    }

    public function markAsRead($sender_id, $receiver_id)
    {
        // Get the message IDs where the receiver_id matches the specified user
        $messageIds = Message::where('receiver_id', $sender_id)
            ->where('sender_id', $receiver_id) // Optional: If you want to filter by sender as well
            ->pluck('id'); // Get the IDs as a collection

        

        // Retrieve the unread messages that will be deleted
        $deletedMessages = UnreadMessage::whereIn('message_id', $messageIds)
            ->get(); // Get the unread messages as a collection

        // Delete the unread messages where message_id matches the retrieved IDs
        UnreadMessage::whereIn('message_id', $messageIds)
            ->delete();

        // Return the deleted messages in the response
        return response()->json([
            'message' => 'Messages marked as read',
            'messageIds' => $messageIds,
            'deleted_messages' => $deletedMessages // Include the deleted messages
        ]);
    }

}
