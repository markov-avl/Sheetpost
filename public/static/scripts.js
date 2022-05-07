const exampleModal = document.getElementById('newPostModal');


function setSheetCount(sheet) {
    let postId = sheet.id.replace('post', '')
    fetch(`/sheetpost/api/get-post-sheet-count/?post_id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data['success']) {
                sheet.getElementsByTagName('span')[0].innerText = data['sheet_count'] > 0 ? data['sheet_count'] : ''
            }
        })
}


function clickOnSheet(sheet) {
    let img = sheet.getElementsByTagName('img')[0]
    let postId = sheet.id.replace('post', '')
    let username = 'andrewsha'
    let password = '1234'
    let apiRequest = img.style.opacity === '1' ? 'unsheet-post' : 'sheet-post'
    let opacity = img.style.opacity === '1' ? '.5' : '1'
    fetch(`/sheetpost/api/${apiRequest}/?username=${username}&password=${password}&post_id=${postId}`)
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


exampleModal.addEventListener('show.bs.modal', function (event) {
    // Button that triggered the modal
    var button = event.relatedTarget
    // Extract info from data-bs-* attributes
    var recipient = button.getAttribute('data-bs-whatever')
    // If necessary, you could initiate an AJAX request here
    // and then do the updating in a callback.
    //
    // Update the modal's content.
    var modalBodyInput = exampleModal.querySelector('.modal-body input')

    modalBodyInput.value = recipient
})


setSheetListeners()