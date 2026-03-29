document.addEventListener('DOMContentLoaded', sortAll);
submitFormValidation();
clearErrorsAndUpdateButton();
toggleValidationErrors();
loginRedirect();

$("#expenseDate").attr("value", getCurrentDate());

createExpensesChart();
showEditToast();
getExpenseDataForModal();
getIncomeDataForModal();
addBalanceInfo();

$("#editExpenseModal").on("input change", ".field, select", function () {
    checkIfExpenseDataChanged();
});

$("#editIncomeModal").on("input change", ".field, select", function () {
    checkIfIncomeDataChanged();
});

$("#editExpenseModal .edit-save-button").on("click", function () {
    closeEditModal();
});

$("#editIncomeModal .edit-save-button").on("click", function () {
    closeEditModal();
});