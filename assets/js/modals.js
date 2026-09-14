let initialExpenseData = {};
let initialIncomeData = {};

function checkIfExpenseDataChanged() {
    const amount = $("#editExpenseModal #expenseAmount").val();
    const date = $("#editExpenseModal #expenseDate").val();
    const paymentMethod = $("#editExpenseModal #expensePaymentMethod").val();
    const category = $("#editExpenseModal #expenseCategory").val();
    const comment = $("#editExpenseModal #expenseComment").val();

    const isSame = amount === String(initialExpenseData.amount) &&
        date === String(initialExpenseData.date) &&
        comment === String(initialExpenseData.comment) &&
        category === String(initialExpenseData.category) &&
        paymentMethod === String(initialExpenseData.paymentMethod);

    const hasEmptyFields =
        amount === "" ||
        date === "" ||
        comment === "" ||
        !category ||
        !paymentMethod;

    $("#editExpenseModal .edit-save-button").prop("disabled", isSame || hasEmptyFields);
}

function getExpenseDataForEditModal() {
    $(".expenses-list").on("click", 'button[data-bs-target="#editExpenseModal"]', function () {
        const transactionItem = $(this).closest(".transaction-item");

        initialExpenseData = {
            id: transactionItem.data("id"),
            amount: transactionItem.data("amount"),
            date: transactionItem.data("date"),
            comment: transactionItem.data("comment"),
            category: transactionItem.data("category"),
            paymentMethod: transactionItem.data("payment-method")
        };

        $("#editExpenseModal #expenseId").val(transactionItem.data("id"));
        $("#editExpenseModal #expenseAmount").val(transactionItem.data("amount"));
        $("#editExpenseModal #expenseDate").val(transactionItem.data("date"));
        $("#editExpenseModal #expensePaymentMethod").val(transactionItem.data("payment-method"));
        $("#editExpenseModal #expenseCategory").val(transactionItem.data("category"));
        $("#editExpenseModal #expenseComment").val(transactionItem.data("comment"));

        checkIfExpenseDataChanged();
    });
}

function getExpenseDataForRemoveModal() {
    $(".expenses-list").on("click", 'button[data-bs-target="#removeExpenseModal"]', function () {
        const transactionItem = $(this).closest(".transaction-item");

    $("#removeExpenseModal #removeExpenseId").val(transactionItem.data("id"));
    });
}

function checkIfIncomeDataChanged() {
    const amount = $("#editIncomeModal #incomeAmount").val();
    const date = $("#editIncomeModal #incomeDate").val();
    const category = $("#editIncomeModal #incomeCategory").val();
    const comment = $("#editIncomeModal #incomeComment").val();

    const isSame = amount === String(initialIncomeData.amount) &&
        date === String(initialIncomeData.date) &&
        comment === String(initialIncomeData.comment) &&
        category === String(initialIncomeData.category);

    const hasEmptyFields =
        amount === "" ||
        date === "" ||
        comment === "" ||
        !category;

    $("#editIncomeModal .edit-save-button").prop("disabled", isSame || hasEmptyFields);
}

function getIncomeDataForEditModal() {
    $(".incomes-list").on("click", 'button[data-bs-target="#editIncomeModal"]', function () {
        const transactionItem = $(this).closest(".transaction-item");

        initialIncomeData = {
            id: transactionItem.data("id"),
            amount: transactionItem.data("amount"),
            date: transactionItem.data("date"),
            comment: transactionItem.data("comment"),
            category: transactionItem.data("category")
        };

        $("#editIncomeModal #incomeId").val(transactionItem.data("id"));
        $("#editIncomeModal #incomeAmount").val(transactionItem.data("amount"));
        $("#editIncomeModal #incomeDate").val(transactionItem.data("date"));
        $("#editIncomeModal #incomeCategory").val(transactionItem.data("category"));
        $("#editIncomeModal #incomeComment").val(transactionItem.data("comment"));

        checkIfIncomeDataChanged();
    });
}

function getIncomeDataForRemoveModal() {
    $(".incomes-list").on("click", 'button[data-bs-target="#removeIncomeModal"]', function () {
        const transactionItem = $(this).closest(".transaction-item");

    $("#removeIncomeModal #removeIncomeId").val(transactionItem.data("id"));
    });
}