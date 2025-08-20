<?php
require_once __DIR__.'/../core/auth.php'; logout(); header('Location: '.admin_url('login.php')); exit;
