<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: logIn.php');
        exit;
    }

    try {
		require_once __DIR__.'/config/database.php';

        $userId = $_SESSION['user_id'];

        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        if (!empty($_GET['period'])){
            if ($_GET['period'] === 'current'){
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
            }
             
            if ($_GET['period'] === 'previous'){
                $startDate = date('Y-m-d', strtotime('first day of previous month'));
                $endDate = date('Y-m-d', strtotime('last day of previous month'));
            }
        }

        if (!empty($_GET['startDate']) && !empty($_GET['endDate'])){
            $startDate = $_GET['startDate'];
            $endDate = $_GET['endDate'];
        }

        $query = $db->prepare(
            'SELECT i.id, i.amount, i.date_of_income, i.income_comment, ic.name as income_category_name
            FROM incomes i
            JOIN incomes_category_assigned_to_users ic
                ON i.income_category_assigned_to_user_id = ic.id
            WHERE i.user_id = :user_id
                AND date_of_income BETWEEN :start_date AND :end_date
            ORDER BY i.id'
        );

        $query->execute([
            ':user_id' => $userId,
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);

        $incomes = $query->fetchAll();

        $incomesByCategory = [];

        foreach ($incomes as $income) {
            $category = $income['income_category_name'];

            if (!isset($incomesByCategory[$category])) {
                $incomesByCategory[$category] = [
                    'total' => 0,
                    'transactions' => []
                ];
            }

            $incomesByCategory[$category]['total'] += $income['amount'];
            $incomesByCategory[$category]['transactions'][] = $income;
        }


        $query = $db->prepare(
            'SELECT e.id, e.amount, e.date_of_expense, e.expense_comment, ec.name as expense_category_name, pm.name AS payment_method_name
            FROM expenses e
            JOIN expenses_category_assigned_to_users ec
                ON e.expense_category_assigned_to_user_id = ec.id
            JOIN payment_methods_assigned_to_users pm
                ON e.payment_method_assigned_to_user_id = pm.id
            WHERE e.user_id = :user_id
                AND date_of_expense BETWEEN :start_date AND :end_date
            ORDER BY e.id'
        );

        $query->execute([
            ':user_id' => $userId,
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);

        $expenses = $query->fetchAll();

        $expensesByCategory = [];

        foreach ($expenses as $expense) {
            $category = $expense['expense_category_name'];

            if (!isset($expensesByCategory[$category])) {
                $expensesByCategory[$category] = [
                    'total' => 0,
                    'transactions' => []
                ];
            }

            $expensesByCategory[$category]['total'] += $expense['amount'];
            $expensesByCategory[$category]['transactions'][] = $expense;
        }

    } 
    catch (PDOException $error){
		error_log($error->getMessage());

        $_SESSION['server_error'] = 'Server error. Please try again later.';
        echo $error->getMessage();
        exit;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Manager - View Balance</title>
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
                <div class="offcanvas offcanvas-end offcanvas-md" tabindex="-1" id="offcanvasRight" role="dialog">
                    <div class="offcanvas-header">
                        <div class="logo">
                            <img src="assets/images/piggy-bank-icon.svg" height="25" alt="">
                            <p class="h6">Budget <br>Manager</p>   
                        </div> 
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="nav-menu">
                            <li class="nav-item">
                                <a class="navbar-button" href="#">
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
                                <a class="navbar-button active" href="#">
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
    <main class="balance-main">
        <section class="balance-section">
            <h1 class="text-center text-white">Current month 
                <button class="open-menu" type="button" data-bs-toggle="modal" data-bs-target="#calendarModal">
                    <img src="assets/images/forms/calendar.svg" alt="Select dates period">
                </button>
            </h1>
            <div class="modal fade" id="calendarModal" tabindex="-1"  role="dialog" aria-labelledby="calendarModalLabel" aria-hidden="true"> 
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header form-header">
                            <h2 id="calendarModalLabel" class="h4">Select a dates range</h2>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="GET" class="modal-body">
                            <button type="submit" name="period" value="current" class="btn button-primary">Current month</button>
                            <button type="submit" name="period" value="previous" class="btn button-primary">Previous month</button>
                        </form>
                        <form method="GET" class="container d-flex flex-column justify-content-start p-3">
                            <h3 class="h6 m-0">Custom period</h3>
                            <div class="row input-wrapper start-date p-3">
                                <span>Start date:</span>
                                <label for="periodStartDate" class="visually-hidden">Date</label>
                                <input id="periodStartDate" name="startDate" class="col field" type="date" required>
                            </div>
                            <div class="row input-wrapper end-date p-3">
                                <span>End date:</span>
                                <label for="periodEndDate" class="visually-hidden">Date</label>
                                <input id="periodEndDate" name="endDate" class="col field" type="date" required>
                            </div>
                            <button type="submit" class="btn button-primary">Save custom period</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <section class="col-12 col-lg-6 incomes-section" aria-labelledby="incomes-heading">
                    <h2 id="incomes-heading" class="form-header h4">Incomes</h2> 
                    <div class="form-card">
                        <div id="incomes">
                            <ul class="incomes-list">
                                
                            <?php foreach ($incomesByCategory as $categoryName => $category): ?>
                                <li class="transactions-category">
                                    <div class="category-header d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-1" data-category="<?= htmlspecialchars($categoryName) ?>"><?= htmlspecialchars($categoryName) ?></span>
                                        <span class="h5 mb-1" data-amount="<?= $category['total'] ?>"><?= number_format($category['total'], 2, '.', '') ?></span>
                                    </div>
                                    <ul class="transactions-list">

                                    <?php foreach ($category['transactions'] as $income): ?>
                                        <li class="transaction-item d-flex justify-content-between"
                                        data-comment="<?= htmlspecialchars($income['income_comment']) ?>"
                                        data-date="<?= htmlspecialchars($income['date_of_income']) ?>"
                                        data-amount="<?= htmlspecialchars($income['amount']) ?>"
                                        data-category="<?= htmlspecialchars($income['income_category_name']) ?>">
                                            <div>
                                                <span class="transaction-comment"><?= htmlspecialchars($income['income_comment']) ?></span><br>
                                                <small class="text-body-secondary transaction-date"><?= date('d.m.Y',strtotime($income['date_of_income'])) ?></small>
                                            </div>
                                            <div class="text-end">
                                                <span class="text transaction-amount"><?= number_format($income['amount'], 2, '.', '') ?></span><br>
                                                <button class="open-menu" type="button" data-bs-toggle="modal" data-bs-target="#editIncomeModal">
                                                    <img src="assets/images/forms/edit.svg" height="15" alt="">
                                                </button>
                                                <button class="open-menu" type="button" data-bs-toggle="modal" data-bs-target="#removeModal">
                                                    <img src="assets/images/forms/trash-bin.svg" height="15" alt="">
                                                </button> 
                                            </div>
                                        </li>
                                    <?php endforeach; ?>

                                    </ul>
                                </li>
                            <?php endforeach; ?>

                            </ul>
                        </div>
                    </div>
                </section>
                <section class="col-12 col-lg-6 expenses-section" aria-labelledby="expenses-heading">
                    <h2 id="expenses-heading" class="form-header h4">Expenses</h2> 
                    <div class="form-card"> 
                        <div id="expenses">
                            <ul class="expenses-list">
                            
                            <?php foreach ($expensesByCategory as $categoryName => $category): ?>
                                <li class="transactions-category">
                                    <div class="category-header d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-1" data-category="<?= htmlspecialchars($categoryName) ?>"><?= htmlspecialchars($categoryName) ?></span>
                                        <span class="h5 mb-1 text-danger" data-amount="<?= $category['total'] ?>">-<?= number_format($category['total'], 2, '.', '') ?></span>
                                    </div>
                                    <ul class="transactions-list">

                                    <?php foreach ($category['transactions'] as $expense): ?>
                                        <li class="transaction-item d-flex justify-content-between" 
                                        data-comment="<?= htmlspecialchars($expense['expense_comment']) ?>"
                                        data-date="<?= htmlspecialchars($expense['date_of_expense']) ?>"
                                        data-amount="<?= htmlspecialchars($expense['amount']) ?>"
                                        data-category="<?= htmlspecialchars($expense['expense_category_name']) ?>"
                                        data-payment-method="<?= htmlspecialchars($expense['expense_category_name']) ?>">
                                            <div>
                                                <span class="transaction-comment"><?= htmlspecialchars($expense['expense_comment']) ?></span><br>
                                                <small class="text-body-secondary transaction-date"><?= date('d.m.Y',strtotime($expense['date_of_expense'])) ?></small>
                                            </div>
                                            <div class="text-end">
                                                <span class="text-danger transaction-amount">-<?= number_format($expense['amount'], 2, '.', '') ?></span><br>
                                                <button class="open-menu" type="button" data-bs-toggle="modal" data-bs-target="#editExpenseModal">
                                                    <img src="assets/images/forms/edit.svg" height="15" alt="">
                                                </button>
                                                <button class="open-menu" type="button" data-bs-toggle="modal" data-bs-target="#removeModal">
                                                    <img src="assets/images/forms/trash-bin.svg" height="15" alt="">
                                                </button>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>

                                    </ul>
                                </li>
                            <?php endforeach; ?>

                            </ul>
                        </div>
                    </div>
                </section>
            </div>
            <section class="total-balance mt-4">
                <h2 class="form-header h4">Total balance</h2>
                <div class="form-card">
                    <span class="h3 text-center" data-amount=""></span>
                    <p class="text-center"></p>
                </div>
            </section>
            <section class="pie-chart pt-5">
                <h2 class="h4 text-center text-white m-0">Your expenses for the selected period</h2>
                <div class="chart-container">
                    <canvas id="expensesChart"></canvas>
                </div>
            </section>
        </section>
        <div class="modal fade" id="editIncomeModal" tabindex="-1"  role="dialog" aria-labelledby="editIncomeModalLabel" aria-hidden="true"> 
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header form-header">
                        <h2 id="editIncomeModalLabel" class="h4">Edit record</h2>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="container d-flex flex-column justify-content-start p-3">
                        <div class="row input-wrapper start-date p-2">
                            <span class="col-1 input-symbol">
                                <img src="assets/images/forms/dollar-sign.svg" alt="">
                            </span>
                            <label for="incomeAmount" class="visually-hidden">Amount</label>
                            <input id="incomeAmount" name="amount" class="col field" type="number" placeholder="Amount" inputmode="decimal" required>
                        </div>
                        <div class="row input-wrapper end-date p-2">
                            <span class="col-1 input-symbol">
                                <img src="assets/images/forms/calendar.svg" alt="">
                            </span>
                            <label for="incomeDate" class="visually-hidden">Date</label>
                            <input id="incomeDate" name="date" class="col field" type="date" required>
                        </div>
                        <div class="row input-wrapper end-date p-2">
                            <span class="col-1 input-symbol">
                                <img src="assets/images/forms/layers.svg" alt="">
                            </span>
                            <label for="incomeCategory" class="visually-hidden">Category</label>
                            <select id="incomeCategory" name="category" class="col field select-field" required>
                                <option value="" selected disabled hidden>Category</option>
                                <option value="allegro-sale">Allegro sale</option>
                                <option value="salary">Salary</option>
                                <option value="bank-interes">Bank interes</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="row input-wrapper end-date p-2">
                            <span class="col-1 input-symbol">
                                <img src="assets/images/forms/message-square.svg" alt="">
                            </span>
                            <label for="incomeComment" class="visually-hidden">Comment</label>
                            <input id="incomeComment" name="comment" class="col field" type="text" placeholder="Comment" required>
                        </div>
                        <button type="button" class="btn button-primary edit-save-button">Save transaction</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editExpenseModal" tabindex="-1"  role="dialog" aria-labelledby="editExpenseModalLabel" aria-hidden="true"> 
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header form-header">
                        <h2 id="editExpenseModalLabel" class="h4">Edit record</h2>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="container d-flex flex-column justify-content-start p-3">
                        <div class="row input-wrapper start-date p-2">
                            <span class="col-1 input-symbol">
                                <img src="assets/images/forms/dollar-sign.svg" alt="">
                            </span>
                            <label for="expenseAmount" class="visually-hidden">Amount</label>
                            <input id="expenseAmount" name="amount" class="col field" type="number" placeholder="Amount" inputmode="decimal" required>
                        </div>
                        <div class="row input-wrapper end-date p-2">
                            <span class="col-1 input-symbol">
                                <img src="assets/images/forms/calendar.svg" alt="">
                            </span>
                            <label for="expenseDate" class="visually-hidden">Date</label>
                            <input id="expenseDate" name="date" class="col field" type="date" required>
                        </div>
                        <div class="row input-wrapper end-date p-2">
                            <span class="col-1 input-symbol">
                                <img src="assets/images/forms/credit-card.svg" alt="">
                            </span>
                            <label for="expensePaymentMethod" class="visually-hidden">Payment method</label>
                            <select id="expensePaymentMethod" name="paymentMethod" class="col field select-field" required>
                                <option value="" selected disabled hidden>Payment method</option>
                                <option class="dropdown-item" value="cash">Cash</option>
                                <option class="dropdown-item" value="credit-card">Credit card</option>
                                <option class="dropdown-item" value="debit-card">Debit card</option>
                            </select>
                        </div>
                        <div class="row input-wrapper end-date p-2">
                            <span class="col-1 input-symbol">
                                <img src="assets/images/forms/layers.svg" alt="">
                            </span>
                            <label for="expenseCategory" class="visually-hidden">Category</label>
                            <select id="expenseCategory" name="category" class="col field select-field" required>
                                <option value="" selected disabled hidden>Category</option>
                                <option value="food">Food</option>
                                <option value="apartment">Apartment</option>
                                <option value="transport">Transport</option>
                                <option value="telecommunication">Telecommunication</option>
                                <option value="healthcare">Healthcare</option>
                                <option value="clothes">Clothes</option>
                                <option value="hygiene">Hygiene</option>
                                <option value="children">Children</option>
                                <option value="recreation">Recreation</option>
                                <option value="trips">Trips</option>
                                <option value="learning">Learning</option>
                                <option value="books">Books</option>
                                <option value="savings">Savings</option>
                                <option value="retirement">Retirement</option>
                                <option value="debt-repayment">Debt repayment</option>
                                <option value="donation">Donation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="row input-wrapper end-date p-2">
                            <span class="col-1 input-symbol">
                                <img src="assets/images/forms/message-square.svg" alt="">
                            </span>
                            <label for="expenseComment" class="visually-hidden">Comment</label>
                            <input id="expenseComment" name="comment" class="col field" type="text" placeholder="Comment" required>
                        </div>
                        <button type="button" class="btn button-primary edit-save-button">Save transaction</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="removeModal" tabindex="-1"  role="dialog" aria-labelledby="removeModalLabel" aria-hidden="true"> 
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header form-header">
                            <h2 id="removeModalLabel" class="h4">Remove transaction</h2>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="container d-flex flex-column justify-content-start p-3">
                            <h3 class="h6 m-0">Are you sure you want to delete transaction?</h3>
                        </div>
                        <div class="modal-body">
                            <button type="button" class="btn button-outline" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn button-primary">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
    </main>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="editToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header form-header p-1">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                The transaction was modified
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/validation.js"></script>
    <script src="assets/js/modals.js"></script>
    <script src="assets/js/balance.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>