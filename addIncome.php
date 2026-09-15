<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: logIn.php');
        exit;
    }

    try {
		require_once __DIR__.'/config/database.php';

        $userId = $_SESSION['user_id'];

        $query = $db->prepare(
            'SELECT id, name
            FROM incomes_category_assigned_to_users
            WHERE user_id = :user_id
            ORDER BY id'
        );

        $query->execute([
            ':user_id' => $userId
        ]);

        $categories = $query->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = $_POST['amount'];
            $date = $_POST['date'];
            $categoryId = $_POST['category'];
            $comment = $_POST['comment'];

            $query = $db->prepare(
            'INSERT INTO incomes (
                user_id,
                income_category_assigned_to_user_id,
                amount,
                date_of_income,
                income_comment
            )
            VALUES (
                :user_id,
                :category_id,
                :amount,
                :date,
                :comment
            )'
        );

            $query->execute([
                ':user_id' => $userId,
                ':category_id' => $categoryId,
                ':amount' => $amount,
                ':date' => $date,
                ':comment' => $comment
            ]);

            $_SESSION['added_income_toast'] = 'Income added!';

            header('Location: addIncome.php');
            exit;
        }
    } 
    catch (PDOException $error){
		error_log($error->getMessage());

        $_SESSION['server_error'] = 'Server error. Please try again later.';
        exit;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Manager - Add Income</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header class="py-3">
        <div class="container-fluid header-container d-flex align-items-center justify-content-between">
            <a href="homePage.php" class="logo">
                <img src="assets/images/piggy-bank-icon.svg" height="25" alt="">
                <span class="h6">Budget <br>Manager</span>   
            </a>
            <nav class="site-nav navbar-expand-md">
                <button class="open-menu" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <img src="assets/images/navbar/menu.svg" alt="Open mobile menu">
                </button>
                <div class="offcanvas offcanvas-end offcanvas-md" tabindex="-1" id="offcanvasRight">
                    <div class="offcanvas-header">
                        <div class="logo">
                            <img src="assets/images/piggy-bank-icon.svg" height="25" alt="">
                            <span class="h6">Budget <br>Manager</span>   
                        </div> 
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="nav-menu">
                            <li class="nav-item">
                                <a class="navbar-button active" href="addIncome.php">
                                    <img src="assets/images/navbar/dollar-sign.svg" height="16" alt="">
                                    <span>Add Income</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="navbar-button" href="addExpense.php">
                                    <img src="assets/images/navbar/shopping-cart.svg" height="16" alt="">
                                    <span>Add Expense</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="navbar-button" href="viewBalance.php">
                                    <img src="assets/images/navbar/pie-chart.svg" height="16" alt="">
                                    <span>View Balance</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="navbar-button" href="#">
                                    <img src="assets/images/navbar/settings.svg" height="16" alt="">
                                    <span>Settings</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="navbar-button" href="logOut.php">
                                    <img src="assets/images/navbar/log-out.svg" height="16" alt="">
                                    <span>Log out</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav> 
        </div>
    </header>
    <div class="user-bar">
        <div class="container-fluid header-container">
            <div class="user-bar-text mt-1">
                <img src="assets/images/forms/person-icon.svg" alt="User icon">
                <span>User: <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></span>
            </div>
        </div>
    </div>
    <main class="container main-centered position-relative">
        <?php if (isset($_SESSION['added_income_toast'])): ?>
            <div class="toast-container position-absolute top-0 end-0 p-3">
                <div id="addedIncomeToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?= isset($_SESSION['added_income_toast']) ? htmlspecialchars($_SESSION['added_income_toast']) : '' ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const toastElement = document.getElementById('addedIncomeToast');
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                });
            </script>
            <?php
                unset($_SESSION['added_income_toast']);
                endif;
            ?>
        <section class="container main-centered">
            <div class="add-income-section" >
                <h2 id="formTitle" class="form-header h4">Adding new income</h2> 
                <form class="form-card" method="post"> 
                    <div class="description">
                        <p>Enter data for new income</p>
                    </div>
                    <div class="fields">
                        <div class="container field-control">
                            <div class="row input-wrapper">
                                <span class="col-2 input-symbol">
                                    <img src="assets/images/forms/dollar-sign.svg" alt="">
                                </span>
                                <label for="amount" class="visually-hidden">Amount</label>
                                <input id="amount" name="amount" class="col field" type="text" step="0.01" placeholder="Amount" inputmode="decimal" required>
                                <img class="error-icon" src="assets/images/forms/validate-icon.svg" alt="">
                            </div>
                        </div>
                        <div class="container field-control">
                            <div class="row input-wrapper">
                                <span class="col-2 input-symbol">
                                    <img src="assets/images/forms/calendar.svg" alt="">
                                </span>
                                <label for="expenseDate" class="visually-hidden">Date</label>
                                <input id="expenseDate" name="date" class="col field" type="date" required>
                            </div>
                        </div>
                        <div class="container field-control">
                            <div class="row input-wrapper">
                                <span class="col-2 input-symbol">
                                    <img src="assets/images/forms/layers.svg" alt="">
                                </span>
                                <label for="category" class="visually-hidden">Category</label>
                                <select id="category" name="category" class="col field select-field" required>
                                    <option value="" selected disabled hidden>Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" class="dropdown-item">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="container field-control">
                            <div class="row input-wrapper">
                                <span class="col-2 input-symbol">
                                    <img src="assets/images/forms/message-square.svg" alt="">
                                </span>
                                <label for="comment" class="visually-hidden">Comment</label>
                                <input id="comment" name="comment" class="col field" type="text" placeholder="Comment" required>
                                <img class="error-icon" src="assets/images/forms/validate-icon.svg" alt="">
                            </div>
                        </div>
                        <button class="button-primary submit addExpense" type="submit">Add income</button>
                        <a class="button-outline" href="homePage.php">Cancel</a>
                    </div>
                </form>
            </div>
        </section> 
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="assets/js/validation.js"></script>
    <script src="assets/js/modals.js"></script>
    <script src="assets/js/balance.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>