var emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

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

function addBlankFieldErrors(field, fieldPlaceholder){
    var errorId = $(field).attr("id") + "-error";
    $(field).attr("aria-describedby", errorId);
    $(field).closest(".field-control").append('<p id="' + errorId + '" class="validation-message" role="alert">'+ fieldPlaceholder +' cannot be empty</p>');
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


$(".submit").on("click", function(){  
    $(".field").each(function (){
        var fieldPlaceholder = $(this).attr("placeholder");
        var hasBlankValidate = $(this).closest(".field-control").find(".validation-message").length;
        var value = $(this).val().trim();

        if (value === "") {
            if (!hasBlankValidate){
                addBlankFieldErrors(this, fieldPlaceholder);
            }

            $(this).addClass("validate-border");
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