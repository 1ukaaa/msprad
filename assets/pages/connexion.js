$(document).ready(function () {
    validateForm();
});

function validateForm() {
    $("#buttonValidate").on("click", function () {
        let mailUser = $("#mailUser").val();
        let passwordUser = $("#passwordUser").val();
        if (!mailUser || !passwordUser) {
            Swal.fire('Attention', 'Veuillez remplir tous les champs', 'warning');
        } else if (!EmailValidation(mailUser)) {
            Swal.fire('Attention', "Le mail n'est pas au bon format", 'warning');
        } else {
            connexion(mailUser, passwordUser);
        }
    });
}

function EmailValidation(mailUser) {
    let mailFormat = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    let isMailValid = false;
    if (mailFormat.test(mailUser)) {
        isMailValid = true;
    }
    return isMailValid;
}

function connexion(mailUser, passwordUser) {
    url = "?route=ControleurConnexion&action=makeConnexion";
    data = {
        'mailUser': mailUser,
        'passwordUser': passwordUser,
    };
    addAjax(url, data, connexionSuccessCallback, errorConnexionCallback, { mailUser });
}

function connexionSuccessCallback(result, params) {
    let data = result.data;
    let brutForceData = result.data.brutForce;
    if (brutForceData.isStillBlocked) {
        let timerInterval
        Swal.fire({
            title: 'Compte Bloqué',
            html: 'Débloquage dans <b></b> secondes',
            showConfirmButton: false,
            allowOutsideClick: false,
            timer: brutForceData.leftTime * 1000,
            timerProgressBar: true,
            didOpen: () => {
                const b = Swal.getHtmlContainer().querySelector('b')
                timerInterval = setInterval(() => {
                    b.textContent = Math.round(Swal.getTimerLeft() / 1000)
                }, 100)
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        })
    } else {
        if (data.goodPassword) {
            if (data.googleAuthenticator.firstConnexion) {
                sweetAlertFirstConnexion(data.googleAuthenticator.imgUrl);
            } else {
                sweetAlertAfterFirstConnexion(params.mailUser);
            }
        } else {
            sweetAlertWrongPassword();
        }
    }


};

function sweetAlertWrongPassword() {
    swal.fire('Mauvais email ou mot de passe', '', 'warning');
}

function sweetAlertFirstConnexion(url) {
    Swal.fire({
        title: `Scanner le qrcode puis reconnectez vous`,
        imageUrl: url,
    })
}

function sweetAlertAfterFirstConnexion(mailUser) {
    Swal.fire({
        title: 'Entre le code Google Authenticator',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Confirmer',
        showLoaderOnConfirm: true,
        preConfirm: (mdp) => {
            url = "?route=ControleurConnexion&action=connectAfterFirstConnexion";
            data = {
                'googleAuthentificatorCode': mdp,
                'mailUser': mailUser,
            };
            addAjax(url, data, connectAfterFirstConnexionSuccessCallback, errorConnexionCallback);
        },
    })
}

function connectAfterFirstConnexionSuccessCallback(result) {
    let connected = result.data;
        if (connected) {
            Swal.fire('Connexion', '', 'success');
        } else {
            Swal.fire('Mauvais code Ou compte Bloqué', '', 'warning');
        }
}

function errorConnexionCallback() {
    Swal.fire('Erreur', 'Une erreur est survenue', 'warning');
};
