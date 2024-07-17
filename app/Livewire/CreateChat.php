<?php

namespace App\Livewire;

use App\Events\IsTyping;
use App\Events\MessageSend;
use App\Models\Message;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateChat extends Component
{
    public $users;
    public $userStatus = 'Active';
    public $sender_id;
    public $receiver_id;
    public $selected_user;
    public $search_user;
    public $userMessages;
    public $content;

    public function mount()
    {
        $this->sender_id = auth()->user()->id;

        $this->users = User::whereNot('id', auth()->user()->id)->get();

        $this->selected_user = $this->users[0];
        $this->receiver_id = $this->selected_user->id;
        $this->userMessages = Message::where(function ($q) {
            $q->where('sender_id', auth()->user()->id)
                ->where('receiver_id', $this->selected_user->id);
        })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->selected_user->id)
                    ->where('receiver_id', auth()->user()->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function updatedContent()
    {
        broadcast(new IsTyping($this->selected_user->id))->toOthers();
    }

    #[On('echo-private:is-typing.{sender_id},IsTyping')]
    public function isTyping()
    {
        $this->userStatus = 'Typing ...';
        
        $this->dispatch('is_typing');
    }

    public function selectUser($userId)
    {
        $this->selected_user = User::find($userId);

        $this->dispatch('scroll');
    }

    public function sendMessage()
    {
        Message::create([
            'sender_id' => auth()->user()->id,
            'receiver_id' => $this->selected_user->id,
            'content' => $this->content
        ]);

        $this->content = null;

        $this->dispatch('scroll');

        broadcast(new MessageSend($this->selected_user->id))->toOthers();
    }

    #[On('echo-private:send-message.{sender_id},MessageSend')]
    public function receiveMessage($payload)
    {
        $this->userMessages = Message::where(function ($q) {
            $q->where('sender_id', auth()->user()->id)
                ->where('receiver_id', $this->selected_user->id);
        })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->selected_user->id)
                    ->where('receiver_id', auth()->user()->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $this->dispatch('scroll');
    }

    public function render()
    {
        $this->users = User::whereNot('id', auth()->user()->id)->when($this->search_user, function ($q) {
            $q->where('name', 'like', "%" . $this->search_user . "%");
        })->get();

        $this->userMessages = Message::where(function ($q) {
            $q->where('sender_id', auth()->user()->id)
                ->where('receiver_id', $this->selected_user->id);
        })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->selected_user->id)
                    ->where('receiver_id', auth()->user()->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('livewire.create-chat');
    }
}
