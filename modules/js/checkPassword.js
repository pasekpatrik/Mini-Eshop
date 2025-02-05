const form = document.querySelector('.check-password')
const passwords = document.querySelector('#passwords')
let span = document.createElement('span')

form.addEventListener('submit', (e) => {
    let password = form.querySelector('[name=password]')
    let passwordOk = form.querySelector('[name=password-ok]')
    let error = false

    if (password.value === '' || passwordOk.value === '') {
        span.textContent = 'Heslo je povinné.'
        error = true
    } else if (password.value !== passwordOk. value) {
        span.textContent = 'Hesla se musí schodovat.'
        error = true
    } else if (password.value.length <= 8){
        span.textContent = 'Heslo musí být delší než osm znaků.'
        error = true
    }

    if (error) {
        e.preventDefault()

        password.classList.add('border')
        passwordOk.classList.add('border')
        passwords.appendChild(span)
    }
})