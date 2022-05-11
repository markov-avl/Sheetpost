function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        return parts.pop().split(';').shift();
    }
}


function setSheetCount(sheet) {
    const postId = sheet.id.replace('post', '')
    fetch(`/sheetpost/api/get-post-sheet-count?post_id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data['success']) {
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
    fetch(`/sheetpost/api/${apiRequest}?username=${username}&password=${password}&post_id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data['success']) {
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
    const messageText = document.getElementById('newPostMessageText')
    const messageTextValidationLabel = document.getElementById('newPostMessageTextValidationLabel')
    const formValidationLabel = document.getElementById('newPostFormValidationLabel')

    if (!messageText.value) {
        messageTextValidationLabel.innerText = 'Field is empty'
    } else if (messageText.value.length > 4096) {
        const exceededCharacters = messageText.value.length - 4096
        messageTextValidationLabel.innerText = `Maximum message length is 4096 characters (${exceededCharacters} characters exceeded)`
    } else {
        messageTextValidationLabel.innerText = ''
    }
    formValidationLabel.hidden = true

    if (messageText.value && messageText.value.length <= 4096) {
        fetch(`/sheetpost/api/create-new-post?username=${username}&password=${password}&message=${messageText.value}`)
            .then(response => response.json())
            .then(data => {
                if (data['success']) {
                    document.location.reload();
                    return
                } else if (data['error'] === 'message is too long (maximum 4096 characters)') {
                    const exceededCharacters = messageText.value.length - 4096
                    formValidationLabel.innerText = `Maximum message length is 4096 characters (${exceededCharacters} characters exceeded)`
                } else if (data['error'] === 'invalid username or password)') {
                    formValidationLabel.innerText = 'Reauthorize and try again'
                }
                formValidationLabel.hidden = false
            })
    }
})


setSheetListeners()