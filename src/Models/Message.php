<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Message Model
 * TLOS - The Last of SaaS
 *
 * Handles bidirectional messaging between sponsors and companies
 * within the context of an event. Supports:
 * - 1 initial message per sender-recipient pair per event
 * - Unlimited replies once a conversation is started
 * - Personal contact details attached to messages
 */
class Message extends Model
{
    protected string $table = 'messages';

    protected array $fillable = [
        'event_id',
        'sender_type',
        'sender_id',
        'recipient_type',
        'recipient_id',
        'parent_message_id',
        'message',
        'sender_name',
        'sender_email',
        'sender_phone',
        'sent_at',
        'read_at',
    ];

    /**
     * Check if a user can send an initial message to a recipient in an event
     * Returns true if no initial message exists yet, or if there's an active conversation
     */
    public function canSendMessage(
        int $eventId,
        string $senderType,
        int $senderId,
        string $recipientType,
        int $recipientId
    ): array {
        // Check if sender has already sent an initial message to this recipient
        $sql = "SELECT id FROM {$this->table}
                WHERE event_id = ?
                  AND sender_type = ?
                  AND sender_id = ?
                  AND recipient_type = ?
                  AND recipient_id = ?
                  AND parent_message_id IS NULL
                LIMIT 1";

        $existing = $this->db->fetch($sql, [
            $eventId,
            $senderType,
            $senderId,
            $recipientType,
            $recipientId
        ]);

        if ($existing) {
            return [
                'can_send' => false,
                'reason' => 'already_sent',
                'message' => 'Ya has enviado un mensaje a este destinatario en este evento.'
            ];
        }

        // Check if recipient has sent a message to sender (can reply)
        $sql = "SELECT id FROM {$this->table}
                WHERE event_id = ?
                  AND sender_type = ?
                  AND sender_id = ?
                  AND recipient_type = ?
                  AND recipient_id = ?
                LIMIT 1";

        $hasReceivedMessage = $this->db->fetch($sql, [
            $eventId,
            $recipientType,
            $recipientId,
            $senderType,
            $senderId
        ]);

        return [
            'can_send' => true,
            'is_reply' => (bool)$hasReceivedMessage,
            'existing_message_id' => $hasReceivedMessage['id'] ?? null
        ];
    }

    /**
     * Send a message
     */
    public function sendMessage(
        int $eventId,
        string $senderType,
        int $senderId,
        string $recipientType,
        int $recipientId,
        string $message,
        ?string $senderName = null,
        ?string $senderEmail = null,
        ?string $senderPhone = null,
        ?int $parentMessageId = null
    ): int|false {
        // Verify sender can send
        $check = $this->canSendMessage($eventId, $senderType, $senderId, $recipientType, $recipientId);

        if (!$check['can_send']) {
            return false;
        }

        // If this is a reply context, link to the conversation
        if ($check['is_reply'] && !$parentMessageId && $check['existing_message_id']) {
            $parentMessageId = $check['existing_message_id'];
        }

        return $this->create([
            'event_id' => $eventId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'recipient_type' => $recipientType,
            'recipient_id' => $recipientId,
            'parent_message_id' => $parentMessageId,
            'message' => $message,
            'sender_name' => $senderName,
            'sender_email' => $senderEmail,
            'sender_phone' => $senderPhone,
        ]);
    }

    /**
     * Reply to a message
     * This allows continuing a conversation even after initial message limit
     */
    public function replyToMessage(
        int $originalMessageId,
        string $message,
        ?string $senderName = null,
        ?string $senderEmail = null,
        ?string $senderPhone = null
    ): int|false {
        $original = $this->find($originalMessageId);

        if (!$original) {
            return false;
        }

        // Reply goes in the opposite direction
        return $this->create([
            'event_id' => $original['event_id'],
            'sender_type' => $original['recipient_type'],
            'sender_id' => $original['recipient_id'],
            'recipient_type' => $original['sender_type'],
            'recipient_id' => $original['sender_id'],
            'parent_message_id' => $originalMessageId,
            'message' => $message,
            'sender_name' => $senderName,
            'sender_email' => $senderEmail,
            'sender_phone' => $senderPhone,
        ]);
    }

    /**
     * Get conversation between two parties in an event
     */
    public function getConversation(
        int $eventId,
        string $partyAType,
        int $partyAId,
        string $partyBType,
        int $partyBId
    ): array {
        $sql = "SELECT m.*
                FROM {$this->table} m
                WHERE m.event_id = ?
                  AND (
                    (m.sender_type = ? AND m.sender_id = ? AND m.recipient_type = ? AND m.recipient_id = ?)
                    OR
                    (m.sender_type = ? AND m.sender_id = ? AND m.recipient_type = ? AND m.recipient_id = ?)
                  )
                ORDER BY m.sent_at ASC";

        return $this->db->fetchAll($sql, [
            $eventId,
            $partyAType, $partyAId, $partyBType, $partyBId,
            $partyBType, $partyBId, $partyAType, $partyAId
        ]);
    }

    /**
     * Get all messages received by a user
     */
    public function getReceivedMessages(
        int $eventId,
        string $recipientType,
        int $recipientId
    ): array {
        $sql = "SELECT m.*,
                       CASE
                           WHEN m.sender_type = 'sponsor' THEN s.name
                           WHEN m.sender_type = 'company' THEN c.name
                       END as sender_org_name,
                       CASE
                           WHEN m.sender_type = 'sponsor' THEN s.logo_url
                           WHEN m.sender_type = 'company' THEN c.logo_url
                       END as sender_logo
                FROM {$this->table} m
                LEFT JOIN sponsors s ON m.sender_type = 'sponsor' AND m.sender_id = s.id
                LEFT JOIN companies c ON m.sender_type = 'company' AND m.sender_id = c.id
                WHERE m.event_id = ?
                  AND m.recipient_type = ?
                  AND m.recipient_id = ?
                ORDER BY m.sent_at DESC";

        return $this->db->fetchAll($sql, [$eventId, $recipientType, $recipientId]);
    }

    /**
     * Get all messages sent by a user
     */
    public function getSentMessages(
        int $eventId,
        string $senderType,
        int $senderId
    ): array {
        $sql = "SELECT m.*,
                       CASE
                           WHEN m.recipient_type = 'sponsor' THEN s.name
                           WHEN m.recipient_type = 'company' THEN c.name
                       END as recipient_org_name,
                       CASE
                           WHEN m.recipient_type = 'sponsor' THEN s.logo_url
                           WHEN m.recipient_type = 'company' THEN c.logo_url
                       END as recipient_logo
                FROM {$this->table} m
                LEFT JOIN sponsors s ON m.recipient_type = 'sponsor' AND m.recipient_id = s.id
                LEFT JOIN companies c ON m.recipient_type = 'company' AND m.recipient_id = c.id
                WHERE m.event_id = ?
                  AND m.sender_type = ?
                  AND m.sender_id = ?
                ORDER BY m.sent_at DESC";

        return $this->db->fetchAll($sql, [$eventId, $senderType, $senderId]);
    }

    /**
     * Get inbox grouped by conversation
     */
    public function getInbox(
        int $eventId,
        string $userType,
        int $userId
    ): array {
        // Get unique conversations where user is either sender or recipient
        $sql = "SELECT
                    CASE
                        WHEN m.sender_type = ? AND m.sender_id = ? THEN m.recipient_type
                        ELSE m.sender_type
                    END as other_party_type,
                    CASE
                        WHEN m.sender_type = ? AND m.sender_id = ? THEN m.recipient_id
                        ELSE m.sender_id
                    END as other_party_id,
                    MAX(m.sent_at) as last_message_at,
                    SUM(CASE WHEN m.recipient_type = ? AND m.recipient_id = ? AND m.read_at IS NULL THEN 1 ELSE 0 END) as unread_count
                FROM {$this->table} m
                WHERE m.event_id = ?
                  AND (
                    (m.sender_type = ? AND m.sender_id = ?)
                    OR
                    (m.recipient_type = ? AND m.recipient_id = ?)
                  )
                GROUP BY other_party_type, other_party_id
                ORDER BY last_message_at DESC";

        $conversations = $this->db->fetchAll($sql, [
            $userType, $userId,
            $userType, $userId,
            $userType, $userId,
            $eventId,
            $userType, $userId,
            $userType, $userId
        ]);

        // Enrich with party details
        foreach ($conversations as &$conv) {
            if ($conv['other_party_type'] === 'sponsor') {
                $sponsor = $this->db->fetch("SELECT id, name, logo_url FROM sponsors WHERE id = ?", [$conv['other_party_id']]);
                $conv['other_party_name'] = $sponsor['name'] ?? 'Sponsor desconocido';
                $conv['other_party_logo'] = $sponsor['logo_url'] ?? null;
            } else {
                $company = $this->db->fetch("SELECT id, name, logo_url FROM companies WHERE id = ?", [$conv['other_party_id']]);
                $conv['other_party_name'] = $company['name'] ?? 'Empresa desconocida';
                $conv['other_party_logo'] = $company['logo_url'] ?? null;
            }

            // Get last message preview
            $lastMessage = $this->db->fetch(
                "SELECT message, sender_type, sender_id FROM {$this->table}
                 WHERE event_id = ?
                   AND (
                     (sender_type = ? AND sender_id = ? AND recipient_type = ? AND recipient_id = ?)
                     OR
                     (sender_type = ? AND sender_id = ? AND recipient_type = ? AND recipient_id = ?)
                   )
                 ORDER BY sent_at DESC LIMIT 1",
                [
                    $eventId,
                    $userType, $userId, $conv['other_party_type'], $conv['other_party_id'],
                    $conv['other_party_type'], $conv['other_party_id'], $userType, $userId
                ]
            );
            $conv['last_message_preview'] = mb_substr($lastMessage['message'] ?? '', 0, 100);
            $conv['last_message_is_mine'] = ($lastMessage['sender_type'] === $userType && $lastMessage['sender_id'] == $userId);
        }

        return $conversations;
    }

    /**
     * Mark message as read
     */
    public function markAsRead(int $messageId): bool
    {
        return $this->update($messageId, ['read_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Mark all messages in a conversation as read
     */
    public function markConversationAsRead(
        int $eventId,
        string $recipientType,
        int $recipientId,
        string $senderType,
        int $senderId
    ): bool {
        $sql = "UPDATE {$this->table}
                SET read_at = NOW()
                WHERE event_id = ?
                  AND recipient_type = ?
                  AND recipient_id = ?
                  AND sender_type = ?
                  AND sender_id = ?
                  AND read_at IS NULL";

        $this->db->query($sql, [$eventId, $recipientType, $recipientId, $senderType, $senderId]);
        return true;
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(int $eventId, string $recipientType, int $recipientId): int
    {
        $sql = "SELECT COUNT(*) as count
                FROM {$this->table}
                WHERE event_id = ?
                  AND recipient_type = ?
                  AND recipient_id = ?
                  AND read_at IS NULL";

        $result = $this->db->fetch($sql, [$eventId, $recipientType, $recipientId]);
        return (int)($result['count'] ?? 0);
    }

    /**
     * Check if there's an existing conversation between two parties
     */
    public function hasConversation(
        int $eventId,
        string $partyAType,
        int $partyAId,
        string $partyBType,
        int $partyBId
    ): bool {
        $sql = "SELECT 1 FROM {$this->table}
                WHERE event_id = ?
                  AND (
                    (sender_type = ? AND sender_id = ? AND recipient_type = ? AND recipient_id = ?)
                    OR
                    (sender_type = ? AND sender_id = ? AND recipient_type = ? AND recipient_id = ?)
                  )
                LIMIT 1";

        $result = $this->db->fetch($sql, [
            $eventId,
            $partyAType, $partyAId, $partyBType, $partyBId,
            $partyBType, $partyBId, $partyAType, $partyAId
        ]);

        return (bool)$result;
    }

    /**
     * Get message with sender details
     */
    public function getMessageWithDetails(int $messageId): ?array
    {
        $sql = "SELECT m.*,
                       CASE
                           WHEN m.sender_type = 'sponsor' THEN s.name
                           WHEN m.sender_type = 'company' THEN c.name
                       END as sender_org_name,
                       CASE
                           WHEN m.sender_type = 'sponsor' THEN s.logo_url
                           WHEN m.sender_type = 'company' THEN c.logo_url
                       END as sender_logo,
                       CASE
                           WHEN m.recipient_type = 'sponsor' THEN sr.name
                           WHEN m.recipient_type = 'company' THEN cr.name
                       END as recipient_org_name,
                       CASE
                           WHEN m.recipient_type = 'sponsor' THEN sr.logo_url
                           WHEN m.recipient_type = 'company' THEN cr.logo_url
                       END as recipient_logo
                FROM {$this->table} m
                LEFT JOIN sponsors s ON m.sender_type = 'sponsor' AND m.sender_id = s.id
                LEFT JOIN companies c ON m.sender_type = 'company' AND m.sender_id = c.id
                LEFT JOIN sponsors sr ON m.recipient_type = 'sponsor' AND m.recipient_id = sr.id
                LEFT JOIN companies cr ON m.recipient_type = 'company' AND m.recipient_id = cr.id
                WHERE m.id = ?";

        return $this->db->fetch($sql, [$messageId]) ?: null;
    }
}
