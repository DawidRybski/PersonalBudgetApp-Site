const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const nameRegex = /^[\p{L}\p{N}_-]+$/u;
const amount = document.querySelector('#amount');
const expenseForm = document.querySelector('.form-card');

function getCurrentDate() {
    const date = new Date();
    const day = String(date.getDate()).padStart(2, "0");
    const month = String((date.getMonth() + 1)).padStart(2, "0");
    const year = date.getFullYear();

    return year + "-" + month + "-" + day;
}

function getFieldLabel(fieldElement) {
    const id = $(fieldElement).attr("id");
    const labelText = $('label[for="' + id + '"]').text().trim();

    if (labelText) return labelText;

    return (
        $(fieldElement).attr("placeholder") ||
        $(fieldElement).data("label") ||
        $(fieldElement).attr("name") ||
        $(fieldElement).attr("id") ||
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

function clearErrors(fieldElement) {
    $(fieldElement).removeClass("validate-border");
    $(fieldElement).closest(".field-control").find(".validation-message").remove();
    $(fieldElement).siblings(".error-icon").hide();
    $(fieldElement).removeAttr("aria-describedby");
}

function addWrongEmailPatternMessage(emailFieldElement) {
    const errorId = $(emailFieldElement).attr("id") + "-error";
    $(emailFieldElement).attr("aria-describedby", errorId);
    $(emailFieldElement).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">Looks like this is not an email</p>');
    $(emailFieldElement).addClass("validate-border");
    $(emailFieldElement).siblings(".error-icon").show();
}

function addWordLengthNamePatternMessage(nameFieldElement) {
    const errorId = $(nameFieldElement).attr("id") + "-error";
    $(nameFieldElement).attr("aria-describedby", errorId);
    $(nameFieldElement).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">Name must be between 3 and 50 characters long</p>');
    $(nameFieldElement).addClass("validate-border");
    $(nameFieldElement).siblings(".error-icon").show();
}

function addWrongCharsNamePatternMessage(nameFieldElement) {
    const errorId = $(nameFieldElement).attr("id") + "-error";
    $(nameFieldElement).attr("aria-describedby", errorId);
    $(nameFieldElement).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">Name cannot contain special characters other than _ and -</p>');
    $(nameFieldElement).addClass("validate-border");
    $(nameFieldElement).siblings(".error-icon").show();
}

function addBlankFieldErrors(fieldElement, fieldLabel) {
    const errorId = $(fieldElement).attr("id") + "-error";
    $(fieldElement).attr("aria-describedby", errorId);
    $(fieldElement).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">' + fieldLabel + ' cannot be empty</p>');
    $(fieldElement).addClass("validate-border");
    $(fieldElement).siblings(".error-icon").show();
}

function addPasswordLengthMessage(passwordFieldElement) {
    const errorId = $(passwordFieldElement).attr("id") + "-error";
    $(passwordFieldElement).attr("aria-describedby", errorId);
    $(passwordFieldElement).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">Password must be between 6 and 64 characters long</p>');
    $(passwordFieldElement).addClass("validate-border");
    $(passwordFieldElement).siblings(".error-icon").show();
}

function addConfirmPasswordMessage(confirmPasswordFieldElement) {
    const errorId = $(confirmPasswordFieldElement).attr("id") + "-error";
    $(confirmPasswordFieldElement).attr("aria-describedby", errorId);
    $(confirmPasswordFieldElement).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">Passwords do not match</p>');
    $(confirmPasswordFieldElement).addClass("validate-border");
    $(confirmPasswordFieldElement).siblings(".error-icon").show();
}

function checkPasswordConfirm(passwordValue, confirmPasswordValue) {
    return passwordValue === confirmPasswordValue;
}

function toggleEmailErrors(fieldElement) {
    const value = $(fieldElement).val().trim();
    const noValidateMessage = $(fieldElement).closest(".field-control").find(".validation-message").length === 0;
    const message = $(fieldElement).closest(".field-control").find(".validation-message").text();

    if (message === "Looks like this is not an email") {
        clearErrors($(fieldElement));
    }

    if (value !== "" && !emailRegex.test(value) && noValidateMessage) {
        addWrongEmailPatternMessage($(fieldElement));
    }
}

function toggleNameErrors(fieldElement){
    const value = $(fieldElement).val().trim();
    const noValidateMessage = $(fieldElement).closest(".field-control").find(".validation-message").length === 0;
    const message = $(fieldElement).closest(".field-control").find(".validation-message").text();
    
    if (message === "Name must be between 3 and 50 characters long" ||
    message === "The name cannot contain special characters other than _ and -") {
        clearErrors($(fieldElement));
    }

    if ((value.length<3 || value.length>50) && noValidateMessage){
        addWordLengthNamePatternMessage($(fieldElement));
    } else if (!nameRegex.test(value) && noValidateMessage){
        addWrongCharsNamePatternMessage($(fieldElement));
    }
}

function togglePasswordErrors(fieldElement) {
    const passwordValue = $("#password").val().trim();
    const confirmPasswordValue = $("#confirmPassword").val().trim();
    const noValidateMessage = $("#confirmPassword").closest(".field-control").find(".validation-message").length === 0;
    const message = $(fieldElement).closest(".field-control").find(".validation-message").text();

    if (message === "Passwords do not match" || 
        message === "Password must be between 6 and 64 characters long") {
        clearErrors($(fieldElement));
    }

    if ($(fieldElement).attr("id") === "password" && (passwordValue.length<6 || passwordValue.length>64) && noValidateMessage){
        addPasswordLengthMessage($(fieldElement));
    } else if ($(fieldElement).attr("id") === "confirmPassword" && confirmPasswordValue !== "" && !checkPasswordConfirm(passwordValue, confirmPasswordValue) && noValidateMessage) {
        addConfirmPasswordMessage($(fieldElement));
    }
}

function submitFormValidation(){
    $(".submit").on("click", function(){    
        $(".field").each(function (){
            let fieldLabel = getFieldLabel(this);
            let hasBlankValidate = $(this).closest(".field-control").find(".validation-message").length;
            let value = ($(this).val() || "").trim();

            if (value === "") {
                if (!hasBlankValidate){
                    addBlankFieldErrors(this, fieldLabel);
                }
            }

            updateButtonState();
        });
    });
}

function clearErrorsAndUpdateButton(){
    $(".field").on("input", function(){
        if ($(this).val().trim() != ""){
            clearErrors($(this));
        }
        updateButtonState();
    });
}

function toggleValidationErrors(){
    $(".field").on("input", function(){
    let idOfElement = $(this).attr("id");
        if ((idOfElement === "email")){
            toggleEmailErrors(this);
        }

        if ((idOfElement === "name")){
            toggleNameErrors(this);
        }

        if ((idOfElement === "password" || idOfElement === "confirmPassword")){
            togglePasswordErrors(this);
        }
        updateButtonState();
    });
}

function loginRedirect(){
    $("#loginForm").on("submit", function(e){

    let blankField = false;
    
    $(".field").each(function (){
        let value = $(this).val().trim();

        if (value === "") {
            blankField = true;
        }
    });

    if (blankField) {
        e.preventDefault();
    }
});
}

function addValidationPatternForAmount (){
    // Usuwamy wszystko oprócz cyfr i kropki
    this.value = this.value.replace(/[^0-9.,]/g, '');

    // Dzielimy po kropce
    const parts = this.value.split(/[.,]/);

    // Zostawiamy maksymalnie 2 cyfry po przecinku
    if (parts.length > 1) {
        const separator = this.value.includes(',') ? ',' : '.';
        this.value = parts[0].slice(0, 6) + separator + parts[1].slice(0, 2);
    } else {
        this.value = parts[0].slice(0, 6);
    }
}

function replaceCommaToDecimalPoint(element){
     element.value = element.value.replace(',', '.');
}