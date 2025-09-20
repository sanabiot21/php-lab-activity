<?php
session_start();

// Initialize messages array in session if not exists
if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
}

// Handle POST request to add new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['message'])) {
    $name = trim($_POST['name']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($message)) {
        $_SESSION['messages'][] = [
            'id' => count($_SESSION['messages']) + 1,
            'name' => htmlspecialchars($name),
            'message' => htmlspecialchars($message),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Redirect to prevent form resubmission
        header('Location: index.php?success=1');
        exit;
    }
}

// Get filter parameter from GET request
$filterUser = isset($_GET['user']) ? trim($_GET['user']) : '';
$showSuccess = isset($_GET['success']) && $_GET['success'] == '1';

// Filter messages if user filter is applied
$displayMessages = $_SESSION['messages'];
if (!empty($filterUser)) {
    $displayMessages = array_filter($_SESSION['messages'], function($msg) use ($filterUser) {
        return strcasecmp($msg['name'], $filterUser) === 0;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Guestbook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <h1 class="text-center mb-4">
                    <i class="bi bi-book text-primary"></i> Simple Guestbook
                </h1>

                <?php if ($showSuccess): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Your message has been added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Add Message Form -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-pencil"></i> Leave a Message</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="index.php" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" required maxlength="50">
                                <div class="invalid-feedback">
                                    Please provide your name.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="3" required maxlength="500"></textarea>
                                <div class="invalid-feedback">
                                    Please provide a message.
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Submit Message
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Messages</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="index.php" class="row g-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="user" placeholder="Enter username to filter" value="<?php echo htmlspecialchars($filterUser); ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-info me-2">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Messages Display -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-dots"></i>
                            <?php if (!empty($filterUser)): ?>
                                Messages from "<?php echo htmlspecialchars($filterUser); ?>" (<?php echo count($displayMessages); ?> found)
                            <?php else: ?>
                                All Messages (<?php echo count($displayMessages); ?> total)
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($displayMessages)): ?>
                            <div class="text-center text-muted">
                                <i class="bi bi-inbox display-4"></i>
                                <p class="mt-2">
                                    <?php if (!empty($filterUser)): ?>
                                        No messages found from "<?php echo htmlspecialchars($filterUser); ?>".
                                    <?php else: ?>
                                        No messages yet. Be the first to leave a message!
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <?php foreach (array_reverse($displayMessages) as $msg): ?>
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="bi bi-person-circle text-primary"></i>
                                                  <?php echo $msg['name']; ?>
                                            </h6>
                                            <p class="mb-1"><?php echo nl2br($msg['message']); ?></p>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> <?php echo $msg['timestamp']; ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bootstrap form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>