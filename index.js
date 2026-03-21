var emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

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
        $("button").prop("disabled", true);
    } else {
        $("button").prop("disabled", false);
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