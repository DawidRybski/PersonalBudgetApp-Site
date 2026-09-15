document.addEventListener('DOMContentLoaded', sortAll);
if (amount) {
    amount.addEventListener('input', addValidationPatternForAmount);
}

if (formCard && amount) {
    formCard.addEventListener('submit', function () {
        replaceCommaToDecimalPoint(amount);
    });
}

submitFormValidation();
clearErrorsAndUpdateButton();
toggleValidationErrors();
loginRedirect();

$("#expenseDate").attr("value", getCurrentDate());

createExpensesChart();
getExpenseDataForEditModal();
getIncomeDataForEditModal();
getExpenseDataForRemoveModal();
getIncomeDataForRemoveModal();
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