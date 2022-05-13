function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        return parts.pop().split(';').shift();
    }
}


function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}


function setSheetCount(sheet) {
    const postId = sheet.id.replace('post', '')
    fetch('/sheetpost/api/get-post-sheet-count?' + new URLSearchParams({
        post_id: postId
    }).toString())
        .then(response => response.json())
        .then(data => {
            if ('success' in data && data['success']) {
                sheet.getElementsByTagName('span')[0].innerText = data['sheet_count'] > 0 ? data['sheet_count'] : ''
            }
        })
}


function clickOnSheet(sheet) {
    const img = sheet.getElementsByTagName('img')[0]
    const postId = sheet.id.replace('post', '')
    const username = getCookie('username')
    const password = getCookie('password')
    const apiRequest = img.style.opacity === '1' ? 'unsheet-post' : 'sheet-post'
    const opacity = img.style.opacity === '1' ? '.5' : '1'
    fetch(`/sheetpost/api/${apiRequest}?` + new URLSearchParams({
        username: username,
        password: password,
        post_id: postId
    }).toString())
        .then(response => response.json())
        .then(data => {
            if ('success' in data && data['success']) {
                setSheetCount(sheet)
                img.style.opacity = opacity
            }
        })
}

function setSheetListeners() {
    [...document.getElementsByClassName('sheet')].forEach(sheet => {
        sheet.addEventListener('click', () => { clickOnSheet(sheet) })
    })
}


document.getElementById('newPostModal').addEventListener('show.bs.modal', () => {
    document.getElementById('newPostMessageTextValidationLabel').innerText = ''
    document.getElementById('newPostFormValidationLabel').hidden = true
})


document.getElementById('newPostCreate').addEventListener('click', () => {
    const username = getCookie('username')
    const password = getCookie('password')
    const messageText = document.getElementById('newPostMessageText').value.trim()
    const messageTextValidationLabel = document.getElementById('newPostMessageTextValidationLabel')
    const formValidationLabel = document.getElementById('newPostFormValidationLabel')

    if (!messageText) {
        messageTextValidationLabel.innerText = 'Message is empty'
    } else if (messageText.length > 4096) {
        messageTextValidationLabel.innerText =
            `Maximum message length is 4096 characters (${messageText.length - 4096} characters exceeded)`
    } else {
        messageTextValidationLabel.innerText = ''
    }
    formValidationLabel.hidden = true

    if (messageText && messageText.length <= 4096) {
        fetch('/sheetpost/api/create-new-post?' + new URLSearchParams({
            username: username,
            password: password,
            message: messageText
        }).toString())
            .then(response => response.json())
            .then(data => {
                if ('success' in data && data['success']) {
                    document.location.reload();
                    return
                } else if ('error' in data && data['error'] === 'invalid username or password') {
                    formValidationLabel.innerText = 'Reauthorize and try again'
                } else if ('error' in data) {
                    formValidationLabel.innerText = capitalize(data['error'])
                } else {
                    formValidationLabel.innerText = 'Something went wrong, try again later'
                }
                formValidationLabel.hidden = false
            })
    }
})


setSheetListeners()