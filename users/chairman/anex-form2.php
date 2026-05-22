<?php
/**
 * Legacy Annex 7 review URL — redirects to File groups (same review workflow).
 */
include 'config/session.php';

header('Location: file_groups.php?status=pending');
exit;
