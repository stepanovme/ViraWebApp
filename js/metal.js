const btnAdd = document.getElementById('btnAdd')
const modal = document.getElementById('modal')
const btnModalCancel = document.getElementById('btnModalCancel')

btnAdd.addEventListener('click', function(){
    modal.style.display = 'flex'
})

btnModalCancel.addEventListener('click', function(){
    modal.style.display = 'none'
})