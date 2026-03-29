var emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
var initialExpenseData = {};
var initialIncomeData = {};

function getCurrentDate(){
    var date = new Date();
    var day = String(date.getDate()).padStart(2, "0");
    var month = String((date.getMonth()+1)).padStart(2, "0");
    var year = date.getFullYear();

    return year + "-" + month + "-" + day;
}

function getFieldLabel(field) {
    var id = $(field).attr("id");
    var labelText = $('label[for="' + id + '"]').text().trim();

    if (labelText) return labelText;

    return (
        $(field).attr("placeholder") ||
        $(field).data("label") ||
        $(field).attr("name") ||
        $(field).attr("id") ||
        "This field"
    );
}

function updateButtonState() {
    if ($(".validation-message").length) {
        $(".submit").prop("disabled", true);
    } else {
        $(".submit").prop("disabled", false);
    }
}

function clearErrors(field) {
    $(field).removeClass("validate-border");
    $(field).closest(".field-control").find(".validation-message").remove();
    $(field).siblings(".error-icon").hide();
    $(field).removeAttr("aria-describedby");
}

function addWrongEmailPatternMessage(emailField){
    var errorId = $(emailField).attr("id") + "-error";
    $(emailField).attr("aria-describedby", errorId);
    $(emailField).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">Looks like this is not an email</p>');
    $(emailField).addClass("validate-border");
    $(emailField).siblings(".error-icon").show();
}

function addBlankFieldErrors(field, fieldLabel){
    var errorId = $(field).attr("id") + "-error";
    $(field).attr("aria-describedby", errorId);
    $(field).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">'+ fieldLabel +' cannot be empty</p>');
    $(field).addClass("validate-border");
    $(field).siblings(".error-icon").show();
}

function addConfirmPasswordMessage(confirmPasswordField){
    var errorId = $(confirmPasswordField).attr("id") + "-error";
    $(confirmPasswordField).attr("aria-describedby", errorId);
    $(confirmPasswordField).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">Passwords do not match</p>');
    $(confirmPasswordField).addClass("validate-border");
    $(confirmPasswordField).siblings(".error-icon").show();
}

function checkPasswordConfirm(password, confirmPassword){
    return password === confirmPassword;
}

function toggleEmailErrors(field){
    var value = $(field).val().trim();
    var noValidateMessage = $(field).closest(".field-control").find(".validation-message").length === 0;
    var message = $(field).closest(".field-control").find(".validation-message").text();

    if (message === "Looks like this is not an email") {
        clearErrors($(field));
    }

    if (value !== "" && !emailReg.test(value) && noValidateMessage) {
        addWrongEmailPatternMessage($(field));
    }
}

function toggleConfirmPasswordErrors(field){
    var pass = $("#password").val().trim();
    var confirmPass = $("#confirmPassword").val().trim();

    var noValidateMessage = $(confirmPass).closest(".field-control").find(".validation-message").length === 0;
    var message = $(field).closest(".field-control").find(".validation-message").text();

    if (message === "Passwords do not match") {
        clearErrors($(field));
    }

    if (confirmPass !== "" && !checkPasswordConfirm(pass, confirmPass) && noValidateMessage) {
        addConfirmPasswordMessage($(field));
    }
}

function createExpensesChart(){
    var categories = [];
    var amounts = [];

    var expenseCategories = $("#expenses .transactions-category");

    expenseCategories.each(function(){
        var categoryName = $(this).find(".category-header [data-category]").text().trim();
        var categoryAmount = $(this).find(".category-header [data-amount]").data("amount");

        categories.push(categoryName);
        amounts.push(categoryAmount);
    })

    new Chart('expensesChart', {
        type: 'pie',
        data: {
            labels: categories,
            datasets: [
                {
                    data: amounts,
                    label: "Expenses by category",
                    backbroundColor:[
                        "rgb(240, 90, 40)",
                        "rgb(216, 154, 0)",
                        "rgb(157, 73, 236)",
                        "rgb(54, 162, 235)",
                        "rgb(255, 99, 132)",
                        "rgb(75, 192, 192)"
                    ],
                    hoverOffset: 8
                }
            ]
        },
        options : {
            responsive: true,
            plugins: {
                legend: {
                    position: "left",
                    labels: {
                        color: "white",
                        boxWidth: 15
                    }
                }   
            }
        }
    });
}

function showEditToast(){
    const toastTrigger = $(".edit-save-button");
    const toast = document.getElementById('editToast');

    if (toastTrigger.length && toast) {
        const toastEdit = bootstrap.Toast.getOrCreateInstance(toast);
        
        toastTrigger.on("click", function (){
            toastEdit.show();
        });
    }
}

function checkIfExpenseDataChanged(){
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

    $("editExpenseModal .edit-save-button").prop("disabled", isSame || hasEmptyFields);
}

function getExpenseDataForModal(){
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

function checkIfIncomeDataChanged(){
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

function getIncomeDataForModal(){
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
    const expenseModal = document.getElementById("editExpenseModal");
    const incomeModal = document.getElementById("editIncomeModal");
    
    if (expenseModal){
        const expenseModalInstance = bootstrap.Modal.getOrCreateInstance(expenseModal);
        expenseModalInstance.hide();
    }

    if (incomeModal){
        const incomeModalInstance = bootstrap.Modal.getOrCreateInstance(incomeModal);
        incomeModalInstance.hide();
    }
}

function sortTransactionsByDate(container) {
    const items = Array.from(container.querySelectorAll('.transaction-item'));

    items.sort((a, b) => {
        const dateA = new Date(a.dataset.date);
        const dateB = new Date(b.dataset.date);
        return dateA - dateB; // rosnąco (od najstarszej)
        // return dateB - dateA; // jeśli chcesz od najnowszej
    });

    items.forEach(item => container.appendChild(item));
}

function sortCategories(container) {
    const categories = Array.from(container.querySelectorAll('.transactions-category'));

    categories.sort((a, b) => {
        const nameA = a.querySelector('[data-category]').dataset.category.toLowerCase();
        const nameB = b.querySelector('[data-category]').dataset.category.toLowerCase();

        return nameA.localeCompare(nameB);
    });

    categories.forEach(cat => container.appendChild(cat));
}

function sortAll() {
    // INCOMES
    const incomesContainer = document.querySelector('.incomes-list');
    sortCategories(incomesContainer);

    incomesContainer.querySelectorAll('.transactions-list').forEach(list => {
        sortTransactionsByDate(list);
    });

    // EXPENSES
    const expensesContainer = document.querySelector('.expenses-list');
    sortCategories(expensesContainer);

    expensesContainer.querySelectorAll('.transactions-list').forEach(list => {
        sortTransactionsByDate(list);
    });
}

document.addEventListener('DOMContentLoaded', sortAll);

$(".submit").on("click", function(){    
    $(".field").each(function (){
        var fieldLabel = getFieldLabel(this);
        var hasBlankValidate = $(this).closest(".field-control").find(".validation-message").length;
        var value = ($(this).val() || "").trim();

        if (value === "") {
            if (!hasBlankValidate){
                addBlankFieldErrors(this, fieldLabel);
            }
        }

        updateButtonState();
    });
});

$(".field").on("input", function(){
    if ($(this).val().trim() != ""){
        clearErrors($(this));
    }
    updateButtonState();
});

$(".field").on("input", function(){
    var idOfElement = $(this).attr("id");

    if ((idOfElement === "email")){
        toggleEmailErrors(this);
    }

    if ((idOfElement === "confirmPassword")){
        toggleConfirmPasswordErrors(this);
    }

    updateButtonState();
});

$("#loginForm").on("submit", function(e){
    e.preventDefault();

    var validCredentials = true;
    var blankField = false;
    
    $(".field").each(function (){
        var value = $(this).val().trim();

        if (value === "") {
            blankField = true;
        }
    });

    if (validCredentials && !blankField){
        window.location.href = "homePage.html";
    }
});

$("#expenseDate").attr("value", getCurrentDate());

createExpensesChart();
showEditToast();
getExpenseDataForModal();
getIncomeDataForModal();

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