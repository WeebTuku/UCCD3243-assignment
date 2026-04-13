<?php
// Auth - CHECK FOR USER AUTHENTICATION LOGIN

// Start new session
session_start();

// if student_id is NOT set, redirect to login.php
if (!isset($_SESSION['student_id']) || !isset($_SESSION['student_name'])) {
    header("Location: login.php");
    exit();
}