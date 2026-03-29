let initialExpenseData = {};
let initialIncomeData = {};

function showEditToast() {
    const toastTrigger = $(".edit-save-button");
    const toastElement = document.getElementById('editToast');

    if (toastTrigger.length && toastElement) {
        const toastEdit = bootstrap.Toast.getOrCreateInstance(toastElement);

        toastTrigger.on("click", function () {
            toastEdit.show();
        });
    }
}

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

function getExpenseDataForModal() {
    $(".expenses-list").on("click", 'button[data-bs-target="#editExpenseModal"]', function () {
        const transactionItem = $(this).closest(".transaction-item");

        initialExpenseData = {
            amount: transactionItem.data("amount"),
            date: transactionItem.data("date"),
            comment: transactionItem.data("comment"),
            category: transactionItem.data("category"),
            paymentMethod: transactionItem.data("payment-method")
        };

        $("#editExpenseModal #expenseAmount").val(transactionItem.data("amount"));
        $("#editExpenseModal #expenseDate").val(transactionItem.data("date"));
        $("#editExpenseModal #expensePaymentMethod").val(transactionItem.data("payment-method"));
        $("#editExpenseModal #expenseCategory").val(transactionItem.data("category"));
        $("#editExpenseModal #expenseComment").val(transactionItem.data("comment"));

        checkIfExpenseDataChanged();
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

function getIncomeDataForModal() {
    $(".incomes-list").on("click", 'button[data-bs-target="#editIncomeModal"]', function () {
        const transactionItem = $(this).closest(".transaction-item");

        initialIncomeData = {
            amount: transactionItem.data("amount"),
            date: transactionItem.data("date"),
            comment: transactionItem.data("comment"),
            category: transactionItem.data("category")
        };

        $("#editIncomeModal #incomeAmount").val(transactionItem.data("amount"));
        $("#editIncomeModal #incomeDate").val(transactionItem.data("date"));
        $("#editIncomeModal #incomeCategory").val(transactionItem.data("category"));
        $("#editIncomeModal #incomeComment").val(transactionItem.data("comment"));

        checkIfIncomeDataChanged();
    });
}

function closeEditModal() {
    const expenseModalElement = document.getElementById("editExpenseModal");
    const incomeModalElement = document.getElementById("editIncomeModal");

    if (expenseModalElement) {
        const expenseModalInstance = bootstrap.Modal.getOrCreateInstance(expenseModalElement);
        expenseModalInstance.hide();
    }

    if (incomeModalElement) {
        const incomeModalInstance = bootstrap.Modal.getOrCreateInstance(incomeModalElement);
        incomeModalInstance.hide();
    }
}