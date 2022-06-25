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


function insertAfter(newNode, existingNode) {
    existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
}


function setSheetCount(sheet) {
    const postId = sheet.id.replace('postSheet', '')
    fetch('/sheetpost-v2/api/get-post-sheet-count?' + new URLSearchParams({
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
    const postId = sheet.id.replace('postSheet', '')
    const apiRequest = img.classList.contains('sheeted') ? 'unsheet-post' : 'sheet-post'
    const classReplace = img.classList.contains('sheeted') ? ['sheeted', 'unsheeted'] : ['unsheeted', 'sheeted']
    fetch(`/sheetpost-v2/api/${apiRequest}?` + new URLSearchParams({
        username: getCookie('username'), password: getCookie('password'), post_id: postId
    }).toString())
        .then(response => response.json())
        .then(data => {
            if ('success' in data && data['success']) {
                setSheetCount(sheet)
                img.classList.replace(...classReplace)
            }
        })
}


function updatePost(postId) {
    const messageText = document.getElementById('editPostMessageText').value.trim()
    const messageTextValidationLabel = document.getElementById('editPostMessageTextValidationLabel')
    const formValidationLabel = document.getElementById('editPostFormValidationLabel')

    if (!messageText) {
        messageTextValidationLabel.innerText = 'Message is empty'
    } else if (messageText.length > 4096) {
        messageTextValidationLabel.innerText = `Maximum message length is 4096 characters (${messageText.length - 4096} characters exceeded)`
    } else {
        messageTextValidationLabel.innerText = ''
    }
    formValidationLabel.hidden = true

    if (messageText && messageText.length <= 4096) {
        fetch('/sheetpost-v2/api/edit-post?' + new URLSearchParams({
            username: getCookie('username'), password: getCookie('password'), post_id: postId, message: messageText
        }).toString())
            .then(response => response.json())
            .then(data => {
                if ('success' in data && data['success']) {
                    const post = document.getElementById(`post${postId}`);
                    [...post.getElementsByClassName('line')].forEach(line => {
                        line.remove()
                    });
                    let lastPostElement = [...post.getElementsByClassName('username')][0]
                    messageText.split('\n').forEach(line => {
                        let p = document.createElement('p')
                        p.classList.add('card-text')
                        p.classList.add('line')
                        p.classList.add('mb-0')
                        p.innerText = line
                        insertAfter(p, lastPostElement)
                        lastPostElement = p
                    })
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
}


function deletePost(postId) {
    fetch(`/sheetpost-v2/api/delete-post?` + new URLSearchParams({
        username: getCookie('username'), password: getCookie('password'), post_id: postId
    }).toString())
        .then(response => response.json())
        .then(data => {
            if ('success' in data && data['success']) {
                document.getElementById(`post${postId}`).remove()
                if (document.querySelector('.posts').children.length === 0) {
                    document.location.reload()
                }
            }
        })
}


function setSheetListeners() {
    [...document.getElementsByClassName('sheet')].forEach(sheet => {
        sheet.addEventListener('click', () => {
            clickOnSheet(sheet)
        })
    })
}


function setEditListeners() {
    const editPostModal = document.getElementById('editPostModal');
    editPostModal.addEventListener('show.bs.modal', event => {
        const postId = event.relatedTarget.getAttribute('data-bs-whatever').replace('post', '')
        editPostModal.querySelector('#editPostMessageText').value =
            [...document.getElementById(`post${postId}`).getElementsByClassName('card-text mb-0')]
                .map(line => {
                    return line.innerText
                }).join('\n')
        document.getElementById('editPostUpdate').addEventListener('click', () => {
            updatePost(postId)
            document.getElementById('editPostCancel').click()
        })
    })
}


function setDeleteListeners() {
    const deletePostModal = document.getElementById('deletePostModal');
    deletePostModal.addEventListener('show.bs.modal', event => {
        const postId = event.relatedTarget.getAttribute('data-bs-whatever').replace('post', '')
        document.getElementById('deletePostAccept').addEventListener('click', () => {
            deletePost(postId)
            document.getElementById('deletePostCancel').click()
        })
    })
}


document.getElementById('newPostModal').addEventListener('show.bs.modal', () => {
    document.getElementById('newPostMessageTextValidationLabel').innerText = ''
    document.getElementById('newPostFormValidationLabel').hidden = true
})


document.getElementById('newPostCreate').addEventListener('click', () => {
    const messageText = document.getElementById('newPostMessageText').value.trim()
    const messageTextValidationLabel = document.getElementById('newPostMessageTextValidationLabel')
    const formValidationLabel = document.getElementById('newPostFormValidationLabel')

    if (!messageText) {
        messageTextValidationLabel.innerText = 'Message is empty'
    } else if (messageText.length > 4096) {
        messageTextValidationLabel.innerText = `Maximum message length is 4096 characters (${messageText.length - 4096} characters exceeded)`
    } else {
        messageTextValidationLabel.innerText = ''
    }
    formValidationLabel.hidden = true

    if (messageText && messageText.length <= 4096) {
        fetch('/sheetpost-v2/api/create-new-post?' + new URLSearchParams({
            username: getCookie('username'), password: getCookie('password'), message: messageText
        }).toString())
            .then(response => response.json())
            .then(data => {
                if ('success' in data && data['success']) {
                    document.location.reload()
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
setEditListeners()
setDeleteListeners()