<?php

require __DIR__ . '/config/session.php';
require_once __DIR__ . '/../../inc/schogms_ched_masterlist_upload.php';

schogms_ched_masterlist_handle_json_upload($conn, 'tes', ['chairman']);
