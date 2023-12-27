<?php
session_start();
require_once "includes/db.php";
if (!isset($_SESSION["user"])) {
    header("Location: index.php?error=unauthenticated");
    exit;
}
$USER = getUser($_SESSION["user"]["email"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wizzy - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/home.css">
        
</head>

<body>
    <div class="wrapper">
        <header class="header bg-primary">
            <div class="container d-flex justify-content-between align-items-center py-3">
                <h1 class="logo text-white">Wizzy</h1>
                <div class="d-flex align-items-center">

                    <button class="btn btn-light me-4 dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                        <span class="badge bg-danger" id="notificationCount">0</span>
                    </button>

                    <ul class="dropdown-menu" id="notifications"></ul>

                    <a href="logout.php"><button class="btn btn-warning">Logout</button></a>
                </div>
            </div>
        </header>

        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-lg-3">
                    <div class="profile-card  text-center">
                        <img src="<?= filter_var($USER["profile_picture"], FILTER_SANITIZE_SPECIAL_CHARS) ?>"
                            alt="Profile Picture">
                        <h4 class="text-left"><b>
                                <?= filter_var($USER["name"], FILTER_SANITIZE_SPECIAL_CHARS) . " " . filter_var($USER["surname"], FILTER_SANITIZE_SPECIAL_CHARS) ?>
                            </b> 
                            <?php
                            $birthDate = $USER["birth_date"];
                            $currentDate = date('Y-m-d');
                            $age = date_diff(date_create($birthDate), date_create($currentDate))->y;

                            echo filter_var("(".$age.")", FILTER_SANITIZE_SPECIAL_CHARS);
                            ?>
                        </h4> 
                        <p>
                            <?= filter_var($USER["email"], FILTER_SANITIZE_SPECIAL_CHARS) ?>
                        </p>

                        
                    </div>

                    <div class="sidebar-section">
                        <h4>Friends</h4>
                        <br>

                        <div id="friend-list">
                            <div class="d-flex align-items-center" id="friendsLoading">
                                <strong>Loading your friends...</strong>
                                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
                            </div>

                        </div>
                    </div>

                    <div class="sidebar-section">
                        <h4>Search</h4>
                        <input type="text" class="form-control me-2" placeholder="Search Wizzy" id="search-input">
                        <br>

                        <div id="search-results">
                            <div class="d-flex align-items-center" id="searchLoading" style="visibility:hidden;">
                                <strong>Searching Wizzy...</strong>
                                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-6">
                    <div class="timeline">

                        <div class="post">
                            <div class="friend" style="text-align:left;">
                                <img width="32"
                                    src="<?= filter_var($USER["profile_picture"], FILTER_SANITIZE_SPECIAL_CHARS) ?>"
                                    style="margin-right: 5px">

                                <strong style="font-size: 20px">
                                    <?= filter_var($USER["name"], FILTER_SANITIZE_SPECIAL_CHARS) . " " . filter_var($USER["surname"], FILTER_SANITIZE_SPECIAL_CHARS) ?>
                                </strong>
                            </div>
                            <hr>
                            <form action="add_post.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <textarea class="form-control" placeholder="Share your politik-doğrucu thoughts ✍️"
                                        name="content" rows="3"></textarea>
                                    <br>

                                </div>

                                <div class="d-flex justify-content-end">
                                    <div class="post-actions">
                                        <label for="postImage" class="btn btn-primary"><i
                                                class="bi bi-image"></i></label>
                                        <input type="file" class="form-control" style="display:none; " id="postImage"
                                            name="postImage" accept="image/png, image/gif, image/jpeg">
                                    </div>

                                    <div class="justify-content-end">
                                        <button class="btn btn-primary text-white"><b>Share</b>
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>

                        <div id="posts">
                            <div class="d-flex">
                                <strong>Loading timeline...</strong>
                                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="assets/js/app.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>