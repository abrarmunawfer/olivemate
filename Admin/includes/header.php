<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OliveMate - Admin Pane</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>

    <div class="d-flex">
        
        <?php include 'sidebar.php';  ?>

        <!-- Main Content Wrapper -->
        <div class="main-content flex-grow-1">
            
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
                <div class="container-fluid">
                    
                    <!-- Mobile Sidebar Toggle -->
                    <button class="btn btn-outline-secondary d-lg-none" id="sidebar-toggle-btn" type="button">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="d-none d-lg-flex align-items-center gap-4 navbar-info">
                        <!-- Welcome Message -->
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle fs-4 me-2"></i>
                            <span class="fw-bold">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                        </div>
                    </div>
                    
                    <div class="ms-auto d-none d-lg-flex align-items-center gap-4">
                        <!-- Live Clock -->
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock fs-4 me-2"></i>
                            <span id="live-clock">Loading clock...</span>
                        </div>
                        <button class="btn btn-outline-danger" id="logout-btn">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid p-4">