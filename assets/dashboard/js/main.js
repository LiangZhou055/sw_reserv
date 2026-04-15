    "use strict";
    let elem = document.documentElement;
    const sideBar = document.getElementById('sideContent');
    const mainContent = document.getElementById('mainContent');
    const menu_icon = document.getElementById('menu_icon');
    const hideBarIcon = document.getElementById('hideBarIcon');
    const searchBar = document.getElementById('searchBar');

    const side_bar_twenty_list = document.querySelector('.side_bar_twenty_list');
    const side_bar_eight_list = document.querySelector('.side_bar_eight_list');
    const side_bar_nine_list = document.querySelector('.side_bar_nine_list');  
    const side_bar_ten_list = document.querySelector('.side_bar_ten_list');  

    const first_twenty_child = document.querySelector('.first_twenty_child');
    const first_eight_child = document.querySelector('.first_eight_child');
    const first_nine_child = document.querySelector('.first_nine_child');
    const first_ten_child = document.querySelector('.first_ten_child');

    const icon1 = document.querySelector('.icon1')
    const icon9 = document.querySelector('.icon9')
    const icon8 = document.querySelector('.icon8')
    const icon10 = document.querySelector('.icon10')
    const icon20 = document.querySelector('.icon20')

    const notification_top = document.getElementsByClassName('notification_top');
    const openFullScreen = document.getElementById('openFullScreen');
    const closeFullScreen = document.getElementById('closeFullScreen');
    let social_count = document.querySelectorAll('.social_count');
    let currency = document.querySelectorAll('.currency');
    let time = 300;

    side_bar_twenty_list.addEventListener('click',()=>{
        first_twenty_child.classList.toggle('first_twenty_menu');
        icon20.classList.toggle('rotate180');
    })
    side_bar_nine_list.addEventListener('click',()=>{
        first_nine_child.classList.toggle('first_nine_menu')
        icon9.classList.toggle('rotate180');
    })

    side_bar_eight_list.addEventListener('click',()=>{
        first_eight_child.classList.toggle('first_eight_menu')
        icon8.classList.toggle('rotate180');
    })
     side_bar_ten_list.addEventListener('click',()=>{
        first_ten_child.classList.toggle('first_ten_menu')
        icon10.classList.toggle('rotate180');
    })

    for (let i = 0; i < notification_top.length; i++) {
        notification_top[i].addEventListener('click',(e)=>{
            alert('This is notification')
        })
    }

    const openFull = () => {
        openFullScreen.style.display = 'none';
        closeFullScreen.style.display = 'block';
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    }

    const closeFull = () => {
        closeFullScreen.style.display = 'none';
        openFullScreen.style.display = 'block';
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }

    const showSideBar = () => {
        sideBar.classList.toggle('marginLeft');
        mainContent.classList.toggle('added');
    }

    const complete = () => alert('Task completed successfully');
    const pending = () => alert('Task is pending now...');
    const showSearchBar = () => searchBar.style.display= 'block';
    const closeSearchBar = () => searchBar.style.display= 'none';

    social_count.forEach(eachSocial => {
        let updateSocial = () =>{
            let target1 = +eachSocial.getAttribute('data-target');
            let count1 = +eachSocial.innerText;
            let increment = target1/time;
            if (count1 < target1) {
                eachSocial.innerText = Math.ceil(count1 + increment);
                setTimeout(updateSocial,100)
            }else{
                eachSocial.innerText = target1
            }
        }
        updateSocial()
    })
    currency.forEach(eachCurrency => {
        let updateCurrency = () =>{
            let target = +eachCurrency.getAttribute('data-target');
            let count = +eachCurrency.innerText;
            let increment = target/time;
            if (count < target) {
                eachCurrency.innerText = Math.ceil(count + increment);
                setTimeout(updateCurrency,1)
            }else{
                eachCurrency.innerText = target
            }
        }
        updateCurrency()
    })

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
 