<?php
/**
 * Admin Edit Button Partial
 * Shows a floating edit button for admin users
 * We're Sinapsis CMS
 *
 * Usage: Controllers should pass 'adminEditUrl' in the view data
 * Example: $data['adminEditUrl'] = '/admin/pages/5/edit';
 */

// Check if user is logged in as admin
$isAdmin = false;
if (!empty($_SESSION['user_id'])) {
    // Quick check - we assume if user_id is set, they're admin
    // (Only admins can log in to this system)
    $isAdmin = true;
}

// Only show if admin and edit URL is provided
if (!$isAdmin || empty($adminEditUrl)) {
    return;
}
?>

<div class="admin-edit-fab" id="adminEditFab">
    <a href="<?= htmlspecialchars($adminEditUrl) ?>" class="admin-edit-btn" title="Editar en admin" target="_blank">
        <i class="fas fa-pencil-alt"></i>
    </a>
</div>

<style>
.admin-edit-fab {
    position: fixed;
    bottom: 100px;
    left: 20px;
    z-index: 9999;
}

.admin-edit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #215A6B 0%, #1a4654 100%);
    color: white;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(33, 90, 107, 0.4);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 18px;
}

.admin-edit-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(33, 90, 107, 0.5);
    color: white;
}

.admin-edit-btn:active {
    transform: scale(0.95);
}

/* Responsive - move up on mobile to avoid other floating elements */
@media (max-width: 768px) {
    .admin-edit-fab {
        bottom: 80px;
        left: 16px;
    }

    .admin-edit-btn {
        width: 44px;
        height: 44px;
        font-size: 16px;
    }
}
</style>
