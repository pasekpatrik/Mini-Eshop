const panelNameDay = document.querySelector('.panel-nameday')
let div = document.createElement('div')

const getData = async () => {
    try {
        const response = await fetch('https://svatkyapi.cz/api/day')
        const data = await response.json()

        div.textContent = `
            Dnes má svátek ${data.name} | ${data.dayNumber}. ${data.monthNumber}. ${data.year}
        `
        panelNameDay.appendChild(div)
    } catch(e) {
        console.log(e)
    }
}

getData()