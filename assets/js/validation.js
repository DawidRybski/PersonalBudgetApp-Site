const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

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

function addBlankFieldErrors(fieldElement, fieldLabel) {
    const errorId = $(fieldElement).attr("id") + "-error";
    $(fieldElement).attr("aria-describedby", errorId);
    $(fieldElement).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">' + fieldLabel + ' cannot be empty</p>');
    $(fieldElement).addClass("validate-border");
    $(fieldElement).siblings(".error-icon").show();
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

function toggleConfirmPasswordErrors(fieldElement) {
    const passwordValue = $("#password").val().trim();
    const confirmPasswordValue = $("#confirmPassword").val().trim();

    const noValidateMessage = $("#confirmPassword").closest(".field-control").find(".validation-message").length === 0;
    const message = $(fieldElement).closest(".field-control").find(".validation-message").text();

    if (message === "Passwords do not match") {
        clearErrors($(fieldElement));
    }

    if (confirmPasswordValue !== "" && !checkPasswordConfirm(passwordValue, confirmPasswordValue) && noValidateMessage) {
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

        if ((idOfElement === "confirmPassword")){
            toggleConfirmPasswordErrors(this);
        }
        updateButtonState();
    });
}

function loginRedirect(){
    $("#loginForm").on("submit", function(e){
    e.preventDefault();

    let validCredentials = true;
    let blankField = false;
    
    $(".field").each(function (){
        let value = $(this).val().trim();

        if (value === "") {
            blankField = true;
        }
    });

    if (validCredentials && !blankField){
        window.location.href = "homePage.html";
    }
});
}