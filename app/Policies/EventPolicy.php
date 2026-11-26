<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

// app/Policies/EventPolicy.php izin event untuk admin dan pemilik organizer
class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageEvents();
    }

    public function create(User $user): bool
    {
        return $user->canManageEvents();
    }

    public function view(User $user, Event $event): bool
    {
        return $user->isAdmin() || $this->owns($user, $event);
    }

    public function updateInfo(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    public function preview(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    public function update(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    public function review(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    private function owns(User $user, Event $event): bool
    {
        return $user->isEventOrganizer() && (int) $event->organizer_id === $user->id;
    }

    public function manageCategories(User $user, Event $event): bool
    {
        return $user->isAdmin() || ($this->owns($user, $event) && $event->status === EventStatus::PendingReview);
    }

    public function manageNotices(User $user, Event $event): bool
    {
        return $user->isAdmin() || ($this->owns($user, $event) && $event->status === EventStatus::Published);
    }
}
