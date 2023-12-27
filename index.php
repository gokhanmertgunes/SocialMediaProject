<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wizzy - Social Media Platform</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
    
</head>

<body>
    <div class="wrapper">
        <header class="bg-primary">
            <div class="container d-flex justify-content-between align-items-center py-3">
                <h1 class="logo text-white">Wizzy</h1>
                <form class="login-form" action="login.php" method="POST">
                    <div class="d-flex">
                        <div class="form-group me-2 mb-0">
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        <div class="form-group me-2 mb-0">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-success" >Sign In</button>
                    </div>
                </form>
            </div>
        </header>

        <div class="container mt-4">
            <div class="row">
                <div class="col-lg-6">
                    <div class="marketing-section">
                        <h2>Connect with friends and bots around the world on Wizzy.</h2>
                        <br>
                        <h5>💀 Stay connected with friends and family.</h5>
                        <h5>📸 Share photos, videos, and updates with your network.</h5>
                        <h5>💵 Discover new people, places, and ideas.</h5>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-section">
                        <h2>Sign Up</h2>
                        <p>It looks like it is free, but we sell your data haha!</p>
                        <form action="register.php" method="POST" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col">
                                    <input type="text" class="form-control" id="firstName" name="firstName"
                                        placeholder="First Name" required>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="lastName" name="lastName"
                                        placeholder="Last Name" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Email Address" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                <label for="birthday">Birthday:</label>
                                <input type="date" class="form-control same-height" id="birthday" name="birthday" required>
                                </div>
                                <div class="col">
                                <label for="profilePicture">Profile Picture:</label>
                                <input type="file" class="form-control same-height" id="profilePicture" name="profilePicture" accept="image/png, image/gif, image/jpeg" >
                                </div>
                            </div>
                           
                           
                            <button type="submit" class="btn btn-lg btn-success">Sign Up</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <?php require_once'footer.php'; ?>

    </div>

    <script>
        let url_string = window.location.href; 
        let url = new URL(url_string);
        let errorType = url.searchParams.get("error");
        
        if(errorType === "login") {
            alert("The password that you've entered is incorrect.");
        } else if(errorType === "unauthenticated") {
            alert("You have tried to enter the system without authorization.");
        } else if(errorType === "email") {
            alert("The email you entered is already registered in the system.");
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
