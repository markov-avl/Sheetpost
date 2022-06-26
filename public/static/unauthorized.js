function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}


document.getElementById('logInModalToggle').addEventListener('show.bs.modal', () => {
    document.getElementById('logInInputUsernameValidationLabel').innerText = ''
    document.getElementById('logInInputPasswordValidationLabel').innerText = ''
    document.getElementById('logInFormValidationLabel').hidden = true
})


document.getElementById('logInEnter').addEventListener('click', () => {
    const form = document.getElementById('logInForm')
    const username = document.getElementById('logInInputUsername')
    const password = document.getElementById('logInInputPassword')
    const formValidationLabel = document.getElementById('logInFormValidationLabel')

    document.getElementById('logInInputUsernameValidationLabel').innerText = username.value ? '' : 'Field is empty'
    document.getElementById('logInInputPasswordValidationLabel').innerText = password.value ? '' : 'Field is empty'
    formValidationLabel.hidden = true

    if (username.value && password.value) {
        fetch('/sheetpost-v3/api/is-user-exists?' + new URLSearchParams({
            username: username.value,
            password: password.value
        }).toString())
            .then(response => response.json())
            .then(data => {
                if ('success' in data && data['success']) {
                    if (data['exists']) {
                        form.submit()
                        return
                    }
                    formValidationLabel.innerText = 'Invalid username or password'
                } else {
                    formValidationLabel.innerText = 'Something went wrong, try again later'
                }
                formValidationLabel.hidden = false
            })
    }
})


document.getElementById('signUpModalToggle').addEventListener('show.bs.modal', () => {
    document.getElementById('signUpInputUsernameValidationLabel').innerText = ''
    document.getElementById('signUpInputPasswordValidationLabel').innerText = ''
    document.getElementById('signUpInputReEnterPasswordValidationLabel').innerText = ''
    document.getElementById('signUpFormValidationLabel').hidden = true
})


document.getElementById('signUpEnter').addEventListener('click', () => {
    const form = document.getElementById('signUpForm')
    const username = document.getElementById('signUpInputUsername')
    const password = document.getElementById('signUpInputPassword')
    const reEnterPassword = document.getElementById('signUpReEnterInputPassword')
    const formValidationLabel = document.getElementById('signUpFormValidationLabel')

    document.getElementById('signUpInputUsernameValidationLabel').innerText = username.value ? '' : 'Field is empty'
    document.getElementById('signUpInputPasswordValidationLabel').innerText = password.value ? '' : 'Field is empty'
    document.getElementById('signUpInputReEnterPasswordValidationLabel').innerText =
        password.value === reEnterPassword.value ? '' : 'Passwords do not match'
    formValidationLabel.hidden = true

    if (username.value && password.value && password.value === reEnterPassword.value) {
        fetch('/sheetpost-v3/api/create-new-user?' + new URLSearchParams({
            username: username.value,
            password: password.value
        }).toString())
            .then(response => response.json())
            .then(data => {
                if ('success' in data && data['success']) {
                    form.submit()
                    return
                } else if ('error' in data) {
                    formValidationLabel.innerText = capitalize(data['error'])
                } else {
                    formValidationLabel.innerText = 'Something went wrong, try again later'
                }
                formValidationLabel.hidden = false
            })
    }
})


