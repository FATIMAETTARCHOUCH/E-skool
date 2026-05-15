<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $groups = \App\Models\Group::all();
        
        // Existing conversations
        if ($user->role === 'admin') {
            $contacts = User::where('role', 'student')
                ->where(function($q) use ($user) {
                    $q->whereHas('messagesSent', function($sq) use ($user) {
                        $sq->where('receiver_id', $user->id);
                    })
                    ->orWhereHas('messagesReceived', function($sq) use ($user) {
                        $sq->where('sender_id', $user->id);
                    });
                })->get();

            // Search for NEW students to message
            $searchStudents = collect([]);
            if ($request->filled('search') || $request->filled('group_id')) {
                $query = User::where('role', 'student');
                if ($request->filled('search')) {
                    $s = $request->search;
                    $query->where(function($q) use ($s) {
                        $q->where('first_name', 'like', "%$s%")
                          ->orWhere('last_name', 'like', "%$s%")
                          ->orWhere('massar_code', 'like', "%$s%");
                    });
                }
                if ($request->filled('group_id')) {
                    $query->where('group_id', $request->group_id);
                }
                $searchStudents = $query->take(10)->get();
            }
        } else {
            $contacts = User::where('role', 'admin')->get();
            $searchStudents = collect([]);
        }

        return view('messages.index', compact('contacts', 'searchStudents', 'groups'));
    }

    /**
     * Display the specified conversation.
     */
    public function show($id)
    {
        $user = auth()->user();
        $contact = User::findOrFail($id);

        $messages = Message::where(function($q) use ($user, $contact) {
                $q->where('sender_id', $user->id)->where('receiver_id', $contact->id);
            })
            ->orWhere(function($q) use ($user, $contact) {
                $q->where('sender_id', $contact->id)->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $contact->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', compact('contact', 'messages'));
    }

    /**
     * Send a new message.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $id,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Message sent.');
    }
}
