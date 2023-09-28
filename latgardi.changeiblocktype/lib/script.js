function changeType(type) {
    const form = document.getElementById('frm')
    const urlParams = new URLSearchParams(window.location.search)
    urlParams.set('type', type)
    const url = new URL(document.location.href);
    url.search = urlParams.toString()
    form.action = url.toString()
    // can just be used 'form.submit()' with redirect to iBlock type list
    form.querySelector('input[name=apply]').click()
}


