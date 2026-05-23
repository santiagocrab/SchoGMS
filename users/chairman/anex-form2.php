<?php
/**
 * Legacy Annex 7 review URL — redirects to File groups (same review workflow).
 */
include 'config/session.php';

header('Location: annex7.php?status=pending');
exit;
