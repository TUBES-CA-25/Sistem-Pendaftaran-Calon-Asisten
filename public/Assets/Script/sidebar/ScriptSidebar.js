let btn = document.getElementById('btn');
let sidebar = document.querySelector('.sidebar');
let user = document.querySelector('.user');

btn.onclick = function(){
    sidebar.classList.toggle('active');
    user.classList.add('active');
    user.classList.toggle('active');
};


