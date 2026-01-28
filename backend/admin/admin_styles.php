<?php
// Backend Admin Pages Shared Styles
$admin_styles = <<<'CSS'
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --primary-color: #4CAF50;
        --secondary-color: #2196F3;
        --danger-color: #f44336;
        --warning-color: #ff9800;
        --success-color: #4CAF50;
        --light-gray: #f5f5f5;
        --border-color: #ddd;
        --text-color: #333;
        --light-text: #666;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--light-gray);
        color: var(--text-color);
        line-height: 1.6;
    }

    .container {
        display: grid;
        grid-template-columns: 250px 1fr;
        min-height: 100vh;
    }

    /* Sidebar Navigation */
    .sidebar {
        background: white;
        border-right: 1px solid var(--border-color);
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
    }

    .sidebar h3 {
        color: var(--primary-color);
        margin-bottom: 20px;
        font-size: 1.3rem;
    }

    .sidebar ul {
        list-style: none;
    }

    .sidebar li {
        margin-bottom: 10px;
    }

    .sidebar a {
        display: block;
        padding: 12px 15px;
        color: var(--text-color);
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s;
        border-left: 3px solid transparent;
    }

    .sidebar a:hover {
        background-color: var(--light-gray);
        border-left-color: var(--primary-color);
        color: var(--primary-color);
    }

    .sidebar a.logout-btn {
        color: var(--danger-color);
        margin-top: 20px;
    }

    /* Main Content */
    .main-content {
        padding: 30px;
    }

    .header {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .header h1 {
        font-size: 1.8rem;
        color: var(--primary-color);
    }

    .header p {
        color: var(--light-text);
        margin-top: 5px;
        font-size: 0.95rem;
    }

    /* Tables */
    .table-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
        margin-bottom: 30px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background-color: var(--primary-color);
        color: white;
    }

    th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }

    td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--border-color);
    }

    tbody tr:hover {
        background-color: var(--light-gray);
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
    }

    .badge-open {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
    }

    .badge-resolved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #28a745;
    }

    .badge-verified {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #28a745;
    }

    .badge-active {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-suspended {
        background-color: #f8d7da;
        color: #721c24;
    }

    .badge-completed {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }

    .badge-in_progress {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    /* Buttons */
    .btn {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.85rem;
        cursor: pointer;
        border: none;
        transition: all 0.3s;
        font-weight: 500;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: #45a049;
    }

    .btn-success {
        background-color: var(--success-color);
        color: white;
    }

    .btn-success:hover {
        background-color: #45a049;
    }

    .btn-warning {
        background-color: var(--warning-color);
        color: white;
    }

    .btn-warning:hover {
        background-color: #fb8500;
    }

    .btn-danger {
        background-color: var(--danger-color);
        color: white;
    }

    .btn-danger:hover {
        background-color: #d32f2f;
    }

    /* Forms */
    .form-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 25px;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-color);
        font-weight: 500;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-family: inherit;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
    }

    /* Alert Messages */
    .alert {
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        border-left: 4px solid;
    }

    .alert-success {
        background-color: #d4edda;
        border-left-color: #28a745;
        color: #155724;
    }

    .alert-warning {
        background-color: #fff3cd;
        border-left-color: #ffc107;
        color: #856404;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-left-color: #f44336;
        color: #721c24;
    }

    /* Revenue Summary */
    .summary-box {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid var(--success-color);
    }

    .summary-box h3 {
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    .summary-box p {
        font-size: 1.1rem;
        color: var(--text-color);
    }

    /* Back Link */
    .back-link {
        margin-top: 20px;
    }

    .back-link a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
    }

    .back-link a:hover {
        text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            grid-template-columns: 1fr;
        }

        .sidebar {
            position: fixed;
            left: -250px;
            height: auto;
            z-index: 1000;
            transition: left 0.3s;
        }

        table {
            font-size: 0.9rem;
        }

        th, td {
            padding: 8px 10px;
        }

        .header h1 {
            font-size: 1.3rem;
        }
    }
</style>
CSS;
